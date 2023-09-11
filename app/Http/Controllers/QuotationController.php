<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;

class QuotationController extends Controller
{
    public function store(Request $request)
    {
//        return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Quotation = Quotation::create([
            'quotation_no' => 'QUT-' . rand(100000, 999999),
            'reference_no' => $request->reference_no,
            'address' => $request->address,
            'currency_id' => $request->currency_id,
            'email' => $request->email,
            'name' => $request->name,
            'customer_notes' => $request->notes,
            'phone_no' => $request->phone_no,
            'sub_total' => $request->sub_total,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
//            'vat_percentage' => $request->vat_percentage,
//            'vat_amount' => $request->vat_amount,
            'total' => $request->total,
            'requested_by' => $request->requested_by,
            'terms_and_conditions' => $request->terms_and_conditions,
            'created_by' => Auth::id(),
        ]);

        $total_amount = 0;
        $quotation_id = $Quotation->id;

        foreach ($request->amount as $index => $amount) {
            if ($amount) {

                $total_amount += $amount;

                $QuotationDetail = QuotationDetail::create([
                    'quotation_id' => $quotation_id,
                    'quantity' => $request->quantity[$index],
                    'unit_price' => $request->unit_price[$index],
                    'vat' => $request->tax[$index],
                    'vat_amount' => $request->vat_amount[$index] ?? 0,
                    'description' => $request->description[$index],
                    'amount' => $amount,
                ]);
            }
        }

//        ATTACHMENT SAVE
        $attachment = $request->file('attachment');
        if ($attachment) {
            foreach ($attachment as $index => $file) {
                $type = $file->getClientOriginalExtension();
                $file_name = Str::random(10) . '.' . $type;
                $file->move(public_path('uploads/quotation/attachment'), $file_name);
                Attachment::create([
                    'name' => $request->attachment_name[$index],
                    'file_name' => $file_name,
                    'type' => $type,
                    'object' => 'Quotation',
                    'object_id' => $quotation_id
                ]);
            }
        }


//        DISCOUNT & TAX CALCULATE
//        if ($request->vat_percentage || $request->discount_percentage) {
//
////            VAT CALCULATE
//            $vat_amount = ($total_amount / 100) * $request->vat_percentage;
//
////            DISCOUNT CALCULATE
//            $discount_amount = ($total_amount / 100) * $request->discount_percentage;
//
//
//            $Quotation = Quotation::find($quotation_id);
//            $Quotation->sub_total = $total_amount;
//            $Quotation->discount_percentage = $request->discount_percentage;
//            $Quotation->discount_amount = $discount_amount;
//            $Quotation->vat_percentage = $request->vat_percentage;
//            $Quotation->vat_amount = $vat_amount;
//            $Quotation->total = $total_amount + $vat_amount - $discount_amount;
//            $Quotation->save();
//        }

        return response([
            'status' => true,
            'message' => 'Quotation Insert Successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Quotation::with(['quotation_details', 'attachment', 'currency', 'get_created_by'])->latest()->get()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Quotation::with(['quotation_details', 'attachment'])->find($request->id)
        ]);
    }

    public function update(Request $request)
    {

//        return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Quotation = Quotation::with('quotation_details')->find($request->quotation_id);

        $Quotation->name = $request->name;
        $Quotation->email = $request->email;
        $Quotation->customer_notes = $request->notes;
        $Quotation->phone_no = $request->phone_no;
        $Quotation->address = $request->address;
        $Quotation->reference_no = $request->reference_no;
        $Quotation->sub_total = $request->sub_total;
        $Quotation->discount_percentage = $request->discount_percentage;
        $Quotation->discount_amount = $request->input('discount_amount', 0);
//        $Quotation->vat_percentage = $request->vat_percentage;
//        $Quotation->vat_amount = $request->vat_amount;
        $Quotation->total = $request->total;
        $Quotation->requested_by = $request->requested_by;
        $Quotation->terms_and_conditions = $request->terms_and_conditions;

        if (isset($request->is_revised)) {
            $Quotation->is_revised = 1;
        }

//        $total_revised = $Quotation->total_revised;

        $Quotation->total_revised = $request->total_revised + 1;

        $Quotation->save();

        $total_amount = 0;

        foreach ($request->amount as $key => $amount) {
            if ($amount) {
                $total_amount += $amount;
                if (isset($request->quotation_detail_id[$key])) {
                    $QuotationDetail = QuotationDetail::where(['id' => $request->quotation_detail_id[$key], 'quotation_id' => $request->quotation_id])->first();
                    $QuotationDetail->quantity = $request->quantity[$key];
                    $QuotationDetail->unit_price = $request->unit_price[$key];
                    $QuotationDetail->description = $request->description[$key];
                    $QuotationDetail->amount = $request->amount[$key];
                    $QuotationDetail->vat = $request->tax[$key];
                    $QuotationDetail->vat_amount = $request->vat_amount[$key] ?? 0;

                    $QuotationDetail->save();
                } else {
                    $QuotationDetail = QuotationDetail::create([
                        'quotation_id' => $request->quotation_id,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'vat' => $request->tax[$key],
                        'vat_amount' => $request->vat_amount[$key] ?? 0,
                        'description' => $request->description[$key],
                        'amount' => $request->amount[$key],
                    ]);
                }
            }
        }

        $attachment = $request->file('attachment');
        if ($attachment) {
            foreach ($attachment as $index => $file) {
                $type = $file->getClientOriginalExtension();
                $file_name = Str::random(10) . '.' . $type;
                $file->move(public_path('uploads/quotation/attachment'), $file_name);

                if (isset($request->attachment_id[$index])) {
                    $Attachment = Attachment::where('id', $request->attachment_id[$index])->where('object', 'Quotation')->first();
                    $Attachment->name = $request->attachment_name[$index];
                    $Attachment->file_name = $file_name;
                    $Attachment->type = $type;
                    $Attachment->save();
                } else {
                    Attachment::create([
                        'name' => $request->attachment_name[$index],
                        'file_name' => $file_name,
                        'type' => $type,
                        'object' => 'Quotation',
                        'object_id' => $request->quotation_id
                    ]);
                }
            }
        }

//        TAX CALCULATE
//        if ($request->vat_percentage) {
//            $vat_amount = ($total_amount / 100) * $request->vat_percentage;
//
//            $Quotation->sub_total = $total_amount;
//            $Quotation->vat_percentage = $request->vat_percentage;
//            $Quotation->vat_amount = $vat_amount;
//            $Quotation->total = $total_amount + $vat_amount;
//        }
//        $Quotation->save();

        return response()->json([
            'status' => true,
            'message' => 'Quotation Update Successfully'
        ]);
    }

    public function quotationDetailDestroy(Request $request)
    {
        $QuotationiDetail = QuotationDetail::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Quotation = Quotation::where(['id' => $request->id])->delete();
        $QuotationDetail = QuotationDetail::where(['quotation_id' => $request->id])->get();

        foreach ($QuotationDetail as $Detail) {
            $Detail->where(['quotation_id' => $request->id])->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function sendEmail(Request $request)
    {
        $Quotation = Quotation::with(['quotation_details', 'currency'])->find($request->id);
        $file_name = 'QUT-INV-' . $request->id . '.pdf';

        if (File::exists(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name)) {
            unlink(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);
        }

        PDF::loadView('invoice.quotaion-invoice', ['data' => $Quotation], [], [
            'title' => 'Quotation',
            'margin_top' => 10
        ])->save(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);

//        $invoiceMailData = [
//            'to_name' => $Quotation->name,
//            'to_email' => $Quotation->email,
//            'subject' => 'Work Invoice by SUMSOLS Corporation',
//            'file_name' => $file_name,
//            'file_path' => public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name,
//            'invoice_public_url' => env('BASE_URL') . 'invoices/' . $request->id . '/public-view'
//        ];
//        Notification::route('mail', $invoiceMailData['to_email'])
//            ->notify(new InvoiceNotification($invoiceMailData));

        $file_path = env('BASE_URL') . 'public/uploads/invoice_pdf/quotation_invoice/' . $file_name . '?v=' . date('ymdhis');

        return response()->json([
            'status' => true,
            'message' => 'Quotation send Successfully',
            'file_path' => $file_path
        ]);
    }

    public function attachmentDestroy(Request $request)
    {
        Attachment::where('id', $request->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Remove Successfully'
        ]);
    }

    public function previousClients()
    {
        $Clients = Quotation::get()->unique('name');

        return response()->json([
            'status' => true,
            'data' => $Clients
        ]);
    }
}

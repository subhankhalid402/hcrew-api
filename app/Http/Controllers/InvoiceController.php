<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;

class InvoiceController extends Controller
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

        $Invoice = Invoice::create([
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
            'trn' => $request->trn,
            'requested_by' => $request->requested_by,
            'terms_and_conditions' => $request->terms_and_conditions,
            'created_by' => Auth::id(),
        ]);

        $total_amount = 0;
        $quotation_id = $Invoice->id;

        foreach ($request->amount as $index => $amount) {
            if ($amount) {

                $total_amount += $amount;

                $InvoiceDetail = InvoiceDetail::create([
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
                    'object' => 'Invoice',
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
//            $Invoice = Invoice::find($quotation_id);
//            $Invoice->sub_total = $total_amount;
//            $Invoice->discount_percentage = $request->discount_percentage;
//            $Invoice->discount_amount = $discount_amount;
//            $Invoice->vat_percentage = $request->vat_percentage;
//            $Invoice->vat_amount = $vat_amount;
//            $Invoice->total = $total_amount + $vat_amount - $discount_amount;
//            $Invoice->save();
//        }

        return response([
            'status' => true,
            'message' => 'Invoice Insert Successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Invoice::with(['quotation_details', 'attachment', 'currency', 'get_created_by'])->latest()->get()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Invoice::with(['quotation_details', 'attachment'])->find($request->id)
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

        $Invoice = Invoice::with('quotation_details')->find($request->quotation_id);

        $Invoice->name = $request->name;
        $Invoice->email = $request->email;
        $Invoice->customer_notes = $request->notes;
        $Invoice->phone_no = $request->phone_no;
        $Invoice->address = $request->address;
        $Invoice->reference_no = $request->reference_no;
        $Invoice->sub_total = $request->sub_total;
        $Invoice->discount_percentage = $request->discount_percentage;
        $Invoice->discount_amount = $request->input('discount_amount', 0);
//        $Invoice->vat_percentage = $request->vat_percentage;
//        $Invoice->vat_amount = $request->vat_amount;
        $Invoice->total = $request->total;
        $Invoice->requested_by = $request->requested_by;
        $Invoice->trn = $request->trn;
        $Invoice->terms_and_conditions = $request->terms_and_conditions;

        if (isset($request->is_revised)) {
            $Invoice->is_revised = 1;
        }

//        $total_revised = $Invoice->total_revised;

        $Invoice->total_revised = $request->total_revised + 1;

        $Invoice->save();

        $total_amount = 0;

        foreach ($request->amount as $key => $amount) {
            if ($amount) {
                $total_amount += $amount;
                if (isset($request->quotation_detail_id[$key])) {
                    $InvoiceDetail = InvoiceDetail::where(['id' => $request->quotation_detail_id[$key], 'quotation_id' => $request->quotation_id])->first();
                    $InvoiceDetail->quantity = $request->quantity[$key];
                    $InvoiceDetail->unit_price = $request->unit_price[$key];
                    $InvoiceDetail->description = $request->description[$key];
                    $InvoiceDetail->amount = $request->amount[$key];
                    $InvoiceDetail->vat = $request->tax[$key];
                    $InvoiceDetail->vat_amount = $request->vat_amount[$key] ?? 0;

                    $InvoiceDetail->save();
                } else {
                    $InvoiceDetail = InvoiceDetail::create([
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
                    $Attachment = Attachment::where('id', $request->attachment_id[$index])->where('object', 'Invoice')->first();
                    $Attachment->name = $request->attachment_name[$index];
                    $Attachment->file_name = $file_name;
                    $Attachment->type = $type;
                    $Attachment->save();
                } else {
                    Attachment::create([
                        'name' => $request->attachment_name[$index],
                        'file_name' => $file_name,
                        'type' => $type,
                        'object' => 'Invoice',
                        'object_id' => $request->quotation_id
                    ]);
                }
            }
        }

//        TAX CALCULATE
//        if ($request->vat_percentage) {
//            $vat_amount = ($total_amount / 100) * $request->vat_percentage;
//
//            $Invoice->sub_total = $total_amount;
//            $Invoice->vat_percentage = $request->vat_percentage;
//            $Invoice->vat_amount = $vat_amount;
//            $Invoice->total = $total_amount + $vat_amount;
//        }
//        $Invoice->save();

        return response()->json([
            'status' => true,
            'message' => 'Invoice Update Successfully'
        ]);
    }

    public function quotationDetailDestroy(Request $request)
    {
        $InvoiceiDetail = InvoiceDetail::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Invoice = Invoice::where(['id' => $request->id])->delete();
        $InvoiceDetail = InvoiceDetail::where(['quotation_id' => $request->id])->get();

        foreach ($InvoiceDetail as $Detail) {
            $Detail->where(['quotation_id' => $request->id])->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function sendEmail(Request $request)
    {
        $Invoice = Invoice::with(['quotation_details', 'currency'])->find($request->id);
        $file_name = 'QUT-INV-' . $request->id . '.pdf';

        if (File::exists(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name)) {
            unlink(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);
        }

        PDF::loadView('invoice.quotaion-invoice', ['data' => $Invoice], [], [
            'title' => 'Invoice',
            'margin_top' => 10
        ])->save(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);

//        $invoiceMailData = [
//            'to_name' => $Invoice->name,
//            'to_email' => $Invoice->email,
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
            'message' => 'Invoice send Successfully',
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
        $Clients = Invoice::get()->unique('name');

        return response()->json([
            'status' => true,
            'data' => $Clients
        ]);
    }
}

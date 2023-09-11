<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Attachment;
use App\Models\ClientCategory;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Notifications\InvoiceNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PDF;

class QuotationController extends Controller
{
    public function store(Request $request)
    {
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
            'quotation_no' => rand(100000, 999999),
            'reference_no' => $request->reference_no,
            'address' => $request->address,
            'currency_id' => $request->currency_id,
            'email' => $request->email,
            'name' => $request->name,
            'customer_notes' => $request->notes,
            'phone_no' => $request->phone_no,
        ]);

        $total_amount = 0;
        $quotation_id = $Quotation->id;


        foreach ($request->amount as $index => $amount) {
            if ($amount) {

                $total_amount += $amount;

                $QuotationDetail = QuotationDetail::create([
                    'quotation_id' => $quotation_id,
                    'from_date' => SiteHelper::reformatDbDate($request->from_date[$index]),
                    'to_date' => SiteHelper::reformatDbDate($request->to_date[$index]),
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


//        TAX CALCULATE
        if ($request->vat_percentage) {
            $vat_amount = ($total_amount / 100) * $request->vat_percentage;

            $Quotation = Quotation::find($quotation_id);
            $Quotation->sub_total = $total_amount;
            $Quotation->vat_percentage = $request->vat_percentage;
            $Quotation->vat_amount = $vat_amount;
            $Quotation->total = $total_amount + $vat_amount;
            $Quotation->save();
        }

        return response([
            'status' => true,
            'message' => 'Quotation Insert Successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Quotation::with(['quotation_details', 'attachment'])->latest()->get()
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

        if (isset($request->is_revised)) {
            $Quotation->is_revised = 1;
        }

//        $Quotation->save();

        $total_amount = 0;

        foreach ($request->amount as $key => $amount) {
            if ($amount) {
                $total_amount += $amount;
                if (isset($request->quotation_detail_id[$key])) {
                    $QuotationDetail = QuotationDetail::where(['id' => $request->quotation_detail_id[$key], 'quotation_id' => $request->quotation_id])->first();
                    $QuotationDetail->from_date = $request->from_date[$key];
                    $QuotationDetail->to_date = $request->to_date[$key];
                    $QuotationDetail->description = $request->description[$key];
                    $QuotationDetail->amount = $request->amount[$key];

                    $QuotationDetail->save();
                } else {
                    $QuotationDetail = QuotationDetail::create([
                        'quotation_id' => $request->quotation_id,
                        'from_date' => SiteHelper::reformatDbDate($request->from_date[$key]),
                        'to_date' => SiteHelper::reformatDbDate($request->to_date[$key]),
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
        if ($request->vat_percentage) {
            $vat_amount = ($total_amount / 100) * $request->vat_percentage;

            $Quotation->sub_total = $total_amount;
            $Quotation->vat_percentage = $request->vat_percentage;
            $Quotation->vat_amount = $vat_amount;
            $Quotation->total = $total_amount + $vat_amount;
        }
        $Quotation->save();

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
        $Quotation = Quotation::with('quotation_details')->find($request->id);
        $file_name = 'QUT-INV-' . $request->id . '.pdf';

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

        $file_path = env('BASE_URL') . 'public/uploads/invoice_pdf/quotation_invoice/' . $file_name;

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
}

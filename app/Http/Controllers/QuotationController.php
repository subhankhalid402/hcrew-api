<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\ClientCategory;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Notifications\InvoiceNotification;
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

        $quotatioin_id = $Quotation->id;
        foreach ($request->quotation_detail as $QuotationDetail) {
            if ($QuotationDetail['amount']) {
                $QuotationDetail = QuotationDetail::create([
                    'quotation_id' => $quotatioin_id,
                    'from_date' => SiteHelper::reformatDbDate($QuotationDetail['from_date']),
                    'to_date' => SiteHelper::reformatDbDate($QuotationDetail['to_date']),
                    'description' => $QuotationDetail['description'],
                    'amount' => $QuotationDetail['amount'],
                ]);
            }
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
            'data' => Quotation::with('quotation_details')->get()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Quotation::with('quotation_details')->find($request->id)
        ]);
    }

    public function update(Request $request)
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

        $Quotation = Quotation::with('quotation_details')->find($request->quotation_id);

        $Quotation->name = $request->name;
        $Quotation->email = $request->email;
        $Quotation->customer_notes = $request->notes;
        $Quotation->phone_no = $request->phone_no;
        $Quotation->address = $request->address;
        $Quotation->reference_no = $request->reference_no;

        $Quotation->save();

        foreach ($request->quotation_detail as $quotationDetail) {
            if ($quotationDetail['amount']) {
                if ($quotationDetail['quotation_detail_id']) {
                    $QuotationDetail = QuotationDetail::where(['id' => $quotationDetail['quotation_detail_id'], 'quotation_id' => $request->quotation_id])->first();
                    $QuotationDetail->from_date = $quotationDetail['from_date'];
                    $QuotationDetail->to_date = $quotationDetail['to_date'];
                    $QuotationDetail->description = $quotationDetail['description'];
                    $QuotationDetail->amount = $quotationDetail['amount'];

                    $QuotationDetail->save();
                } else {
                    $QuotationDetail = QuotationDetail::create([
                        'quotation_id' => $request->quotation_id,
                        'from_date' => SiteHelper::reformatDbDate($quotationDetail['from_date']),
                        'to_date' => SiteHelper::reformatDbDate($quotationDetail['to_date']),
                        'description' => $quotationDetail['description'],
                        'amount' => $quotationDetail['amount'],
                    ]);
                }
            }
        }

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
        $sub_total = 0;
        foreach ($Quotation->quotation_details as $QuotationDetail) {
            $sub_total += $QuotationDetail['amount'];
        }

        $Quotation->sub_total = $sub_total;
        $file_name = 'QUT-INV-' . $request->id . '.pdf';

        PDF::loadView('invoice.quotaion-invoice', ['data' => $Quotation], [], [
            'title' => 'Quotation',
            'margin_top' => 10
        ])->save(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);

        $invoiceMailData = [
            'to_name' => $Quotation->name,
            'to_email' => $Quotation->email,
            'subject' => 'Work Invoice by SUMSOLS Corporation',
            'file_name' => $file_name,
            'file_path' => public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name,
            'invoice_public_url' => env('BASE_URL') . 'invoices/' . $request->id . '/public-view'
        ];
        Notification::route('mail', $invoiceMailData['to_email'])
            ->notify(new InvoiceNotification($invoiceMailData));

        return response()->json([
            'status' => true,
            'message' => 'Quotation send Successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if ($request->querystring == 'quotation') {
            $this->quotationCreate($request);
        } elseif ($request->querystring == 'invoice') {
            $this->invoiceCreate($request);
        }

        return response([
            'status' => true,
            'message' => ucfirst($request->querystring) . ' Insert Successfully'
        ]);
    }

    public function all(Request $request)
    {
        $data = '';
        if ($request->querystring == 'quotation') {
            $data = Quotation::with(['details', 'attachment', 'currency', 'get_created_by'])->latest()->get();
        } else if ($request->querystring == 'invoice') {
            $data = Invoice::with(['details', 'attachment', 'currency', 'get_created_by'])->latest()->get();
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);

    }

    public function show(Request $request)
    {
        $data = '';
        if ($request->querystring == 'quotation') {
            $data = Quotation::with(['details', 'attachment'])->find($request->id);
        } else if ($request->querystring == 'invoice') {
            $data = Invoice::with(['details', 'attachment', 'currency', 'get_created_by'])->find($request->id);
        }

        return response()->json([
            'status' => true,
            'data' => $data
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

        if ($request->querystring == 'quotation') {
            $this->updateQuotation($request);
        } elseif ($request->querystring == 'invoice') {
            $this->updateInvoice($request);
        }


        return response()->json([
            'status' => true,
            'message' => ucfirst($request->querystring) . ' Update Successfully'
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
        if ($request->querystring == 'quotation') {
            $file_path = $this->readyQuotationForMail($request);
        } elseif ($request->querystring == 'invoice') {
            $file_path = $this->readyInvoiceForMail($request);
        }


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

    public function previousClients(Request $request)
    {
        $Clients = '';
        if ($request->querystring == 'quotation') {
            $Clients = Quotation::get()->unique('name');
        } else {
            $Clients = Invoice::get()->unique('name');
        }

        return response()->json([
            'status' => true,
            'data' => $Clients
        ]);
    }

    private function quotationCreate($request)
    {
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
            'vat_amount' => $request->total_vat_amount,
            'total' => $request->total,
            'trn' => $request->trn,
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

    }

    private function invoiceCreate($request)
    {
        $Invoice = Invoice::create([
            'invoice_no' => 'INV-' . rand(100000, 999999),
            'reference_no' => $request->reference_no,
            'quotation_id' => $request->input('quotation_id', null),
            'address' => $request->address,
            'po_no' => $request->po_no,
            'currency_id' => $request->currency_id,
            'email' => $request->email,
            'name' => $request->name,
            'customer_notes' => $request->notes,
            'phone_no' => $request->phone_no,
            'sub_total' => $request->sub_total,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
//            'vat_percentage' => $request->vat_percentage,
            'vat_amount' => $request->total_vat_amount,
            'total' => $request->total,
            'trn' => $request->trn,
            'requested_by' => $request->requested_by,
            'terms_and_conditions' => $request->terms_and_conditions,
            'created_by' => Auth::id(),
        ]);

        $total_amount = 0;
        $invoice_id = $Invoice->id;

        foreach ($request->amount as $index => $amount) {
            if ($amount) {

                $total_amount += $amount;

                $InvoiceDetail = InvoiceDetail::create([
                    'invoice_id' => $invoice_id,
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
                $file->move(public_path('uploads/invoice/attachment'), $file_name);
                Attachment::create([
                    'name' => $request->attachment_name[$index],
                    'file_name' => $file_name,
                    'type' => $type,
                    'object' => 'Invoice',
                    'object_id' => $invoice_id
                ]);
            }
        }

    }

    private function updateQuotation($request)
    {
        $Quotation = Quotation::with('details')->find($request->id);

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
        $Quotation->trn = $request->trn;
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
                if (isset($request->detail_id[$key])) {
                    $QuotationDetail = QuotationDetail::where(['id' => $request->detail_id[$key], 'quotation_id' => $request->id])->first();
                    $QuotationDetail->quantity = $request->quantity[$key];
                    $QuotationDetail->unit_price = $request->unit_price[$key];
                    $QuotationDetail->description = $request->description[$key];
                    $QuotationDetail->amount = $request->amount[$key];
                    $QuotationDetail->vat = $request->tax[$key];
                    $QuotationDetail->vat_amount = $request->vat_amount[$key] ?? 0;

                    $QuotationDetail->save();
                } else {
                    $QuotationDetail = QuotationDetail::create([
                        'quotation_id' => $request->id,
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
                        'object_id' => $request->id
                    ]);
                }
            }
        }
    }


    private function updateInvoice($request)
    {
        $Invoice = Invoice::with('details')->find($request->id);

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
                if (isset($request->detail_id[$key])) {
                    $InvoiceDetail = InvoiceDetail::where(['id' => $request->detail_id[$key], 'invoice_id' => $request->id])->first();
                    $InvoiceDetail->quantity = $request->quantity[$key];
                    $InvoiceDetail->unit_price = $request->unit_price[$key];
                    $InvoiceDetail->description = $request->description[$key];
                    $InvoiceDetail->amount = $request->amount[$key];
                    $InvoiceDetail->vat = $request->tax[$key];
                    $InvoiceDetail->vat_amount = $request->vat_amount[$key] ?? 0;

                    $InvoiceDetail->save();
                } else {
                    $InvoiceDetail = InvoiceDetail::create([
                        'invoice_id' => $request->id,
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
                $file->move(public_path('uploads/invoice/attachment'), $file_name);

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
                        'object_id' => $request->id
                    ]);
                }
            }
        }
    }

    public function readyQuotationForMail($request)
    {
        $Quotation = Quotation::with(['details', 'currency'])->find($request->id);
        $file_name = 'QUT-' . $request->id . '.pdf';

        if (File::exists(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name)) {
            unlink(public_path() . '/uploads/invoice_pdf/quotation_invoice/' . $file_name);
        }

        PDF::loadView('invoice.quotaion-invoice', ['data' => $Quotation, 'is_invoice' => 'NO'], [], [
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

        return $file_path;
    }

    public function readyInvoiceForMail($request)
    {
        $Invoice = Invoice::with(['details', 'currency'])->find($request->id);
        $file_name = 'INV-' . $request->id . '.pdf';

        if (File::exists(public_path() . '/uploads/invoice_pdf/invoice/' . $file_name)) {
            unlink(public_path() . '/uploads/invoice_pdf/invoice/' . $file_name);
        }

        PDF::loadView('invoice.quotaion-invoice', ['data' => $Invoice, 'is_invoice' => 'YES'], [], [
            'title' => 'Invoice',
            'margin_top' => 10
        ])->save(public_path() . '/uploads/invoice_pdf/invoice/' . $file_name);

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

        $file_path = env('BASE_URL') . 'public/uploads/invoice_pdf/invoice/' . $file_name . '?v=' . date('ymdhis');

        return $file_path;
    }

    public function convertToInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // Find the Quotation by its ID along with its details
        $quotation = Quotation::with(['details'])->find($request->id);

        // Check if the Quotation was found
        if ($quotation) {
            $Invoice = Invoice::create([
                'invoice_no' => 'INV-' . rand(100000, 999999),
                'reference_no' => $quotation->reference_no,
                'quotation_id' => $quotation->id,
                'address' => $quotation->address,
                'po_no' => $quotation->po_no,
                'currency_id' => $quotation->currency_id,
                'email' => $quotation->email,
                'name' => $quotation->name,
                'customer_notes' => $quotation->customer_notes,
                'phone_no' => $quotation->phone_no,
                'sub_total' => $quotation->sub_total,
                'discount_percentage' => $quotation->discount_percentage,
                'discount_amount' => $quotation->discount_amount,
//            'vat_percentage' => $quotation->vat_percentage,
                'vat_amount' => $quotation->vat_amount,
                'total' => $quotation->total,
                'trn' => $quotation->trn,
                'requested_by' => $quotation->requested_by,
                'terms_and_conditions' => $quotation->terms_and_conditions,
                'created_by' => Auth::id(),
            ]);

            // create InvoiceDetails for this Invoice
            foreach ($quotation->details as $quotationDetail) {
                InvoiceDetail::create([
                    'invoice_id' => $Invoice->id,
                    'quantity' => $quotationDetail->quantity,
                    'unit_price' => $quotationDetail->unit_price,
                    'vat' => $quotationDetail->vat,
                    'vat_amount' => $quotationDetail->vat_amount ?? 0,
                    'description' => $quotationDetail->description,
                    'amount' => $quotationDetail->amount,
                ]);
            }

            $quotation->status = 'invoiced';
            $quotation->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Convert Successfully'
        ]);
    }
}

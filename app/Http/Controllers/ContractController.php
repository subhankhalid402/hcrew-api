<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Payment;
use App\Notifications\InvoiceNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Job;
use App\Models\JobDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use PDF;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        $has_double_shift = 0;
        if (isset($request->has_double_shift)) {
            $has_double_shift = $request->has_double_shift;
        }


        $validator = Validator::make($request->all(), [
            'starts_at' => 'required',
            'ends_at' => 'required',
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Contract = Contract::create([
            'title' => $request->title,
            'client_id' => $request->client_id,
            'starts_at' => SiteHelper::reformatDbDate($request->starts_at),
            'ends_at' => SiteHelper::reformatDbDate($request->ends_at),
            'contract_status' => $request->contract_status,
            'notes' => $request->notes,
            'currency_id' => $request->currency_id,
        ]);

        $contract_id = $Contract->id;


        foreach ($request->job_details as $index => $detail) {

            $Job = Job::create([
                'hours_in_day' => $detail['hours_in_day'],
                'contract_id' => $contract_id,
                'employee_category_id' => $detail['employee_category_id'],
                'starts_at' => $detail['starts_at'],
                'ends_at' => $detail['ends_at'],
                'rate_per_day' => $detail['rate_per_day'],
                'has_double_shift' => $has_double_shift,
                'double_shift_starts_hours' => $detail['double_shift_starts_hours'],
                'overtime_rate_per_hour' => $detail['overtime_rate_per_hour'],
            ]);

            $job_id = $Job->id;

            foreach ($detail['employee_id'] as $key => $value) {
                $JobDetail = JobDetail::create([
                    'job_id' => $job_id,
                    'employee_id' => $value
                ]);
            }
        }


        return response()->json([
            'status' => true,
            'message' => 'Contract saved successfully'
        ]);
    }

    public function all(Request $request)
    {
        $Contract = Contract::with('jobs', 'client', 'currency')->whereIn('contract_status', explode(',', $request->status))->orderByDesc('created_at')->get();

        return response()->json([
            'status' => true,
            'data' => $Contract
        ]);
    }

    public function detail(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Contract::with('jobs', 'client', 'currency')->where('id', $request->contract_id)->get()
        ]);
    }

    public function search(Request $request)
    {
        $Contract = Contract::query();
        if ($request->name) {
            $Contract->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->name . '%');
                $q->orWhereHas('client', function ($param) use ($request) {
                    $param->where('name', 'Like', '%' . $request->name . '%');
                });
            });

        }

        $Contract->whereIn('contract_status', explode(',', $request->status));
        return response()->json([
            'status' => true,
            'data' => $Contract->with('jobs')->with('client')->with('currency')->orderBy('id', 'DESC')->get()
        ]);

    }

    public function getAttendance($payment_date, $job_detail_id)
    {
        $Payment = Payment::with('job_detail')->where('payment_date', $payment_date)->where('job_detail_id', $job_detail_id)->first();

        return $Payment;
    }

    public function checkAttendance($payment_date, $job_detail_id)
    {
        $Payment = Payment::where('job_detail_id', $job_detail_id)->whereDate('payment_date', $payment_date)->get();
        return $Payment;
    }

    public function deleteAttendance($payment_date, $job_detail_id, $end_date, $start_date)
    {
        $Payment = Payment::where('payment_date', '>', $end_date)->orwhere('payment_date', '<', $start_date)->where('job_detail_id', $job_detail_id)->delete();

        return $Payment;
    }

    public function attendanceSave(Request $request)
    {
        $Contract = Contract::with('jobs', 'client', 'currency')->firstWhere('id', $request->contract_id);
        $Periods = CarbonPeriod::create($Contract->starts_at, $Contract->ends_at);

        $Currency = $Contract->currency;
        $AttendanceArray = [];
        $total_payment = 0;
        foreach ($Contract->jobs as $Job) {
            foreach ($Job->job_details as $JobDetail) {
                foreach ($Periods as $Period) {
                    $job_payment = $Job->rate_per_day + $Job->overtime_rate_per_hour;
                    $total_payment += $job_payment;
                    $date = SiteHelper::reformatReadableDateNice($Period);
                    $payment_date = Carbon::createFromFormat('d M, Y', $date)->format('Y-m-d');

                    if (!empty($this->checkAttendance($payment_date, $JobDetail->id)->toArray())) {

                    } else {
                        Payment::create([
                            'payment_date' => $payment_date,
                            'hours_worked' => $Job->hours_in_day,
                            'job_detail_id' => $JobDetail->id,
                            'rate_per_day' => $Job->rate_per_day,
                            'overtime_hours' => $Job->double_shift_starts_hours,
                            'double_shift' => $Job->has_double_shift,
                            'overtime_hours_rate' => $Job->overtime_rate_per_hour,
                            'net_payment' => 0,
                        ]);
                    }
                    $this->deleteAttendance($payment_date, $JobDetail->id, $Contract->ends_at, $Contract->starts_at);
                    $AttendanceArray[] = $this->getAttendance($payment_date, $JobDetail->id);
                }
            }
        }

        $DateArray = [];

        foreach ($AttendanceArray as $Attendance) {
            $DateArray[SiteHelper::reformatReadableDateNice($Attendance->payment_date)][] = $Attendance;
        }


        return response()->json([
            'status' => true,
            'data' => collect($DateArray)->toArray(),
            'total_payment' => $total_payment
        ]);
    }

    public function doughnut()
    {
        $returnData = array();
        $returnData['total_links'] = ["99", "16", "11", "10", "1"];
        $returnData['type'] = ["rotator", "copier", "camouflage", "cloacking", "redirector"];
        $returnData['doughnut_bgColors'] = ["#2C483A", "#28E4FB", "#8AFB10", "#142E5A", "#715B60"];
        return $returnData;
    }

    public function downloadPdf(Request $request)
    {
        $Contract = Contract::with(['jobs', 'currency', 'client'])->firstWhere('id', $request->id);
        $sub_total = 0;
        foreach ($Contract->jobs as $Job) {
            foreach ($Job->job_details as $JobDetail) {
                foreach ($JobDetail->payments as $JobPayment) {
                    $sub_total += $JobPayment->subtotal_payment;
                }
            }
        }

        $Contract->total_price = $sub_total;

        $file_name = 'INV-' . $request->id . '.pdf';

        PDF::loadView('invoice.contract-invoice', ['data' => $Contract], [], [
            'title' => 'Another Title',
            'margin_top' => 0
        ])->save(public_path() . '/uploads/invoice_pdf/contract_invoice/' . $file_name);

        $invoiceMailData = [
            'to_name' => $Contract->title,
            'to_email' => $Contract->client->email,
            'subject' => 'Work Invoice by SUMSOLS Corporation',
            'file_name' => $file_name,
            'file_path' => public_path() . '/uploads/invoice_pdf/contract_invoice/' . $file_name,
            'invoice_public_url' => env('BASE_URL') . 'invoices/' . $request->id . '/public-view'
        ];
        Notification::route('mail', $invoiceMailData['to_email'])
            ->notify(new InvoiceNotification($invoiceMailData));

        return response()->json([
            'status' => true,
            'message' => 'Invoice Send Successfully'
        ]);

    }

}

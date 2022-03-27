<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Job;
use App\Models\JobDetail;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        $has_double_shift = 0;
        if (isset($request->has_double_shift)) {
            $has_double_shift = $request->has_double_shift;
        }

//            return $has_double_shift;


//            $validator = Validator::make($request->all(), [
//                'name' => 'required',
//            ]);

//            if ($validator->fails()){
//                return response()->json([
//                    'status' => false,
//                    'message' => $validator->errors()->first()
//                ]);
//            }

        $Contract = Contract::create([
            'title' => $request->title,
            'client_id' => $request->client_id,
            'starts_at' => SiteHelper::reformatDbDate($request->starts_at),
            'ends_at' => SiteHelper::reformatDbDate($request->ends_at),
            'contract_status' => $request->contract_status,
            'currency_id' => $request->currency_id,
        ]);

        $contract_id = $Contract->id;
//           return $contract_id;


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

    public function attendance(Request $request)
    {
        $collection = Payment::with('job_detail')->whereHas('job_detail.job', function ($q) use ($request) {
            $q->where('contract_id', $request->contract_id);
        })->get();

        $Contract = Contract::with('jobs', 'client', 'currency')->where('id', $request->contract_id)->get();
//        return $Contract;


        foreach ($Contract as $contractDate) {
            $period = CarbonPeriod::create($contractDate->starts_at, $contractDate->ends_at);
            $Currency = $contractDate->currency;
            $Job = $contractDate->jobs;

//            $job = [
//                'job' => $Job,
//                'date' => ''
//            ];

            foreach ($Job as $newJ) {
                //select * from payments where contact_id=1 & date=2022-02-03
                // if(recordExist)
                // $job = []
                $job = [
                    'rate_per_day' => $newJ->rate_per_day,
                    'contract_id' => $newJ->contract_id,
                    'double_shift_starts_hours' => $newJ->double_shift_starts_hours,
                    'employee_category' => $newJ->employee_category,
                    'has_double_shift' => $newJ->has_double_shift,
                    'hours_in_day' => $newJ->hours_in_day,
                    'job_details' => $newJ->job_details,
                    'overtime_rate_per_hour' => $newJ->overtime_rate_per_hour,
                    'date' => '',
                ];

                $JobArray[] = $job;
            }
        }

// Iterate over the period
        $array = [];
        foreach ($period as $date) {
            $job['date'] = SiteHelper::reformatReadableDateNice($date);

            $filtered = $collection->where('payment_date', Carbon::parse($date)->format('Y-m-d'));
//            return $filtered;
            foreach ($filtered as $key => $payment){
                if (SiteHelper::reformatReadableDateNice($payment->payment_date) == SiteHelper::reformatReadableDateNice($date)){
                    $payment = [
                        'rate_per_day' => $payment->rate_per_day,
                        'contract_id' => $newJ->contract_id,
                        'double_shift_starts_hours' => $payment->overtime_hours,
                        'employee_category' => $newJ->employee_category,
                        'has_double_shift' => $payment->double_shift,
                        'hours_in_day' => $payment->hours_worked,
                        'overtime_rate_per_hour' => $payment->overtime_hours_rate,
                        'job_details' => $payment->job_details,
                        'date' => '',
                    ];

                    $array[SiteHelper::reformatReadableDateNice($date)] = $payment;
                }
                else{
                    $JobArray[] = $job;
                }
            }
//            return $JobArray;


            $array[SiteHelper::reformatReadableDateNice($date)] = $JobArray;

//            $array["payment"][SiteHelper::reformatReadableDateNice($date)] =  $filtered->all();
//            $array["job"][SiteHelper::reformatReadableDateNice($date)] = $Contract;
//            $array['date'] = $date->format('Y-m-d');
        }

        return response()->json([
            'status' => true,
            'data' => $array
        ]);
    }

    public function doughnut()
    {
//        return 100;
//        $doughnut = Contract::selectRaw('COUNT(id) AS total_contract , contract_status')
//            ->orderBy('total_contract')
//            ->groupBy(DB::raw('contract_status'))
//            ->get();

//        return $doughnut;

//        $newArray = array();
//
//        foreach ($doughnut AS $row) {
//            $newArray['total_status'] = $row['total_status'];
//            $newArray['type'] = $row['type'];
//            $newArray['doughnut_bgColors'] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
//            return $newArray;
//        }

        $returnData = array();
        $returnData['total_links'] = ["99", "16", "11", "10", "1"];
        $returnData['type'] = ["rotator", "copier", "camouflage", "cloacking", "redirector"];
        $returnData['doughnut_bgColors'] = ["#2C483A", "#28E4FB", "#8AFB10", "#142E5A", "#715B60"];
        return $returnData;
    }

}

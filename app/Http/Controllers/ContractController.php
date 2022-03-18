<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Job;
use App\Models\JobDetail;

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
                print_r($value);
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
        $Contract = Contract::with('jobs', 'client')->whereIn('contract_status', explode(',', $request->status))->orderByDesc('created_at')->get();

        return response()->json([
            'status' => true,
            'data' => $Contract
        ]);
    }

    public function detail(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Contract::with('jobs', 'client')->where('id', $request->contract_id)->get()
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
            'data' => $Contract->with('jobs')->with('client')->orderBy('id', 'DESC')->get()
        ]);

    }

}

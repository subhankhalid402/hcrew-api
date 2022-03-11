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
            if ($request->has_double_shift == 'on')
            {
                $has_double_shift = 1;
            }
            else{
                $has_double_shift = 0;
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
                'starts_at' => SiteHelper::reformatDbDate($request->starts_at),
                'ends_at' => SiteHelper::reformatDbDate($request->ends_at),
                'contract_status' => $request->contract_status,
            ]);

           $contract_id =  $Contract->id;
//           return $contract_id;


           foreach ($request->job_details as $index => $detail)
            {

                $Job = Job::create([
                    'hours_in_day' => $detail['hours_in_day'],
                    'contract_id' => 1,
                    'employee_category_id' => $detail['employee_category_id'],
                    'starts_at' => $detail['starts_at'],
                    'ends_at' => $detail['ends_at'],
                    'rate_per_day' => $detail['rate_per_day'],
                    'has_double_shift' => $has_double_shift,
                    'double_shift_starts_hours' => $detail['double_shift_starts_hours'],
                    'overtime_rate_per_hour' => $detail['overtime_rate_per_hour'],
                ]);

                $job_id = $Job->id;

                foreach ($detail['employee_id'] as $key => $value){
                    print_r($value);
                    $JobDetail = JobDetail::create([
                        'job_id' => $job_id,
                        'employee_id' => $value
                    ]);
                }
            }
            die();



            return response()->json([
                'status' => true,
                'message' => 'Contract saved successfully'
            ]);
        }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Contract::with('jobs')->get()
        ]);
    }

}

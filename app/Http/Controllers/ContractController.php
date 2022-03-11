<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Job;

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
//
//            $Contract = Contract::create([
//                'title' => $request->title,
//                'starts_at' => $request->starts_at,
//                'ends_at' => $request->ends_at,
//            ]);
//
//           $contract_id =  $Contract->id;
//           return $contract_id;

           $Job = new Job;
//            $Job_detail_array = '';

           foreach ($request->job_details as $index => $detail)
            {
                $Job_detail_array[$index] = [
                    'hours_in_day' => $detail['hours_in_day'],
                    'contract_id' => 1,
                    'employee_category_id' => 1,
                    'starts_at' => $detail['starts_at'],
                    'ends_at' => $detail['ends_at'],
                    'rate_per_day' => $detail['rate_per_day'],
                    'has_double_shift' => $has_double_shift,
                    'double_shift_starts_hours' => $detail['double_shift_starts_hours'],
                    'overtime_rate_per_hour' => $detail['overtime_rate_per_hour'],
                ];
            }

            $Job->createMany($Job_detail_array);
           $Job->save($Job_detail_array);


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

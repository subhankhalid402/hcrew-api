<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request){
//        return $request;
//        $payment_date = $request->payment_date;

        $payment_date = Carbon::createFromFormat('d M, Y', $request->payment_date)->format('Y-m-d');
        foreach ($request->attendance_details as $attendance){
            $net_payment = floatval($attendance['net_payment']);

            $Payment = Payment::create([
                'payment_date' => $payment_date,
                'hours_worked' => $attendance['hours_worked'],
                'job_detail_id' => $attendance['job_detail_id'],
                'rate_per_day' => $attendance['rate_per_day'],
                'overtime_hours' => $attendance['overtime_hours'],
                'double_shift' => $attendance['double_shift'],
                'overtime_hours_rate' => $attendance['overtime_hours_rate'],
                'net_payment' => $net_payment,
            ]);

//            $Payment->payment_date = '2020-9-20';
//
//            $Payment->hours_worked = $attendance['hours_worked'];
//            $Payment->job_detail_id = $attendance['job_detail_id'];
//            $Payment->rate_per_day = $attendance['rate_per_day'];
//            $Payment->overtime_hours = 1;
//            $Payment->overtime_hours_rate = $attendance['overtime_hours_rate'];
//            $Payment->net_payment = 100;
//
//            $Payment->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Added'
        ]);
    }
}

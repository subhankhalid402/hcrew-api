<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $payment_date = Carbon::createFromFormat('d M, Y', $request->payment_date)->format('Y-m-d');
        foreach ($request->attendance_details as $Attendance) {
            $Payment = Payment::whereDate('payment_date', $payment_date)->where('id', $Attendance['payment_id'])->first();
            $net_payment = floatval($Attendance['net_payment']);

            if ($Attendance['overtime_hours'])
                $overtime_hours = $Attendance['overtime_hours'];
            else
                $overtime_hours = 0;

            $Payment->payment_date = $payment_date;
            $Payment->hours_worked = $Attendance['hours_worked'];
            $Payment->rate_per_day = $Attendance['rate_per_day'];
            $Payment->overtime_hours = $overtime_hours;
            $Payment->double_shift = $Attendance['double_shift'];
            $Payment->overtime_hours_rate = $Attendance['overtime_hours_rate'];
            $Payment->sub_total = $Attendance['sub_total'];
            $Payment->net_payment = $net_payment;
            $Payment->save();
        }

        return response()->json([
            'status' => true,
            'message' => "Payment Successfully of $request->payment_date"
        ]);
    }
}

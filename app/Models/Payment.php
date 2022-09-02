<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['month'];

    public function job_detail(){
        return $this->belongsTo(JobDetail::class)->with('job')->with('employee')->withCount('employee');
    }

    public function getMonthAttribute(){
        return Carbon::parse($this->payment_date)->format('Y-m');
    }
}

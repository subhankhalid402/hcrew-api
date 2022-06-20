<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function job_detail(){
        return $this->belongsTo(JobDetail::class)->with('job')->with('employee');
    }
}

<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $guarded = [];

   protected $appends = ['start_at_format', 'end_at_format'];

    public function employee_category(){
        return $this->belongsTo(EmployeeCategory::class);
    }

    public function job_details(){
        return $this->hasMany(JobDetail::class)->with('employee');
    }

    public function getStartAtFormatAttribute(){
        return Carbon::parse(date('Y-m-d') . $this->starts_at)->format('g:i A');
    }

    public function getEndAtFormatAttribute(){
        return Carbon::parse(date('Y-m-d') . $this->ends_at)->format('g:i A');
    }

}

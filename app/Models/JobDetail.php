<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Job;
use App\Models\Employee;

class JobDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    use SoftDeletes;

    public function job(){
        return $this->belongsTo(Job::class)->with('contract');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class)->with('employee_category');
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }
}

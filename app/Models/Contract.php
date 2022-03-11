<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Job;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function jobs(){
       return $this->hasMany(Job::class);
    }
}

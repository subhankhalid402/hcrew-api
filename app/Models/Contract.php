<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Client;
use App\Models\JobDetail;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['start_date_format', 'end_date_format', 'day_percentage', 'contract_status_labelled','contract_remaining_days'];

    public function jobs()
    {
        return $this->hasMany(Job::class)->with(['employee_category', 'job_details']);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getStartDateFormatAttribute()
    {
        if (empty($this->starts_at)) {
            return '';
        } else {
            return Carbon::parse($this->starts_at)->format('M d, Y');
        }
    }

    public function getContractRemainingDaysAttribute()
    {
        if($this->starts_at==$this->ends_at)
            return 1;
       return  Carbon::parse($this->starts_at)->diffInDays(Carbon::parse($this->ends_at));
    }
    public function getContractStatusLabelledAttribute()
    {
        if ($this->contract_status == 'awaiting')
            return '<label class="badge badge-secondary">Awaiting</label>';
        else if ($this->contract_status == 'inprogress')
            return '<label class="badge badge-info">In-progress</label>';
        else if ($this->contract_status == 'completed')
            return '<label class="badge badge-success">Completed</label>';
        else if ($this->contract_status == 'cancelled')
            return '<label class="badge badge-danger">Cancelled</label>';
        else
            return '<label class="badge badge-info">Undefined</label>';
    }

    public function getEndDateFormatAttribute()
    {
        if (empty($this->ends_at)) {
            return '';
        } else {
            return Carbon::parse($this->ends_at)->format('M d, Y');
        }
    }

    public function getDayPercentageAttribute()
    {
        return 'will_see_later';

       /* $start = strtotime($this->starts_at);
        $end = strtotime($this->ends_at);

        $current = strtotime(now());

        return (($current - $start) / ($end - $start)) * 100;*/

//        $now = time(); // or your date as well
//        $start_date = strtotime($this->starts_at);
//        $end_date = strtotime($this->ends_at);
//        $datediff =  $end_date - $start_date;
//
//        $total_days = round($datediff / (60 * 60 * 24));

//        $start = Carbon::parse($this->starts_at)->timestamp;
//
//        $end = Carbon::parse($this->ends_at)->timestamp;
//
//        $timespan = $end - $start;
////        return $timespan;
//        $current = Carbon::now()->timestamp - $start;
//
//        $progress = $current / $timespan;
//
//        return  (1 - $progress) * 100;
    }
}

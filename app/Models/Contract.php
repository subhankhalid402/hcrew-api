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

    protected $appends = ['start_date_format', 'end_date_format', 'contract_days_percentage', 'contract_status_labelled', 'contract_remaining_days', 'setting'];

    public function jobs()
    {
        return $this->hasMany(Job::class)->with(['employee_category', 'job_details']);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
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
        if ($this->starts_at == $this->ends_at)
            return 1;
//        return Carbon::parse($this->starts_at)->diffInDays(Carbon::parse($this->ends_at));
        $start_date = Carbon::parse($this->starts_at);
        $end_date = Carbon::parse($this->ends_at);
        $today = Carbon::now();
        $total_days = $start_date->diffInDays($end_date);

        $total_remaining_days = $start_date->diffInDays($today);
        $days =  $total_days - $total_remaining_days;

        if ($days <= 0)
            return 0;

        return $days;
    }

    public function getContractStatusLabelledAttribute()
    {
        if ($this->contract_status == 'awaiting')
            return '<label class="badge bg-secondary">Awaiting</label>';
        else if ($this->contract_status == 'inprogress')
            return '<label class="badge bg-info">In-progress</label>';
        else if ($this->contract_status == 'completed')
            return '<label class="badge bg-success">Completed</label>';
        else if ($this->contract_status == 'cancelled')
            return '<label class="badge bg-danger">Cancelled</label>';
        else
            return '<label class="badge bg-info">Undefined</label>';
    }

    public function getEndDateFormatAttribute()
    {
        if (empty($this->ends_at)) {
            return '';
        } else {
            return Carbon::parse($this->ends_at)->format('M d, Y');
        }
    }

    public function getContractDaysPercentageAttribute()
    {
        $start_date = Carbon::parse($this->starts_at);
        $end_date = Carbon::parse($this->ends_at);
        $today = Carbon::now();
        $total_days = $start_date->diffInDays($end_date);

        $total_remaining_days = $start_date->diffInDays($today);

        if ($today >= $end_date)
            return 100;
        if ($today < $start_date)
            return 0;

        $percentage = ($total_remaining_days / $total_days) * 100;

        if ($percentage > 100)
            return 100;

        return number_format($percentage, 2);
    }
    public function getSettingAttribute(){
        return Setting::find(1);
    }
}

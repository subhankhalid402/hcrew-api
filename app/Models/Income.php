<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['income_date_formatted', 'month'];

    public function getIncomeDateFormattedAttribute()
    {
        if ($this->date)
            return SiteHelper::reformatReadableDate($this->date);
        else
            return false;
    }

    public function income_head()
    {
        return $this->belongsTo(IncomeHead::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getMonthAttribute()
    {
        return Carbon::parse($this->date)->format('Y-m');
    }
}

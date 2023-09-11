<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['expense_date_formatted', 'month', 'amount_formatted'];

    public function getExpenseDateFormattedAttribute()
    {
        if ($this->date)
            return SiteHelper::reformatReadableDate($this->date);
        else
            return false;
    }

    public function expense_head()
    {
        return $this->belongsTo(ExpenseHead::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'object_id', 'id')->where('object', 'Expense');
    }

    public function getMonthAttribute()
    {
        return Carbon::parse($this->date)->format('Y-m');
    }

    public function getAmountFormattedAttribute()
    {
        return SiteHelper::amountFormatter($this->amount);
    }
}

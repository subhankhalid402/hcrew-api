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

    protected $appends = ['income_date_formatted', 'month', 'amount_formatted'];

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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getMonthAttribute()
    {
        return Carbon::parse($this->date)->format('Y-m');
    }

    public function attachment(){
        return $this->hasOne(Attachment::class, 'object_id', 'id')->where('object', 'Income');
    }

    public function getAmountFormattedAttribute()
    {
        return SiteHelper::amountFormatter($this->amount);
    }
}

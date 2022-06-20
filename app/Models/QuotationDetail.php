<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['from_date_formatted', 'to_date_formatted'];

    public function getFromDateFormattedAttribute()
    {
        if (empty($this->from_date)) {
            return '';
        } else {
            return Carbon::parse($this->from_date)->format('M d, Y');
        }
    }

    public function getToDateFormattedAttribute()
    {
        if (empty($this->to_date)) {
            return '';
        } else {
            return Carbon::parse($this->to_date)->format('M d, Y');
        }
    }
}

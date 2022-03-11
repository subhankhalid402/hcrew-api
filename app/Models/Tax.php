<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Tax extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['formatted_created_at_datetime', 'amount_formatter'];
    protected $guarded = [];

    public function getFormattedCreatedAtDatetimeAttribute(){
        return Carbon::parse($this->created_at)->format('d M, Y');
    }

    public function getAmountFormatterAttribute()
    {
        return $this->amount + 0;
    }
}

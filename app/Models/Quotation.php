<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\True_;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['setting'];

    public function quotation_details(){
        return $this->hasMany(QuotationDetail::class, 'quotation_id', 'id');
    }

    public function getSettingAttribute(){
        return Setting::find(1);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}

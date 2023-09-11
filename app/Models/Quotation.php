<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['setting', 'file_url', 'total_amount_formatted'];

    public function quotation_details()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id', 'id');
    }

    public function getSettingAttribute()
    {
        return Setting::find(1);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function attachment()
    {
        return $this->hasMany(Attachment::class, 'object_id', 'id')->where('object', '=', 'Quotation');
    }

    public function getFileUrlAttribute()
    {
        return asset('upload/quotation/attachment/');
    }

    public function get_created_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getTotalAmountFormattedAttribute()
    {
        return SiteHelper::amountFormatter($this->total);
    }
}

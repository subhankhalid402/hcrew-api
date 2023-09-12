<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['setting', 'file_url', 'total_amount_formatted', 'created_at_formatted'];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
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
        return asset('upload/invoice/attachment/');
    }

    public function get_created_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getTotalAmountFormattedAttribute()
    {
        return SiteHelper::amountFormatter($this->total);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return SiteHelper::reformatReadableDateTime($this->created_at);
    }
}

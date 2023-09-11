<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['created_at_formatted', 'file_url'];

    public function getCreatedAtFormattedAttribute()
    {
        return SiteHelper::reformatReadableDateTime($this->created_at);
    }

    public function getFileUrlAttribute()
    {
        if ($this->context == 'expense') {
            return asset('uploads/expense-attachment') . '/' . $this->file_name;
        }elseif($this->context == 'income'){
            return asset('uploads/income-attachment') . '/' . $this->file_name;
        }

        return '';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ClientCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['formatted_created_at_datetime'];

    public function getFormattedCreatedAtDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('d M, Y');
    }
}

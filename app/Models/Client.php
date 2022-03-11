<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Currency;
use App\Models\ClientCategory;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['image_url'];


    public function getImageUrlAttribute(){
        return asset('public/uploads/client') . '/' . $this->logo;
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function clientCategory(){
        return $this->belongsTo(ClientCategory::class);
    }
}

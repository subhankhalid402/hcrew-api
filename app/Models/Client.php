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

    protected $guarded  = [];
    protected $appends = ['image_url'];


    public function getImageUrlAttribute(){
        if ($this->logo)
            return env('BASE_URL'). 'public/uploads/client/' . $this->logo;
        else
            return asset('uploads/user.png');
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function client_category(){
        return $this->belongsTo(ClientCategory::class);
    }

    public function contracts(){
        return $this->hasMany(Contract::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $appends = ['logo_url', 'signature_url'];

    public function getLogoUrlAttribute(){
        if ($this->logo)
            return env('BASE_URL'). 'public/uploads/website/' . $this->logo;
        else
            return asset('uploads/user.png');
    }
    public function getSignatureUrlAttribute(){
            return env('BASE_URL'). 'public/uploads/website/' . $this->signature;
    }
}

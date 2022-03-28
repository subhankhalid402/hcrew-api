<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeCategory;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['image_url'];


    public function getImageUrlAttribute()
    {
        if ($this->picture)
            return asset('uploads/employee') . '/' . $this->picture;
        else
            return asset('uploads/user.png');
    }

    public function employee_category()
    {
        return $this->belongsTo(EmployeeCategory::class);
    }
}

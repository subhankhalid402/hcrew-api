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


    public function getImageUrlAttribute(){
        return asset('public/uploads/employee') . '/' . $this->picture;
    }

    public function employeeCategory(){
        return $this->belongsTo(EmployeeCategory::class);
    }
}

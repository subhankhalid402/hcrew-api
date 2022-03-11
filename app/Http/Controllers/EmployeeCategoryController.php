<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeCategoryController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $EmployeeCategory = EmployeeCategory::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Employee Category saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => EmployeeCategory::all()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => EmployeeCategory::find($request->id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $EmployeeCategory = EmployeeCategory::find($request->id);

        $EmployeeCategory->name = $request->name;

        $EmployeeCategory->save();

        return response()->json([
            'status' => true,
            'message' => 'Employee Category Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $EmployeeCategory = EmployeeCategory::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

}

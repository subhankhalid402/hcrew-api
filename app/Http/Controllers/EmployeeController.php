<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
//            'last_name' => 'required',
//            'email' => 'required|email|unique:clients',
//            'phone_no' => 'required',
//            'address' => 'required',
//            'passport_number' => 'required',
//            'employee_category_id' => 'required',
//            'picture' => 'required',
//            'joining_date' => 'required',
//            'basic_salary' => 'required',
//            'bio' => 'required',
//            'dob' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $picture = $request->file('picture');
            if ($picture) {
                $picture_name = Str::random(10) . '.' . $picture->getClientOriginalExtension();
                $picture->move(public_path('uploads/employee'), $picture_name);
            } else {
                $picture_name = '';
            }
        $Employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'address' => $request->address,
            'passport_number' => $request->passport_number,
            'employee_category_id' => $request->employee_category_id,
            'picture' => $picture_name,
            'joining_date' => $request->joining_date,
            'basic_salary' => $request->basic_salary,
            'dob' => $request->dob,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Employee saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Employee::with('employee_category')->get()
        ]);
    }

    public function detail(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Employee::with('employee_category')->with('job_details.job.contract')->where('id', $request->id)->first()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Employee::find($request->id)
        ]);
    }

    public function update(Request $request)
    {

//        $validator = Validator::make($request->all(), [
//            'first_name' => 'required',
//        ]);
        //            'last_name' => 'required',
//            'email' => 'required|email|unique:clients',
//            'phone_no' => 'required',
//            'address' => 'required',
//            'passport_number' => 'required',
//            'employee_category_id' => 'required',
//            'joining_date' => 'required',
//            'basic_salary' => 'required',
//            'bio' => 'required',
//            'dob' => 'required',

//        if ($validator->fails()){
//            return response()->json([
//                'status' => false,
//                'message' => $validator->errors()->first()
//            ]);
//        }

        $Employee = Employee::find($request->id);

        $Employee->first_name = $request->first_name;
        $Employee->last_name = $request->last_name;
        $Employee->email = $request->email;
        $Employee->phone_no = $request->phone_no;
        $Employee->address = $request->address;
        $Employee->passport_number = $request->passport_number;
        $Employee->employee_category_id = $request->employee_category_id;
        $Employee->joining_date = $request->joining_date;
        $Employee->basic_salary = $request->basic_salary;
        $Employee->bio = $request->bio;
        $Employee->dob = $request->dob;

        if (!empty($request->picture)) {
            $picture = $request->file('picture');
            $picture_name = Str::random(10) . '.' . $picture->getCLientOriginalExtension();

            $picture->move(public_path('uploads/employee'), $picture_name);
            $Employee->picture = $picture_name;
        }

        $Employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Employee Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Employee = Employee::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function search()
    {
        $Employee = Employee::query();
        if (request('term')) {
            $Employee->where(['name', 'Like', '%' . request('term') . '%'], []);
        }
        if (request('cat_id')) {
            $Employee->where('employee_category_id', '=', request('cat_id'));
        }
        return response()->json([
            'status' => true,
            'data' => $Employee->orderBy('id', 'DESC')->get()
        ]);
    }
}

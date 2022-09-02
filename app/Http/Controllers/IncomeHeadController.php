<?php

namespace App\Http\Controllers;

use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncomeHeadController extends Controller
{
    public function index()
    {
        return view('income-head.list');
    }

    public function all()
    {
        $IncomeHeads = IncomeHead::orderByDesc('id')->get();
        return response()->json([
            'status' => true,
            'data' => $IncomeHeads
        ]);
    }

    public function create()
    {
        return view('income-head.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        IncomeHead::create([
            'name' => $request->name,
            'notes' => $request->notes,
            'created_by' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Record saved successfully'
        ]);
    }

    public function edit()
    {
        return view('income-head.edit');
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => IncomeHead::find($request->id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }
        $IncomeHead = IncomeHead::find($request->id);
        $IncomeHead->name = $request->name;
        $IncomeHead->notes = $request->notes;
        $IncomeHead->save();

        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully'
        ]);
    }

    public function delete(Request $request)
    {
        IncomeHead::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }
}

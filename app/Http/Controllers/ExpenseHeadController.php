<?php

namespace App\Http\Controllers;

use App\Models\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseHeadController extends Controller
{
    public function index()
    {
        return view('expense-head.list');
    }

    public function all()
    {
        $ExpenseHeads = ExpenseHead::orderByDesc('id')->get();
        return response()->json([
            'status' => true,
            'data' => $ExpenseHeads
        ]);
    }

    public function create()
    {
        return view('expense-head.create');
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

        ExpenseHead::create([
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
        return view('expense-head.edit');
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => ExpenseHead::find($request->id)
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
        $ExpenseHead = ExpenseHead::find($request->id);
        $ExpenseHead->name = $request->name;
        $ExpenseHead->notes = $request->notes;
        $ExpenseHead->save();

        return response()->json([
            'status' => true,
            'message' => 'Record updated successfully'
        ]);
    }

    public function delete(Request $request)
    {
        ExpenseHead::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Support\Facades\Validator;

class TaxController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required',
            'tax_key' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Tax = Tax::create([
            'name' => $request->name,
            'tax_key' => $request->tax_key,
            'amount' => $request->amount,
            'notes' => $request->notes
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tax saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Tax::all()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Tax::find($request->id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required',
            'tax_key' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Tax = Tax::find($request->id);

        $Tax->name = $request->name;
        $Tax->tax_key = $request->tax_key;
        $Tax->amount = $request->amount;
        $Tax->notes = $request->notes;

        $Tax->save();

        return response()->json([
            'status' => true,
            'message' => 'Tax Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Tax = Tax::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }
}

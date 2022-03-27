<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'symbol' => 'required',
            'position' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

       $Currency = Currency::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'position' => $request->position,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Currency saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Currency::all()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Currency::find($request->id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'symbol' => 'required',
            'position' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Currency = Currency::find($request->id);

        $Currency->name = $request->name;
        $Currency->symbol = $request->symbol;
        $Currency->position = $request->position;

        $Currency->save();

        return response()->json([
            'status' => true,
            'message' => 'Currency Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Currency = Currency::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }
}

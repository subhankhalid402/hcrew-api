<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientCategory;
use Illuminate\Support\Facades\Validator;

class ClientCategoryController extends Controller
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

        $ClientCategory = ClientCategory::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Client Category saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => ClientCategory::all()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => ClientCategory::find($request->id)
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

        $ClientCategory = ClientCategory::find($request->id);

        $ClientCategory->name = $request->name;

        $ClientCategory->save();

        return response()->json([
            'status' => true,
            'message' => 'Client Category Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $ClientCategory = ClientCategory::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Client;


class ClientController extends Controller
{
    public function store(Request $request)
    {
//        return $request->file('image');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'short_name' => 'required',
            'email' => 'required|email|unique:clients',
            'phone_no' => 'required',
            'address' => 'required',
            'tax_number' => 'required',
            'client_category_id' => 'required',
            'currency_id' => 'required',
            'logo' => 'required',
            'focal_name' => 'required',
            'focal_phone_no' => 'required',
            'focal_email' => 'required',
            'website' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $logo = $request->file('logo');
        $logo_name = Str::random(10) . '.' . $logo->getClientOriginalExtension();

        $logo->move(public_path('uploads/client'), $logo_name);

        $Client = Client::create([
            'name' => $request->name,
        ]);
//        'short_name' => $request->short_name,
//            'email' => $request->email,
//            'phone_no' => $request->phone_no,
//            'address' => $request->address,
//            'tax_number' => $request->tax_number,
//            'client_category_id' => $request->client_category_id,
//            'currency_id' => $request->currency_id,
//            'logo' => $logo_name,
//            'notes' => $request->notes,
//            'focal_name' => $request->focal_name,
//            'focal_phone_no' => $request->focal_phone_no,
//            'focal_email' => $request->focal_email,
//            'website' => $request->website,



        
//        $Client  =new Client;
//        $Client->username = $request->username;
//        $Client->email = $request->email;
//        $Client->password = Hash::make($request->password);
//        $Client->save();

        return response()->json([
            'status' => true,
            'message' => 'Client saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => Client::with('currency','clientCategory')->get()
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Client::find($request->id)
        ]);
    }

    public function update(Request $request)
    {
//        return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'short_name' => 'required',
            'email' => 'required',
            'phone_no' => 'required',
            'address' => 'required',
            'tax_number' => 'required',
            'client_category_id' => 'required',
            'currency_id' => 'required',
//            'logo' => 'required',
            'focal_name' => 'required',
            'focal_phone_no' => 'required',
            'focal_email' => 'required',
            'website' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $Client = Client::find($request->id);

        $Client->name = $request->name;
        $Client->short_name = $request->short_name;
        $Client->email = $request->email;
        $Client->phone_no = $request->phone_no;
        $Client->address = $request->address;
        $Client->tax_number = $request->tax_number;
        $Client->client_category_id = $request->client_category_id;
        $Client->currency_id = $request->currency_id;
        $Client->notes = $request->notes;
        $Client->focal_name = $request->focal_name;
        $Client->focal_phone_no = $request->focal_phone_no;
        $Client->focal_email = $request->focal_email;
        $Client->website = $request->website;

        if (!empty($request->logo)){
            $logo = $request->file('logo');
            $logo_name = Str::random(10) . '.' . $logo->getClientOriginalExtension();

            $logo->move(public_path('uploads/client'), $logo_name);
            $Client->logo = $logo_name;
        }

        $Client->save();

        return response()->json([
            'status' => true,
            'message' => 'Client Update Successfully'
        ]);
    }

    public function destroy(Request $request)
    {
        $Client = Client::where(['id' => $request->id])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function search(){
        $Client = Client::query();
        if (request('term')) {
            $Client->where('name', 'Like', '%' . request('term') . '%');
        }
        return response()->json([
            'status' => true,
            'data' => $Client->orderBy('id', 'DESC')->get()
        ]);

    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Utils;

class UserController extends Controller
{
    public function store(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'username' => 'required',
//            'email' => 'required|email|unique:users',
//            'password' => 'required|min:6'
//        ]);
//
//        if ($validator->fails()){
//            return response()->json([
//                'status' => false,
//                'message' => $validator->errors()->first()
//            ]);
//        }

//       $User = User::create([
//            'username' => $request->username,
//            'email' => $request->email,
//            'password' => Hash::make($request->password)
//        ]);
        $User  = new User;
        $User->username = $request->username;
        $User->email = $request->email;
        $User->password = Hash::make($request->password);
        $User->role_id = $request->role_id;
        $User->save();

        return response()->json([
            'status' => true,
            'message' => 'User saved successfully'
        ]);
    }

    public function all()
    {
        return response()->json([
            'status' => true,
            'data' => User::with('role')->get()
        ]);
    }

    public function show(Request $request)
    {
     return response()->json([
         'status' => true,
         'data' => User::find($request->id)
     ]);
    }

    public function update(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'username' => 'required',
//            'email' => 'required|email|unique:users',
////            'password' => 'required|min:6'
//        ]);
//
//        if ($validator->fails()){
//            return response()->json([
//                'status' => false,
//                'message' => $validator->errors()->first()
//            ]);
//        }

        $User = User::find($request->id);

        $User->username = $request->username;
        $User->email = $request->email;
        $User->role_id = $request->role_id;

        if (!empty($request->password)) {
            $User->password =  Hash::make($request->password);
        }

        $User->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully'
        ]);

    }

    public function destroy(Request $request){
        User::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function getRole(){
        return response()->json([
            'status' => true,
            'data' => Role::all()
        ]);
    }

    public function accountSettingUpdate(Request $request){
        $User = User::find(Auth::id());
        if (!empty($request->password)) {
            $User->password =  Hash::make($request->password);
        }
        $User->username = $request->username;

        $User->save();

        return response()->json([
            'status' => true,
            'message' => 'Account Update successfully'
        ]);
    }
}

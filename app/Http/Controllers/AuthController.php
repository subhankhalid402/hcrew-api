<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('Auth.login');
    }

    public function loginBackend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // $client = $request->client ?? 'user';

        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        // $User = User::where('email', $request->email)->whereHas('role', function ($Role) use ($client) {
        //     $Role->where('key', $client);
        // })->first();

        $User = User::with('role')->where('email', $request->email)->first();


        // check if User exist or not
        if (!$User) {
            return response()->json([
                'status' => false,
                'message' => 'No user registered with this email'
            ]);
        }

        // check if password match or not
        if (Hash::check($request->password, $User->password)) {


            // if (empty($User->email_verified_at)) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Email is not verified yet'
            //     ]);
            // }

            $token = $User->createToken('Admin')->plainTextToken;
            return response()->json([
                'code' => 200,
                'message' => 'Logged in',
                'token' => ['token' => $token],
                'user' => $User,
                'roleKey' => $User->role->role_key
            ])->header('Cache-Control', 'private')->header('Authorization', $token);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Password!'
            ]);
        }
    }

    public function registerBackend(Request $request)
    {

        // 'phone_code' => 'required',
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|required_with:confirm_password|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }
        $role_id = Role::firstWhere('key', $request->role_key)->id;

        $User = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_code' => $request->phone_code,
            'phone_no' => $request->phone_no,
            'email' => $request->email,
            'role_id' => $role_id,
            'email_verified_at' => Carbon::now(),
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);
        if ($User) {
            return response()->json([
                'status' => true,
                'message' => 'Register Successfully'
            ]);
        }


        // $User->sendEmailVerificationNotification();
        // return response()->json([
        //     'status' => true,
        //     'message' => 'Registration successfull! Activation email sent. please verify your account to login'
        // ]);
    }

    public function changePassword()
    {
        return view('Auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6|required_with:confirm_password|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        $User = User::where('id', Auth::id())->first();
        if (Hash::check($request->current_password, $User->password)) {
            $User->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $User->save();
            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully'
            ]);
        } else {
            return response()->json([
                'status' => FALSE,
                'message' => 'Current Password does not match'
            ]);
        }
    }

    public function forgotPassword()
    {
        return view('Auth.forgot_password');
    }

    public function forgotPasswordCheck(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        $User = User::where('email', $request->email)->first();
        if (!empty($User)) {
            $password_reset_code = date('hisymd') . $User->id;
            $User->password_reset_code = $password_reset_code;
            $User->save();

            /*******PREPARE EMAIL FOR USER PASSWORD RESET********/
            $link = env('BASE_URL') . 'reset/' . $password_reset_code;
            // Notification::send($user, new PasswordReset($details));
            $User->notify(new PasswordReset($password_reset_code));


            /*******END- PREPARE EMAIL FOR USER PASSWORD RESET********/

            return response()->json([
                'status' => TRUE,
                'message' => "An Email Has been sent to your email address to reset your password",
            ]);
        } else {
            return response()->json([
                'status' => FALSE,
                'message' => "Email doesn't exists!"
            ]);
        }
    }

    public function reset($password_reset_code)
    {
        $User = User::where('password_reset_code', $password_reset_code)->get()->toArray();
        if (empty($User)) {
            return redirect()->action([AuthController::class, 'login']);
        }

        return view('Auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_reset_code' => 'required',
            'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        $User = User::where('password_reset_code', $request->password_reset_code)->first();
        if (!empty($User)) {
            $User->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $User->password_reset_code = '';
            $User->save();
            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully'
            ]);
        } else {
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid password reset request'
            ]);
        }
    }

    public function websiteSettingShow()
    {
        return response()->json([
            'status' => true,
            'data' => Setting::find(1)
        ]);
    }

    public function accountInfoShow(){
        return response()->json([
            'status' => true,
            'data' => User::find(Auth::id())
        ]);
    }

    public function accountSettingUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        $User = User::find(Auth::id());


        if (!empty($User)) {

            if (!empty($request->password)) {
                $User->password = Hash::make($request->password);
            }
            $User->username = $request->username;

            $User->save();

            return response()->json([
                'status' => true,
                'message' => 'Account Settings Updated successfully',
                'user' => $User
            ]);
        } else {
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid request'
            ]);
        }
    }

    public function websiteSettingUpdate(Request $request)
    {
//        return $request;
        $logo = $request->file('logo');
        $signature_logo = $request->file('signatureLogo');

        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors()->first()
            ]);
        }

        $Setting = Setting::find(1);

        if (!empty($Setting)) {
            $Setting->company_name = $request->company_name;
            $Setting->address = $request->address;
            $Setting->phone_no = $request->phone_no;
            $Setting->phone_no_2 = $request->phone_no_2;
            $Setting->email = $request->email;
            $Setting->website = $request->website;
            $Setting->terms_and_conditions = $request->terms_and_conditions;

            if($logo && $signature_logo){
                $logo_name = Str::random(10) . '.' . $logo->getClientOriginalExtension();
                $signature_logo_name = Str::random(10) . '.' . $signature_logo->getClientOriginalExtension();
                $logo->move(public_path('uploads/website'), $logo_name);
                $signature_logo->move(public_path('uploads/website'), $signature_logo_name);

                $Setting->logo = $logo_name;
                $Setting->signature = $signature_logo_name;
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Select Image'
                ]);
            }

            $Setting->save();

            return response()->json([
                'status' => true,
                'message' => 'Account Settings Updated successfully',
                'user' => $Setting
            ]);
        } else {
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid request'
            ]);
        }
    }


    public function verify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            $message = "Invalid/Expired url provided.";
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $message = "Email has been verified. You can now login from app";
        } else {
            $message = "Email verified already";
        }

        return view('Auth.email_verified', ['message' => $message]);
    }
}

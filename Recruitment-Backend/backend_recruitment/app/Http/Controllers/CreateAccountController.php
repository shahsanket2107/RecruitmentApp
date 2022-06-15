<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Response;
use JWTFactory;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Mail;
use App\Models\User;
use App\Models\userprofile;
use App\Models\charge;
use App\helpers;
use Illuminate\Support\Facades\Session;
use Laravel\Ui\Presets\React;

class CreateAccountController extends Controller
{
    function generateRandomString($length)
    {
        $randomString = '';
        $characters = '123456789';
        $characterLengths = strlen($characters);
        for($i=0; $i<$length;$i++)
        {
            $randomString .= $characters[rand(0,$characterLengths - 1)];
        }
        return $randomString;
    }

    function SuccessResponse($message,$otp,$status_code,$data)
    {
        return response()->json(['success' => true,
            'status_code' => $status_code,
            'message' => $message,
            'otp' => $otp,
            'data' => $data
        ]);
    }

    function InvalidResponse($message,$status_code)
    {
        return response()->json(['success' => false,
            'status_code' => $status_code,
            'message' => $message,
            'data' => array()
        ]);
    }

    public function createAccount(Request $request)
    {
        $accountUserPhone = User::where('phone',$request->phone)->first();
        if(empty($accountUserPhone)){
            $otp = $this->generateRandomString(4);
            $createAccountUser = new User;
            Session::put('OTP'.$createAccountUser->id, $otp);
            $createAccountUser->name         = $request->name;
            $createAccountUser->phone        = $request->phone;
            $createAccountUser->gender       = $request->gender;
            $createAccountUser->email        = $request->email;
            $createAccountUser->password     = Hash::make($request->password);
            $createAccountUser->save();
            $message = 'Registration Successfully.';
            return $this->SuccessResponse($message,$otp,200,$createAccountUser);

        } else {
            $message = 'This phone already exists in our record';
            return $this->InvalidResponse($message,101);
        }
    }

    public function verify_otp(Request $request)
    {
        $user=User::where('phone',$request->phone)->first();
        $otp=session()->get('OTP'.$user->id);
        if($otp == $request->otp){
            $user->phone_verified=true;
            $user->save();
            $message = 'User verified Successfully.';
            return $this->SuccessResponse($message,$request->otp,200,$user);
        }
        else
        {
            $message = 'OTP is invalid';
            return $this->InvalidResponse($message,101);
        }
    }

    public function resendOtp(Request $request)
    {
        $user=User::where('phone',$request->phone)->first();
        Session::forget('OTP'.$user->id);
        $otp = $this->generateRandomString(4);
        Session::put('OTP'.$user->id, $otp);
        return response()->json(['success' => true,
            'status_code' => 200,
            'message' => 'Otp resent successfully to '.$user->phone,
            'otp' => $otp
        ]);
    }

    public function userCharges(Request $request)
    {
        $Acctuser = User::where('id',$request->id)->first();
        if(empty($Acctuser))
        {
            $message = 'This user doesn\'t exist in our record';
            return $this->InvalidResponse($message,101);
        }
        else
        {
            $user = new charge;
            $user->user_id=$Acctuser->id;
            $user->charges = $request->charges;
            $user->save();
            $message = 'Subscription charges added successfully';
            return response()->json(['success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $user
        ]);
        }
    }

    public function userProfile(Request $request)
    {
        $Acctuser = User::where('id',$request->id)->first();
        if(empty($Acctuser))
        {
            $message = 'This user doesn\'t exist in our record';
            return $this->InvalidResponse($message,101);
        }
        else
        {
            $user = new userprofile;
            $user->user_id=$Acctuser->id;
            $user->Education=$request->Education;
            $user->Current_Position=$request->Current_Position;
            $user->Current_Industry=$request->Current_Industry;
            $user->Total_Work_Experience=$request->Total_Work_Experience;
            $user->Last_Working_Month=$request->Last_Working_Month;
            $user->Current_Location=$request->Current_Location;
            $user->Last_Drawn_Salary=$request->Last_Drawn_Salary;
            $user->save();
            $message = 'User Profile added successfully';
            return response()->json(['success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $user
        ]);
        }
    }

    // public function postRefreshToken(Request $request)
    // {
    //     $inputData = $request->all();
    //     $header = $request->header('AuthorizationUser');
    //     if(empty($header))
    //     {
    //         $message = 'Authorisation required' ;
    //         return InvalidResponse($message,101);
    //     }
    //     $response = veriftyAPITokenData($header);
    //     $success = $response->original['success'];
    //     if (!$success)
    //     {
    //         return $response;
    //     }
    // }

    // public function resend_otp_on_email(Request $request){

    //     $user = User::where('email',$request->email)->first();

    //     if(!empty($user)){
    //         $otp = generateRandomString(4);
    //         // $user->otp = $otp;
    //         // $user->save();
    //         /*
    //         $from_address = env('MAIL_FROM_ADDRESS');
    //         $from_name = env('MAIL_FROM_NAME');

    //         $url = $otp;
    //         $data = array('name'=> $user->name,'url' => $url);

    //         Mail::send('mails.cookery-resend-otp', $data, function($message) use($user,$from_address,$from_name) {
    //             $message->to($user->email);
    //             $message->subject('Resend OTP to your email ');
    //             $message->from($from_address,$from_name);
    //         });
    //         */
    //         $message = 'Resending OTP Successfully';
    //         return SuccessResponse($message,$otp,200,$user);
    //     } else {
    //         $message = 'This Email not exists in our record';
    //         return InvalidResponse($message,101);
    //     }
    // }
}

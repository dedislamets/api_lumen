<?php

namespace App\Http\Controllers;

use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;

class AuthController extends Controller
{

	public function emailRequestVerification(Request $request)
    {
        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address is already verified.');
        }
        
        $request->user()->sendEmailVerificationNotification();
        
        return response()->json('Email request verification sent to '. Auth::user()->email);
    }

    public function emailVerify(Request $request)
    {

        $this->validate($request, [
          'token' => 'required|string',
        ]);
        \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        dd($request->user());
        if ( ! $request->user() ) {
            return response()->json('Invalid token', 401);
        }
    
        if ( $request->user()->hasVerifiedEmail() ) {
            return response()->json('Email address '.$request->user()->getEmailForVerification().' is already verified.');
        }
        $request->user()->markEmailAsVerified();
        return response()->json('Email address '. $request->user()->email.' successfully verified.');
    }
}
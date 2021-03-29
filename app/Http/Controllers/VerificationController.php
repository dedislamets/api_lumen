<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Tokens;
// use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\Providers\Auth;


class VerificationController extends Controller
{
    /*
   |--------------------------------------------------------------------------
   | Email Verification Controller
   |--------------------------------------------------------------------------
   |
   | This controller is responsible for handling email verification for any
   | user that recently registered with the application. Emails may also
   | be re-sent if the user didn't receive the original email message.
   |
   */

    // use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('signed')->only('verify');
        // $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show()
    {
         return response()->json('Email request verification sent to ');
    }

    public function verify(Request $request)
    {
        $this->validate($request, [
          'token' => 'required|string',
        ]);

        $userID = $request["id"];
        $verifyToken = Tokens::where(array('token' => $request["token"], 'id_user' => $userID) )->first();
        if(empty($verifyToken) ){
            return response()->json('Token not valid');
        }

        
        $user = Register::findOrFail($userID);

        if (!empty($user->email_verified_at) ) {
            return response()->json('Email address '. $user->email .' is already verified.');
        }

        $date = \Carbon\Carbon::now();
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->save();

        // \Tymon\JWTAuth\Facades\JWTAuth::getToken();
        // \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        // if ( ! $request->user() ) {
        //     return response()->json('Invalid token', 401);
        // }
    
        
        // $request->user()->markEmailAsVerified();
        return response()->json('Email address '. $user->email.' successfully verified.');
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            session()->flash('error', 'User already have verified email!');
            return redirect()->route('home.index');
        }
        $request->user()->sendEmailVerificationNotification();
        session()->flash('success', 'The notification has been resubmitted');
        return redirect()->route('home.index');
        // return back()->with('resent', true);
    }
}

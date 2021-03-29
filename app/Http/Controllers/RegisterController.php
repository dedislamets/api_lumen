<?php

namespace App\Http\Controllers;

use App\Models\Register;
use App\Mail\VerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\URL;
// use Illuminate\Support\Facades\Config;
// use Illuminate\Routing\UrlGenerator;
use Validator;
use Auth;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $data = URL::temporarySignedRoute(
        //     'verification.verify',
        //     Carbon::now()->addMinutes(60),
        //     [
        //         'id' => 11,
        //         'hash' => sha1('dedi.supatman@modena.com'),
        //     ]);

        $data = Register::all();
        return response()->json($data);
    }
    public function show($id)
    {
        $data = Register::where('id', $id)->first();

        $verifyUser = VerifyUser::create([
            'user_id' => $data->id,
            'token' => sha1(time())
        ]);
        Mail::to($data->email)->send(new VerificationEmail($data));
        // $data = array('name'=>"Arunkumar");
        // Mail::send('mail', $data, function($message) {
        //     $message->to('dedi.supatman@modena.com', 'dedi.supatman')
        //             ->subject('Test Mail from Selva');
        //     $message->from('dedi.slamets@gmail.com','dedi');
        // });

        return response()->json($data);
    }

    public function verifyUser($token)
    {
      $verifyUser = VerifyUser::where('token', $token)->first();
      if(isset($verifyUser) ){
        $user = $verifyUser->user;
        if(!$user->verified) {
          $verifyUser->user->verified = 1;
          $verifyUser->user->save();
          $status = "Your e-mail is verified. You can now login.";
        } else {
          $status = "Your e-mail is already verified. You can now login.";
        }
      } else {
        return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
      }
      return redirect('/login')->with('status', $status);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
            ],
            [
                'name.regex' => 'The name may only contain letters and whitespace.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'error','msg' => $validator->errors(),401]);
        }

        $data = new Register();
        $data->name = $request->input('name');
        $data->email = $request->input('email');
        $data->password = Hash::make($request->input('password'));
        $data->phone = $request->input('phone');
        $data->suspend = 0;
        $data->save();

        return response()->json(['status' => 'success','msg' => $data], 201);
    }
    public function update(Request $request, $id)
    {
        $data = Register::where('id', $id)->first();
        $data->activity = $request->input('activity');
        $data->description = $request->input('description');
        $data->save();

        return response('Berhasil Merubah Data');
    }

    public function destroy($id)
    {
        $data = Register::where('id', $id)->first();
        $data->delete();

        return response('Berhasil Menghapus Data');
    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error','msg' => $validator->errors(),401]);
        }else{
            $user = Register::where('email', $request->input('email'))->first();

            if(Hash::check($request->input('password'), $user->password)){
                return response()->json(['status' => 'success','msg' => $user]);
            }else{
                return response()->json(['status' => 'fail'],401);
            }
        }
    }

    public function mail() {
        $data = array('name'=>"Arunkumar");
        Mail::send('mail', $data, function($message) {
            $message->to('dedi.supatman@modena.com', 'dedi.supatman')
                    ->subject('Test Mail from Selva');
            $message->from('dedi.slamets@gmail.com','dedi');
        });
        echo "Email Sent. Check your inbox.";
    }
    
}

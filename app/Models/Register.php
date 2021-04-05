<?php

namespace App\Models;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Mail;
// use App\Notifications\VerifyEmail;

class Register extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject,CanResetPasswordContract
{
	use Authenticatable, Authorizable, Notifiable, MustVerifyEmail,CanResetPassword;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'birth_date', 'phone', 'subscribe', 'suspend', 'email_verified_at'
    ];

    protected $hidden = [
	   'password',
	   'remember_token'
	];

	protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
  	{
    	return $this->getKey();
  	}

  	public function getJWTCustomClaims()
  	{
      return [];
  	}

  	protected static function boot()
	  {
	    parent::boot();
	    
	    static::saved(function ($model) {
	      if( $model->isDirty('email') ) {
	        $model->setAttribute('email_verified_at', null);
	        $model->sendEmailVerificationNotification();
	      }

	    // VerifyEmail::toMailUsing(function ($notifiable,$url){
	     //        $mail = new MailMessage;
	     //        $mail->subject('Welcome!');
	     //        $mail->markdown('emails.verify-email', ['url' => $url]);
	     //        return $mail;
	     //    });
		});
	}

	public function sendPasswordResetNotification($token){

	    $data = [
	        $this->email
	    ];

	    Mail::send('reset', [
	        'fullname'      => $this->name,
	        'reset_url'     => "https://demo.development.modena.co.id/password/reset/". $token ."&email=". $this->email
	        // route('user.password.reset', ['token' => $token, 'email' => $this->email]),
	    ], function($message) use($data){
	        $message->subject('Reset Password Request');
	        $message->to($data[0]);
	    });
	}

}
<?php
namespace App\Notifications;
// namespace Illuminate\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Tokens;


class VerifyEmail extends Notification
{
    // use Queueable;
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;
    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //cara laen kirim email template

        /*VerifyEmail::toMailUsing(function ($notifiable,$url){
            $mail = new MailMessage;
            $mail->subject('Welcome!');
            $mail->markdown('verify', ['url' => $url]);
            return $mail;
        });*/

        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }
        return (new MailMessage)
            //tambahan untuk merubah template email verifikasi, dari y
            ->markdown('verify', ['url' => "email/verify/". $verificationUrl,'name' => $notifiable['name']])
            //sampe sini
            ->subject('Thank you for registering to MODENA, please verify your account')
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('If you did not create an account, no further action is required.'));
    }
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // return URL::temporarySignedRoute(
        //     'verification.verify',
        //     Carbon::now()->addMinutes(60),
        //     [
        //         'id' => $notifiable->getKey(),
        //         'hash' => sha1($notifiable->getEmailForVerification()),
        //     ]
        // );
        
        $token = JWTAuth::fromUser($notifiable);
        $store_token = Tokens::create([
            'id_user' => $notifiable->getKey(),
            'token' => $token
        ]);
        return $notifiable->getKey() . "?token=". $token;
        // return route('email.verify', ['token' => $token], false);
    }
    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
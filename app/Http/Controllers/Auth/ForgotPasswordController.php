<?php

namespace App\Http\Controllers\Auth;

use App\BlockedUser;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function showLinkRequestForm()
    {
        // dd('ddf');
        $value = Cookie::get('firewall');
        // return $value;
		if($value == ''){
			$ds = '|';
			$userIp = request()->ip(); // 127.0.0.1
			$generatingTime = time();
			$randomNumber = rand();
			$firewallString = $userIp.$ds.$generatingTime.$ds.$randomNumber;
            $lifeTimeInMinute =  43800; // 1 month in minute
            Cookie::queue('firewall',$firewallString,$lifeTimeInMinute);
            return view('auth.passwords.email');
        }
        $block_user = BlockedUser::where('token',$value)->first();
        if(isset($block_user)){
            if($block_user->is_blocked){
                $now = date('Y-m-d H:i:s',Carbon::now()->timestamp);
                $expires = date('Y-m-d H:i:s',strtotime('+30 minutes',strtotime($block_user->created_at)));
                // dd($now,$expires);
                if($now > $expires){
                    $block_user->delete();
                    return view('auth.passwords.email');
                }
                else{
                    return abort(403);
                }
            }
            else{
                return view('auth.passwords.email');
            }
        }
        else{
            return view('auth.passwords.email');
        }
    }

    private function isBlocked($data){
        $isBlocked  = false;
        if(isset($data) && !empty($data)){
            if($data->is_blocked){
                $now = date('Y-m-d H:i:s',Carbon::now()->timestamp);
                $expires = date('Y-m-d H:i:s',strtotime('+30 minutes',strtotime($data->created_at)));
                if($now <= $expires){
                    $isBlocked = true;
                }
            }
        }

        return $isBlocked ;
    }

    private function isvalidUser($data){
        $valid = true;
        if(empty($data) || !$data->is_active){
            $valid = false;
        }

        return $valid;

    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $value = Cookie::get('firewall');
        // return [$value,$request->email];
        if($value == ''){
            return abort(403);
        }
        $tokenData = explode('|',$value);
        if(count($tokenData) !=3){
            return abort(403);
        }

        $block_user = BlockedUser::where('token',$value)->first();

        $isBlocked = $this->isBlocked($block_user);

        if($isBlocked){
            return abort(403);
        }
        $email = $request->email;
        $user = User::where('email',$email)->first();

        if(!$this->isvalidUser($user)){
           if(empty($block_user)){
            BlockedUser::create([
                'token'=>$value,
                'counter'=>1,
                'is_blocked'=>0
            ]);
           }else{
                $now = date('Y-m-d H:i:s',Carbon::now()->timestamp);
                $expires = date('Y-m-d H:i:s',strtotime('+30 minutes',strtotime($block_user->created_at)));
                // dd($now,$expires);
                if($now > $expires){
                    $block_user->counter = 1;
                    $block_user->created_at = Carbon::now();
                    $block_user->is_blocked = 0;
                }else{
                    $block_user->counter+=1;
                }
                if($block_user->counter > 9){
                    $block_user->is_blocked = 1;
                    $block_user->counter = 1;
                }
                $block_user->save();
           }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }
}

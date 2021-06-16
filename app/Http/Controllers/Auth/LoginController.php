<?php

namespace App\Http\Controllers\Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;//, ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    
    // protected $redirectTo = '/dashboard/home';
    /**
     * Set how many failed logins are allowed before being locked out.
     */
    public $maxAttempts = 5;

    /**
     * Set how many seconds a lockout will last.
     */
    public $decayMinutes = 30;

    public function login(Request $request)
    {
        $input = $request->all();
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
        $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            createUserActivity($request, 'ログイン', 'システムにログインしました', '一般的な', null);
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        if($this->rateLimiter->retriesLeft($this->throttleKey($request), $this->maxAttempts) < 1){
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user) {
        User::where('id', $user->id)->update(array('last_login' => date("Y-m-d H:i:s")));
        if (!$user->is_active) {
            auth()->logout();
            return back()->with('active', trans('user.inactive_user'));
        }
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('user.activities');
        } else if (auth()->user()->hasRole('operator')) {
            return redirect()->route('seller.list');
        } else if (auth()->user()->hasRole('seller')) {
            return redirect()->route('sellerProductList');
        }else if (auth()->user()->hasRole('vendor')) {
            return redirect()->route('localvendorProductList');
        } else {
            return redirect()->route('buyer.top');
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RateLimiter $rateLimiter)
    {
        $this->middleware('permission:account-unblock', ['only' => ['blockedUserList', 'unblockUser']]);
        $this->rateLimiter = $rateLimiter;
        $this->middleware('guest')->except(['logout','unblockUser',
        'blockedUserList']);
        $this->username = $this->findUsername();
    }

    /**
    * Get the login username to be used by the controller.
    *
    * @return string
    */
    public function findUsername()
    {
        $login = request()->input('email');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }
    /**
    * Get username property.
    *
    * @return string
    */
    public function username()
    {
        return $this->username;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $attempts = $this->rateLimiter->retriesLeft($this->throttleKey($request),   $this->maxAttempts);
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed').' '.trans('auth.login_attempt', ['attempt'=>$attempts])],
        ]);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        
        $this->guard()->logout();

        $request->session()->invalidate();
        createUserActivity($request, 'ログアウト', 'システムからログアウトしました', '一般的な', $user->name.'<'.$user->email.'>');

        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username()));
    }

    public function unblockUser(Request $request) {
        $target = $request->email;
        $this->limiter()->clear($target);
        $data = [];
        $data['status'] = 200;
        $data['message'] = "User Unblocked Successfully";
        return $data;
    }

    public function blockedUserList(Request $request) {
        $users = User::all();
        $result = [];
        foreach($users as $user) {
            if($this->limiter()->tooManyAttempts($user->email, $this->maxAttempts())){
                array_push($result,$user);
            }
        }
        if($request->ajax()) {
            return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('action-btn', function($row) {
                $btn = '<a href="javascript:void(0)" id="deleteData"  data-toggle="tooltip"  data-id="'.$row->email.'" data-original-title="Delete" class="btn btn-dt-delete">ロック解除</a>';
                return $btn;
            })
            ->rawColumns(['action-btn'])
            ->make(true);
        }
        return view('admin.users.block-user')->with([
            'users'=>$result
        ]);
    }
}

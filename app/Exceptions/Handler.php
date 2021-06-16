<?php

namespace App\Exceptions;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            // abort('401');
            $error= ['要求されたページはこのアカウントでは表示できません。'];
            if (auth()->user()->hasRole('admin')){
                return redirect()->route('user.activities')->withErrors($error);    
            }else if (auth()->user()->hasRole('operator')){
                return redirect()->route('seller.list')->withErrors($error);    
            } else if(auth()->user()->hasRole('seller')){
                return redirect()->route('sellerProductList')->withErrors($error);
            } else if(auth()->user()->hasRole('buyer')){
                return redirect()->route('buyer.top')->withErrors($error);
            }else{
                return abort(401);
            }
        }

        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->route('login');
        }
     
        return parent::render($request, $exception);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;
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
	
	
    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('username')+['is_active'=>1];
    }
	
	
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {   
		if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'username' => [trans($response)],
            ]);
        }

        return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => trans($response)]);
    }	
	
	public function sendResetLinkEmail(Request $request)
	{

        $this->validate($request, ['username' => 'required'], ['username.required' => 'Please enter your username.']);

         $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        if ($response === Password::RESET_LINK_SENT) {
            return back()->with('status', trans($response));
        }

        return back()->withErrors(
            ['username' => trans($response)]
        );
	}	
	
}

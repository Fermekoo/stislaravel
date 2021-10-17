<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\AuthRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authRepo;
    public function __construct(AuthRepo $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'      => 'required',
            'password'      => 'required',
            'rememberMe'    => 'nullable|boolean'
        ]);

        if($validator->fails()){
            return redirect()->route('login')->withErrors($validator)->with('error', $validator->getMessageBag()->first());
        }

        try {

            $this->authRepo->login($request);

        } catch (\Exception $e) {

            return redirect()->route('login')->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('login')->with('success', 'Terima kasih');
    }
}

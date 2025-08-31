<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login-bpkp');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'min:6|required',
        ]);
        if ($validator->fails()) {
            return redirect('login')->withInput()->withErrors($validator);
        }
        $user = User::where('username', $request->username)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->status == 1) {
                    if ($user->hasRole('admin')) {
                        Auth::login($user);
                        return redirect()->route('dashboard')->with('success','Welcome back! '.$user->username);
                    } elseif ($user->hasRole('inspektorat')) {
                        Auth::login($user);
                        return redirect()->route('atasan.dashboard')->with('success','Welcome back! '.$user->username);
                    } elseif ($user->hasRole('pegawai')) {
                        Auth::login($user);
                        return redirect()->route('pegawai.dashboard')->with('success','Welcome back! '.$user->username);
                    } 
                }
                Auth::logout();
                return redirect('/')->with('status', 'Status anda tidak aktif');
            }
        }
        // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember'); // Check if "Remember Me" is checked

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status == 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput()->withErrors([
                    'username' => 'Your account is inactive. Please contact manager.'
                ]);
            }

            // If "Remember Me" is checked, store username and password in cookies
            if ($remember) {
                $minutes = 60 * 24 * 30; // Store for 30 days
                Cookie::queue('remember_username', $request->username, $minutes);
                Cookie::queue('remember_password', $request->password, $minutes); // Consider encrypting this
            }
            if (!$user->user_lang) {
                $user->update(['user_lang' => 'kh']);
                session(['user_lang' => 'kh']);
                app()->setLocale('kh');
            } else {
                // Use existing user language preference
                session(['user_lang' => $user->user_lang]);
                app()->setLocale($user->user_lang);
            }
            $request->session()->regenerate();
            // dd(auth()->user()->role_id);
            if(auth()->user()->role_id == AppHelper::USER_EMPLOYEE && auth()->user()->type == 1){
                return redirect()->route('report.index')
                    ->with('success', 'Welcome to CRM system.')
                    ->with('show_popup', true);
            }else{
                return redirect()->route('dashboard.index')
                    ->with('success', 'Welcome to AdminPanel.')
                    ->with('show_popup', true);
            }
            
        }

        return back()->withInput()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function forgetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        return view('backend.user.forget-password', compact('user'));
    }

    public function forgetPasswordPost(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $superAdmin = User::where('email', $request->email)
            ->where('role_id', AppHelper::USER_SUPER_ADMIN)
            ->first();

        if (!$superAdmin) {
            return redirect()->back()->with("error", "Email doesn't match a super admin account.");
        }

        $user = User::findOrFail($id);

        $user->password = Hash::make('123456');
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Password reset successfully.');
    }


    // public function forgetPasswordPost(Request $request)
    // {
    //     $request->validate([
    //         'email' => "required|email|exists:users",
    //     ]);

    //     $token = Str::random(16);

    //     DB::table('password_reset_tokens')->insert([
    //         'email' => $request->email,
    //         'token' => $token,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     Mail::send("backend.email.forget-password", ['token' => $token], function ($message) use ($request) {
    //         $message->to($request->email);
    //         $message->subject("Reset Password");
    //     });

    //     return redirect()->to(route('forget.password'))->with('success', 'We have send an email to reset password');
    // }

    // public function resetPassword($token)
    // {
    //     return view('backend.user.new-password', compact('token'));
    // }

    // public function resetPasswordPost(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:users',
    //         'password' => 'required|string|min:6',
    //         'password_confirmation' => 'required|same:password',
    //     ]);


    //     $updatePassword = DB::table('password_reset_tokens')
    //         ->where([
    //             'email' => $request->email,
    //             'token' => $request->token,
    //         ])->first();

    //     if (!$updatePassword) {
    //         return redirect()->to(route('reset.password'))->with('error', 'Invalid');
    //     }

    //     User::where('email', $request->email)
    //         ->update(['password' => Hash::make($request->password)]);

    //     DB::table('password_reset_tokens')
    //         ->where(['email' => $request->email])
    //         ->delete();

    //     return redirect()->to(route('login'))->with('succcess', 'Password reset successfully');
    // }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}

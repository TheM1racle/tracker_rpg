<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()    { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(Request $r)
    {
        $creds = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($creds, $r->boolean('remember'))) {
            return back()->withErrors(['email' => 'Неверный email или пароль'])->onlyInput('email');
        }
        $r->session()->regenerate();
        return redirect()->intended('/');
    }

    public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        Auth::login(User::create($data));
        $r->session()->regenerate();
        return redirect('/');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin() { return view('login'); }

    public function doLogin(Request $r)
    {
        $r->validate(['username'=>'required', 'password'=>'required']);

        $u = DB::table('users')
            ->where('username', $r->username)
            ->orWhere('email', $r->username)
            ->first();

        if (!$u || !Hash::check($r->password, $u->password)) {
            return back()->withErrors(['username' => 'User/Password salah'])->withInput();
        }

        Session::put('uid', $u->id);
        Session::put('uname', $u->username ?? $u->email ?? $u->name);
        return redirect()->intended(route('beranda'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}

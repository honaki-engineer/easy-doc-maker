<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GuestLoginController extends Controller
{
    public function login(Request $request)
    {
        // 🔐 トークン検証（任意の文字列）
        if ($request->token !== config('app.guest_token')) {
            abort(403, '不正なアクセスです。');
        }

        // ✅ ゲストユーザーを取得 or 作成
        $guestUser = User::firstOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name' => 'ゲスト',
                'password' => bcrypt('guestpassword'), // 初回だけ実行
            ]
        );

        Auth::login($guestUser);

        return redirect('/'); // 任意のページにリダイレクト
    }
}

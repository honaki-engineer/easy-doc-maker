@component('mail::message')
# パスワード再設定のご案内

{{ $user->name ?? 'お客' }}様、こんにちは！

パスワード再設定のリクエストを受け付けました。

@component('mail::button', ['url' => $resetUrl])
パスワードを再設定する
@endcomponent

このリンクは60分で無効になります。<br>
心当たりがない場合は、このメールを無視してください。

@endcomponent

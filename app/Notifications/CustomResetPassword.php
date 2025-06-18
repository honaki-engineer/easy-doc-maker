<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable; // この通知を「キュー処理(非同期)」で送信できるようにする。

    public $token; // 再設定用リンクに必要な「トークン」をインスタンスに保持する変数。

    public function __construct($token) // new CustomResetPassword($token)で呼び出されたときに、トークンを $this->token にセットして保持。
    {
        $this->token = $token;
    }

    public function via($notifiable) // この通知は「メール(mail)」で送信することを指定
    {
        return ['mail'];
    }

    // メールの中身を定義
    public function toMail($notifiable)
    {
        // $resetUrl：トークンとメールアドレスを使ってパスワード再設定用リンクを生成。
        $resetUrl = url("/reset-password/{$this->token}?email={$notifiable->getEmailForPasswordReset()}"); // $notifiable：通知される「ユーザー情報」などが入ってる。

        // メールの送信内容
        return (new MailMessage) // (new MailMessage)：メール送信用オブジェクトを作成。
            ->subject('【パスワード再設定】のご案内') // ->subject(...)：メールの件名。
            ->markdown('emails.reset-password', [ // ->markdown(...)：Markdownテンプレートを使ってメールを構成。
                'resetUrl' => $resetUrl, // 第一引数：ビューのパス(resources/views/emails/reset-password.blade.php)
                'user' => $notifiable, // 第二引数：そのビューに渡す変数($resetUrl と user)
            ]);
    }
}

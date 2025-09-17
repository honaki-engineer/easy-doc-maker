# Easy Doc Maker（領収書作成アプリ） ※デモ用

## 概要

高級お弁当店からの依頼により開発した、**クライアント1社専用**の Laravel 製 Web アプリです。  
取引先や支払い情報を入力するだけで、Tailwind CSS で整えた**美しい領収書を高精度**に PDF 出力できます。  
ZIP 一括ダウンロードや印刷（単体・一括）機能も搭載し、**領収書発行業務を大幅に効率化**します。  
  
※ 実運用は店舗専用の URL、**デモは店舗専用とは別の URL** で公開しています（機能・構成は同一、データはダミー、Githubリポジトリは別）。  
  
※ 本アプリはクライアント（高級弁当店）の業務フローに合わせてPC利用を前提に設計されています。  
  スマートフォンでは表示が大きく崩れるため、**PCまたはタブレットでのご利用**を推奨します。

---

## サイト

- アプリ  
  https://easy-doc-maker.akkun1114.com/  
- ゲストログイン（今すぐ試せます）  
  https://easy-doc-maker.akkun1114.com/guest-login?token=guest123  

### ゲストログイン情報
- メールアドレス：不要
- パスワード：不要

上記のURLをクリックするだけで、ゲストログインが完了します。

---

## 目次

- [概要](#概要)
- [サイト](#サイト)
- [使用技術](#使用技術)
- [主な機能](#主な機能)
- [セットアップ手順(開発環境)](#セットアップ手順開発環境)
- [ディレクトリ構成](#ディレクトリ構成)
- [本番環境の注意点](#本番環境の注意点)
  
---

## 使用技術

- **フロントエンド**：HTML / JavaScript / Tailwind CSS
- **バックエンド**：PHP 8.x（開発: 8.2.27 / 本番: 8.2.29） / Laravel 9.52.20  
- **データベース**：MySQL 8.0（開発） / MariaDB 10.5（本番・MySQL互換）  
- **インフラ・環境**：MAMP / ConoHa VPS（AlmaLinux 9.5 / Apache） / macOS Sequoia 15.3.1  
- **ビルド環境**：Node.js 24.4.0（開発） / Node.js 20.19.4（本番: ConoHa VPS / NodeSource導入） / Composer 2.8.x（開発: 2.8.4 / 本番: 2.8.10）  
- **開発ツール**：VSCode / Git / GitHub / phpMyAdmin  

---

## 主な機能
### 開発者目線

- **認証/認可**：Breeze、全ルート `auth` / 取得は本人スコープ固定   
- **領収書管理**：作成・閲覧・削除（編集不可：証跡保持のため） / 検索  
- **ドキュメント出力**：PDF（A4 高精度：Browsershot × Tailwind）  
- **PDF配布**：個別ダウンロード / ZIP 一括ダウンロード / 印刷（単体・一括）  
- **マスタ自動登録**：ブランド、お弁当、支払方法を登録時に候補化  
- **自動クリーンアップ**：生成した PDF を 1 時間後に自動削除（スケジューラ）  
- **400〜503**：カスタムエラーページ  
- **その他**：バリデーション / 入力保持（old関数＆セッション） / バリデーションエラーメッセージ表示 / ページネーション  

---

### ユーザー目線
#### 区分別 機能対応表

| 機能                                       | 非ログインユーザー | 管理ユーザー |
| ----------------------------------------- | --------------- | ------ |
| ログイン                                    | -               | ●      |
| パスワード再設定                             | ●                | ●      |
| ゲストログイン（1クリック）                    | ●                | -      |
| 領収書の一覧・詳細表示                        | -                | ●      |
| 領収書の削除                                | -                | ●      |
| 「日付 × フリー検索」による領収書検索           | -                | ●      |
| 領収書のダウンロード / 印刷（詳細ページ）        | -                 | ●      |
| 領収書の一括ダウンロード / 一括印刷（一覧ページ） | -                 | ●      |
| 自社情報の詳細表示                           | -                 | ●      |
| 自社情報の更新                               | -                 | ●      |
| お弁当、ブランドの一覧表示                     | -                 | ●      |
| お弁当、ブランドの削除                        | -                 | ●      |
| 「フリー検索」によるお弁当、ブランド検索         | -                 | ●      |
| プロフィール編集                             | -                  | ●      |

---

## セットアップ手順（開発環境）

1. リポジトリをクローン
```bash
git clone https://github.com/honaki-engineer/easy-doc-maker.git
cd easy-doc-maker
```
2. 環境変数を設定
```bash
cp .env.example .env
```
.env の `DB_` 各項目などは、開発環境に応じて適宜変更してください。  
- [.env 設定例（開発環境）](#env-設定例開発環境)
3. PHPパッケージをインストール
```bash
composer install
```
4. アプリケーションキーを生成
```bash
php artisan key:generate
```
5. DBマイグレーション & 初期データ投入
```bash
php artisan migrate --seed
```
6. フロントエンドビルド（Tailwind/Vite 使用時）
```bash
npm install # 時間がかかります
npm run dev
```
7. 初期画像作成（+ ストレージリンク作成）
```bash
chmod +x setup.sh
./setup.sh
```
8. サーバー起動
```bash
php artisan serve
```
9. PDF ダウンロード機能の用意
- 単体ダウンロード  
  https://qiita.com/honaki/items/0bc4dddcc373a25a3f13
- 一括ダウンドード  
  https://qiita.com/honaki/items/84bcfea2eac48ce2e5b2
10. 印刷機能の用意
- 単体印刷  
  https://qiita.com/honaki/items/c81086d5ce26865b0b94
- 一括印刷  
  https://qiita.com/honaki/items/09845c06bbb181cdfeb1

---

### .env 設定例（開発環境）
  
```env
APP_NAME=領収書作成アプリ
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mailpit を使う場合
MAIL_MAILER=smtp
MAIL_HOST=localhost # MAMP の場合
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Browsershot（PDF・印刷）
# 各自の環境で実際のパスを確認して置き換えてください（README のセットアップ手順 9 参照）
NODE_BINARY_PATH=/path/to/node            # ← `which node`
NODE_INCLUDE_PATH=/path/to/node/include   # ← `dirname $(which node)`
CHROME_PATH="/path/to/chrome-or-chromium" # ← `ls /Applications/Chromium.app/Contents/MacOS/Chromium`

# ゲストログイン
GUEST_LOGIN_TOKEN=guest123    # ゲストログイントークン
GUEST_PASSWORD=guestpassword  # ゲストログインのパスワード
GUEST_EMAIL=guest@example.com # ゲストログインのメールアドレス
```

---

## ディレクトリ構成

```txt
easy-doc-maker/
├── app/
│   ├── Console/
│   │   ├── Commands/DeleteOldPdfs.php    # 一時ファイルの自動削除        
│   │   └── Kernel.php                    # Artisanコマンドの登録＆スケジュール定義
│   ├── Http/
│   │   ├── Controllers/                  # 各種コントローラ
│   │   └── Requests/                     # フォームリクエスト
│   ├── Models/                           # Eloquent モデル
│   ├── Notifications/                    # カスタム通知（パスワード再設定メールのカスタム）
│   └── Services/                         # サービスクラス
├── config/
│   ├── app.php                           # アプリ全体設定 + ゲストログインENV（guest_token 等）
│   └── browsershot.php                   # browsershotENV（chrome_path 等）
├── database/
│   ├── migrations/                       # マイグレーションファイル
│   └── seeders/                          # 初期データ投入用
├── lang/
│   └── ja/                               # バリデーションエラーの日本語化など
├── public/
│   ├── index.php                         # エントリーポイント
│   └── storage -> ../storage/app/public  # storage:link のシンボリックリンク
├── resources/
│   ├── css/                              # Tailwind CSS定義
│   ├── js/                               # JavaScriptエントリーポイント
│   └── views/                            # Bladeテンプレート
├── routes/
│   └── web.php                           # ルーティング設定
├── setup-assets/                         # 初期画像格納
├── storage/
│   └── app/public/
│       ├── images/                       # setup-assets/ の保存先
│       └── tmp/                          # 一時的な領収書PDFの保管場所
├── .env.example                          # 環境変数テンプレート
├── composer.json                         # PHPパッケージ管理ファイル
├── package.json                          # Node.jsパッケージ管理ファイル
├── README.md
├── setup.sh                              # 初期画像のセットアップ 
├── tailwind.config.js                    # Tailwind CSS 設定
└── vite.config.js                        # Vite 設定
```

---

## 本番環境の注意点

ConoHa VPS 上で Laravel アプリを本番公開する際の詳細な手順は、以下の記事にまとめています：

- ① 【DNSレコード作成前】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/b0060303c21682c0e8e5

- ② 【DNSレコード】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/11343be97c3cee2c3102

- ③ 【DNSレコード後】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/834b4fe730441db2d2fa

---

### PDF ダウンロード / 印刷機能 の本番対応
README の[セットアップ手順 9・10](#セットアップ手順開発環境) の本番対応については、以下の記事にまとめています：  

- 【conohaVPS環境】Browsershot 本番運用  
  https://qiita.com/honaki/items/6fc2285d7f1f476486d8
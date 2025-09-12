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

---

## 目次

- [概要](#概要)
- [サイト](#サイト)
- [使用技術](#使用技術)
- [主な機能](#主な機能)
- [クイックスタート](#クイックスタート)
- [ディレクトリ構成](#ディレクトリ構成)
- [本番環境の注意点](#本番環境の注意点)
  
---

## 使用技術

- **フロントエンド**：HTML / JavaScript / Tailwind CSS
- **バックエンド**：PHP 8.x（開発: 8.2.27 / 本番: 8.2.29） / Laravel 9.52.20  
- **データベース**：MySQL 8.0（開発） / MariaDB 10.5（本番・MySQL互換）  
- **インフラ・環境**：MAMP / ConoHa VPS（AlmaLinux 9.5 / Apache） / macOS Sequoia 15.3.1  
- **ビルド環境**：Node.js 22.17.0（開発） / Node.js 20.19.4（本番: ConoHa VPS / NodeSource導入） / Composer 2.8.x（開発: 2.8.4 / 本番: 2.8.10）  
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

## セットアップに必要な環境

- PHP 8.2 以上
- Composer 2.8.x
- DB：MySQL 8.0 もしくは MariaDB 10.5（MySQL互換）
- Node.js（Tailwind をビルド）
- Git（クローンする場合）

---

## クイックスタート

1. リポジトリをクローン
```bash
git clone https://github.com/honaki-engineer/easy-doc-maker.git
cd easy-doc-maker
```
2. 環境変数を設定
```bash
cp .env.example .env
```
.env の `DB_` 各項目などは、ConoHa VPS または開発の環境に応じて適宜変更してください。  
- [.env 設定例（開発用）](#env-設定例開発用)
- [.env 設定例（本番用）](#env-設定例本番用)
3. PHPパッケージをインストール
```bash
# 開発
composer install

# 本番
composer install --no-dev --optimize-autoloader
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

# 開発
npm run dev

# 本番
npm run build
```
7. 初期画像作成（+ ストレージリンク作成）
```bash
chmod +x setup.sh
./setup.sh
```
8. サーバー起動（開発用のみ）
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
11. PDF ダウンロード機能 ＆ 印刷機能の準備（本番のみ）
  https://qiita.com/honaki/items/6fc2285d7f1f476486d8


### .env 設定例（開発用）
  
```env
APP_NAME=領収書作成アプリ
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easy_doc_maker
DB_USERNAME=root
DB_PASSWORD=root

# Mailpit を使う場合
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Browsershot（PDF・印刷）
# 各自の環境で実際のパスを確認して置き換えてください（↓確認方法あり）
NODE_BINARY_PATH=/opt/homebrew/bin/node                           # ← `which node`
NODE_INCLUDE_PATH=/opt/homebrew/bin                               # ← `dirname $(which node)`
CHROME_PATH="/Applications/Chromium.app/Contents/MacOS/Chromium"  # ← `ls /Applications/Chromium.app/Contents/MacOS/Chromium`

# ゲストログイン
GUEST_LOGIN_TOKEN=guest123 # ゲストログイントークン
GUEST_PASSWORD=guestpassword # ゲストログインのパスワード
GUEST_EMAIL=guest@example.com # ゲストログインのメールアドレス
```

### .env 設定例（本番用）

```env
APP_NAME=領収書作成アプリ
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=（本番用 データベース）
DB_USERNAME=（本番用 ユーザー）
DB_PASSWORD=（本番用 DBuser パスワード）

# Gmail の場合
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=（使用するメールアドレス）
MAIL_PASSWORD=（16桁のアプリパスワード）
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=（使用するメールアドレス）
MAIL_FROM_NAME="${APP_NAME}"

# Browsershot（PDF・印刷）
# 各自の環境で実際のパスを確認して置き換えてください（↓確認方法あり）
NODE_BINARY_PATH=/opt/homebrew/bin/node     # ← `which node`
NODE_INCLUDE_PATH=/opt/homebrew/bin         # ← `dirname $(which node)`
CHROME_PATH="/usr/bin/chromium-browser"     # ← `which chromium-browser`

# 本番のみ：Browsershot が一時ファイルを置くディレクトリ
BROWSERSHOT_HOME="/var/www/.browsershot"

# ゲストログイン
GUEST_LOGIN_TOKEN=guest123 # ゲストログイントークン
GUEST_PASSWORD=guestpassword # ゲストログインのパスワード
GUEST_EMAIL=guest@example.com # ゲストログインのメールアドレス
```

---

## ディレクトリ構成

```txt
easy-doc-maker/
├── app/
│   ├── Console/
│   │   ├── Commands/DeleteOldPdfs.php  # 一時ファイルの自動削除        
│   │   └── Kernel.php                  # Artisanコマンドの登録＆スケジュール定義
│   ├── Http/
│   │   ├── Controllers/                # 各種コントローラ
│   │   └── Requests/                   # フォームリクエスト
│   ├── Models/                         # Eloquent モデル
│   ├── Notifications/                  # カスタム通知（パスワード再設定メールのカスタム）
│   └── Services/                       # サービスクラス
├── config/
│   ├── app.php                         # アプリ全体設定 + ゲストログインENV（guest_token 等）
│   └── browsershot.php                 # browsershotENV（chrome_path 等）
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
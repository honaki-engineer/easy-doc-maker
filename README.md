# Easy Doc Maker (領収書作成アプリ)

## 概要

Easy Doc Maker (領収書作成アプリ) は、高級お弁当店からの依頼により開発した、**特定クライアント専用**の Laravel 製 Web アプリです。  
取引先や支払い情報を入力するだけで、Tailwind CSS で整えた**美しい領収書を高精度**に PDF 出力できます。  
ZIP一括ダウンロードやワンクリック印刷機能も搭載し、**領収書発行業務を大幅に効率化**します。

---

## サイト

🔗 （※必要に応じてURLを記入）

---

## 目次

- [サイト](#サイト)
- [使用技術](#使用技術)
- [主な機能](#主な機能)
- [セットアップに必要な環境](#セットアップに必要な環境)
- [セットアップ手順](#セットアップ手順)
- [ディレクトリ構成](#ディレクトリ構成)
- [開発環境](#開発環境)
- [本番環境の注意点](#本番環境の注意点)
  
---

## 使用技術

- **フロントエンド**：HTML / JavaScript / Tailwind CSS
- **バックエンド**：PHP 8.2 / Laravel 9.x  
- **データベース**：MySQL 8.0 (ローカル) / MariaDB 10.5 (conohaVPS・MySQL互換)  
- **インフラ・環境**：MAMP / macOS Sequoia 15.3.1 / conohaVPS / AlmaLinux 9.5 / Apache
- **ビルド環境**：Node.js 22.x (ローカル) / Node.js 20.x (ConoHa VPS / NodeSource導入) / Composer 2.x  
- **開発ツール**：VSCode / Git / GitHub / phpMyAdmin  

---

## 主な機能
### 開発者目線

- **ユーザー認証(Laravel Breeze)**：ログイン / ログアウト / パスワード再発行  
- **領収書管理**：作成・削除・一覧表示・詳細表示・PDF出力・一括ZIP・印刷  
- **自社情報管理**：領収書テンプレートに反映される設定情報の編集  
- **ブランド/お弁当管理**：削除・一覧表示 + 領収書作成時の自動登録・候補化に対応  
- **支払い方法の管理**：領収書作成時の自動登録・候補化に対応  
- **バリデーション対応**：入力保持（`old()`） + エラーメッセージ表示  
- **一覧画面**：ページネーション / 検索フォームによる絞り込み対応  
- **PDF機能**：Browsershot + Tailwind による A4 高精度PDF生成  
- **一括PDF**：複数PDFをZIPで出力 or 結合して1ファイル化 (印刷中継画面付き)  
- **カスタムエラーページ対応**：400〜503に独自UIを実装  
- **プロフィール編集**：編集  

### ユーザー目線
#### 区分別 機能対応表

| 機能                  | ページ分類  | 非ログインユーザー | 管理ユーザー |
| ------------------- | ------ | --------- | ------ |
| ログイン                | 管理者ページ | -         | ●      |
| パスワード再設定            | 管理者ページ | -         | ●      |
| 領収書の一覧・詳細表示         | 管理者ページ | -         | ●      |
| 領収書の作成・削除・PDF出力     | 管理者ページ | -         | ●      |
| 領収書の一括ZIP出力 ・ 一括印刷  | 管理者ページ | -         | ●      |
| ブランド/お弁当情報の一覧・削除 | 管理者ページ | -         | ●      |
| 領収書作成時の支払い方法/ブランド/お弁当情報の自動登録・候補化 | 管理者ページ | -         | ●      |
| 検索 (領収書一覧、お弁当一覧) | 管理者ページ | -         | ●      |
| 自社情報の編集             | 管理者ページ | -         | ●      |
| プロフィール編集               | 管理者ページ | -         | ●      |

---

## セットアップに必要な環境

- PHP 8.2 以上
- Laravel 9.x
- Composer 2.x
- MySQL (ローカル or conohaVPS上で構成)
- Node.js (Tailwind CSS のビルドに使用)
- Git（クローン / バージョン管理に使用）

.env の `DB_` 各項目などは、conohaVPS またはローカルの環境に応じて適宜変更してください。

### .env 設定例（ローカル開発用）

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easy_doc_makers
DB_USERNAME=root
DB_PASSWORD=
```

---

## セットアップ手順

1. リポジトリをクローン
```bash
git clone https://github.com/HondaAkihito/easy_doc_maker.git
cd easy_doc_maker
```
2. 環境変数を設定
```bash
cp .env.example .env
```
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
6. フロントエンドビルド (Tailwind/Vite 使用時)
```bash
npm install
npm run dev  # 開発環境用
npm run build  # 本番環境用
```
7. サーバー起動 (ローカル開発用)
```bash
php artisan serve
```

---

## ディレクトリ構成

```txt
easy_doc_maker/
├── app/                     # アプリケーションロジック（モデル、コントローラ、サービスなど）
│   ├── Console/             
│   ├── Exceptions/          
│   ├── Http/
│   │   ├── Controllers/     # 各種コントローラ (認証 / 領収書 / ブランド / お弁当 / 自社情報 / プロフィール)
│   │   ├── Middleware/      # ミドルウェア定義
│   │   └── Requests/        # フォームリクエスト (バリデーション)
│   ├── Models/              # Eloquent モデル
│   ├── Notifications/       # パスワード再発行通知など
│   ├── Providers/           # 各種サービスプロバイダ
│   ├── Services/            # 業務ロジックやデータ処理を集約し、コントローラーを簡潔に保つ
│   └── View/Components/     # Bladeコンポーネント
├── bootstrap/               
│   └── cache/               
├── config/                  # 各種設定ファイル (app, database, mail 等)
├── database/
│   ├── factories/           # テストデータ生成用ファクトリ
│   ├── migrations/          # マイグレーションファイル
│   └── seeders/             # 初期データ投入
├── public/                  # 公開ディレクトリ (index.php, アセット)
├── resources/
│   ├── css/                 # Tailwind CSS定義
│   ├── js/                  # JavaScriptエントリーポイント
│   └── views/               # Bladeテンプレート
├── routes/
│   └── web.php              # ルーティング設定
├── storage/                 # ログ・セッション・ファイル保存
├── tests/                   
├── .env.example             # 環境変数のテンプレート
├── composer.json            # PHP依存管理ファイル
├── package.json             # Node依存管理ファイル
├── vite.config.js           # Vite設定ファイル
├── tailwind.config.js       # Tailwind CSS 設定
└── README.md
```

---

## 開発環境

- PHP 8.2
- Laravel 9.x
- Composer 2.x
- Node.js 22.x (ローカル開発)
- MySQL 8.0（ローカル環境）
- 推奨ブラウザ：Google Chrome 最新版

---

## 本番環境の注意点

ConohaVPS 上で Laravel アプリを本番公開する際の詳細な手順は、以下の記事にまとめています：

- ① 【DNSレコード作成前】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/b0060303c21682c0e8e5

- ② 【DNSレコード】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/11343be97c3cee2c3102

- ③ 【DNSレコード後】ConoHa VPSでLaravel + Apache + phpMyAdminを公開する手順（2025年版）  
  https://qiita.com/honaki/items/834b4fe730441db2d2fa
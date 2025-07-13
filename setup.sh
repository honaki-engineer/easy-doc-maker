#!/bin/bash

echo "📦 ロゴ画像を配置中..."

# 必要なディレクトリを作成
mkdir -p storage/app/public/images

# 画像をコピー（setup-assets → storage）
cp setup-assets/easyDocMaker.png storage/app/public/images/

# Laravelのシンボリックリンク（public/storage）
php artisan storage:link

echo "✅ ロゴ画像の配置が完了しました！"

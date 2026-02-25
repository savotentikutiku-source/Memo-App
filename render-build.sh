#!/usr/bin/env bash
# エラーが起きたら処理を止める
set -o errexit

# パッケージのインストール
composer install --optimize-autoloader --no-dev

# 古いキャッシュをクリア
php artisan config:clear
php artisan route:clear
php artisan view:clear

# データベースのテーブルを作成（自動で「はい」と答える）
php artisan migrate --force
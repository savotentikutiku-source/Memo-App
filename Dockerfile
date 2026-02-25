# PHPの環境を用意
FROM php:8.2-cli

# PostgreSQL用のツールとComposer（パッケージ管理）をインストール
RUN apt-get update && apt-get install -y libpq-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# コードをコピーしてパッケージをインストール
WORKDIR /app
COPY . .
RUN composer install --optimize-autoloader --no-dev

# 起動コマンド（指示書の実行 ＋ サーバー起動）
CMD bash -c "sh ./render-build.sh && php artisan serve --host=0.0.0.0 --port=$PORT"
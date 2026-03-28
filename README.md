# t102L-laravel-server

ラズパイと通信をやり取りするサーバです。
PHP/Laravelを使用します。
その他研修目的で各種機能を追加します。

## 作者

- 小幡 obata@mir-ai.co.jp

## 制約事項


## 対象利用者

- 教育対象者

## 構成

## 会社案内

## 事例

## 使用している主な技術

![PHP](https://img.shields.io/badge/-PHP82-777BB4.svg?logo=php&style=for-the-badge&logoColor=white)

![Laravel](https://img.shields.io/badge/Laravel-v10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

![Bootstrap](https://img.shields.io/badge/Bootstrap5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)

![Javascript](https://shields.io/badge/JavaScript-F7DF1E?logo=JavaScript&logoColor=000&style=flat-square)

![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)

![Redis](https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white)

![Httpd](https://img.shields.io/badge/-Apache-2d075e?&logo=Apache)

# ディレクトリ構成について


## 環境変数（作成中）

| 環境変数 | 説明 | サンプル値 |
|-----|-----|-----|
APP_NAME | アプリケーションの表示名。 | Laravel または My Application
APP_ENV | アプリケーションの実行環境。 | local、testing、production
APP_KEY | アプリケーションの暗号化に使用される32文字の秘密鍵。 | base64:AbCdEfG...==
APP_DEBUG | デバッグモードの有効/無効。本番環境ではfalse必須。 | true または false
APP_URL | アプリケーションのベースURL。 | http://localhost または https://myapp.com
APP_LOCALE | アプリケーションの主要なロケール（言語）。 | ja (日本語) または en (英語)
APP_FALLBACK_LOCALE | 主要ロケールに翻訳がない場合に代替となるロケール。 | en
APP_FAKER_LOCALE | シーダーやファクトリで使用するFakerライブラリのロケール。 | ja_JP または en_US
APP_MAINTENANCE_DRIVER | メンテナンスモードを制御するドライバー。 | file または cache
BCRYPT_ROUNDS | パスワードハッシュ化に使用するBcryptのラウンド数（強度）。 | 12
LOG_CHANNEL | デフォルトのログチャネル。 | stack または single
LOG_STACK | スタックチャネルに含まれるログチャネルのリスト（カンマ区切り）。 | daily,slack
LOG_DEPRECATIONS_CHANNEL | 非推奨（Deprecation）のアラートを記録するチャネル。 | null または daily
LOG_LEVEL | ログを記録する最低レベル。 | debug、info、error
DB_CONNECTION | 使用するデータベースドライバー。 | mysql、pgsql、sqlite
DB_HOST | データベースサーバーのホスト名またはIPアドレス。 | 127.0.0.1 または db_server
DB_PORT | データベースサーバーのポート番号。 | 3306 (MySQL) または 5432 (PostgreSQL)
DB_DATABASE | 接続するデータベース名。 | laravel または my_app_db
DB_USERNAME | データベース接続用のユーザー名。 | root または dbuser
DB_PASSWORD | データベース接続用のパスワード。 | password または (空欄)
SESSION_DRIVER | セッション情報を保存する方法。 | file、cookie、database、redis
SESSION_LIFETIME | セッションの有効期限（分）。 | 120
SESSION_ENCRYPT | セッションデータの暗号化を有効にするか。 | true または false
SESSION_PATH | セッションCookieのパス。 | /
SESSION_DOMAIN | セッションCookieのドメイン。サブドメイン間で共有する場合などに使用。 | null または .myapp.com
BROADCAST_CONNECTION | イベントブロードキャストに使用するドライバー。 | log、pusher、redis
FILESYSTEM_DISK | デフォルトのファイルシステムディスク。 | local、public、s3
QUEUE_CONNECTION | ジョブキューに使用するドライバー。 | sync、database、redis、sqs
CACHE_STORE | キャッシュに使用するストレージ。 | file、redis、memcached
MEMCACHED_HOST | Memcachedサーバーのホスト名。 | 127.0.0.1
REDIS_CLIENT | Redisクライアントの種類。 | phpredis または predis
REDIS_HOST | Redisサーバーのホスト名。 | 127.0.0.1
REDIS_PASSWORD | Redisサーバーのパスワード。 | null または redis_secret
REDIS_PORT | Redisサーバーのポート番号。 | 6379
MAIL_MAILER | メール送信に使用するドライバー。 | smtp、log、ses、mailgun
MAIL_SCHEME | SMTP接続の暗号化方式。 | tls または ssl
MAIL_HOST | SMTPサーバーのホスト名。 | smtp.mailgun.org
MAIL_PORT | SMTPサーバーのポート番号。 | 587 (TLS) または 465 (SSL)
MAIL_USERNAME | SMTP認証のユーザー名。 | myuser
MAIL_PASSWORD | SMTP認証のパスワード。 | secret_password
MAIL_FROM_ADDRESS | 送信元メールアドレス。 | hello@example.com
MAIL_FROM_NAME | 送信元名。 | My Application Support
AWS_ACCESS_KEY_ID | AWSサービス（S3など）のアクセスキーID。 | AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY | AWSサービス（S3など）のシークレットアクセスキー。 | wJalrXUtnFEMIK7E...
AWS_DEFAULT_REGION | AWSサービスのデフォルトリージョン。 | us-east-1 または ap-northeast-1
AWS_BUCKET | AWS S3バケット名。 | my-laravel-bucket
AWS_USE_PATH_STYLE_ENDPOINT | AWS S3でパス形式のエンドポイントを使用するか。 | FALSE
VITE_APP_NAME | Vite/フロントエンドで利用するために公開されるアプリケーション名（Vite関連）。 | My Application

## 開発時に参考にした記事

[How to Install Bootstrap 5 in Laravel 12 with Vite](https://itstuffsolutiotions.io/how-to-install-bootstrap-5-in-laravel-12-with-vite/)

[Laravel 12 Bootstrap Auth Scaffolding Tutorial Step by Step](https://itstuffsolutiotions.io/laravel-12-bootstrap-auth-scaffolding-tutorial-step-by-step/)

[Laravel 12 Install and Configure Laravel Debugbar](https://itstuffsolutiotions.io/?s=Laravel+12+Install+and+Configure+Laravel+Debugbar)

[Laravel 12 Summernote Image Upload CRUD Example](https://itstuffsolutiotions.io/laravel-12-summernote-image-upload-crud-example/)

[Laravel 12 How to Install SweetAlert2 With Vite](https://itstuffsolutiotions.io/laravel-12-how-to-install-sweetalert2-with-vite/)


## Setup

composer install

npm install

npm rund dev

rpm run build


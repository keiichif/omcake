# omcake v0.2用の修正手順

mrディレクトリでauthenticationプラグインをインストールしてください。
> composer require "cakephp/authentication:^2.0"

mrデータベースの構造を変更してください。（psqlを使って変更してください。）
> ALTER TABLE tbl_cc_and_order ADD userid varchar(16);
> CREATE TABLE users (id serial PRIMARY KEY, userid varchar(16) NOT NULL, password varchar(256) NOT NULL, is_entering_newpw boolean);

configディレクトリのファイルの修正をしてください。(configディレクトリに修正のサンプルをおいています。)
srcディレクトリのApplication.phpを修正してください。

# omcake v0.1インストール手順書

﻿サーバとなるPCにORCAをインストール（手順書は下記サイト）。
https://www.orca.med.or.jp/receipt/download/focal/focal_install_52.html

postgresの設定:
	pg_hba.conf: 
		IPv4 local connections:
			アクセスを許可するアドレスにtrustを指定。
	postgresql.conf:
		listen_addressesにアクセスを許可するアドレスを指定。

ORCAサーバPCに下記の手順でPHP、cakePHP、mrデータベースをセットアップ。

apache2をインストール。
	http://localhostでエラーがないかチェック。

php7.4以上をインストール。
-intl, -mbstring, -xml, -mysql, -pgsql, -phpdbg, -curl, -sqlite3　をインストール。

cakephp4をインストールマニュアルに従ってインストール。
	app.php:
		AppのdefaultTimezoneをAsia/Tokyoに。
		DatasourcesのdefaultのdriverをPostgres::classに。
	app_local.php:
		Datasourcesのdefaultにorcaデータベースを設定。

	http://localhost:8765でエラーがないかチェック。
　
$ sudo useradd mr
$ psql -U postgres -h localhost
postgres=# create role mr createdb login;
$ sudo -u mr createdb -lC -EUTF8 -Ttemplate0 mr

スキーマだけのmrダンプファイルをリストア。（to_zenkaku関数もセットアップされる。）
$ sudo -u mr pg_restore -x -O -d -v mr_scheme_only

mrフォルダにomcakeファイル群を配置。
OrcaApiComponent.phpのUSERNAMEとPASSWORDを修正。

postgresサーバとjma-receiptサーバが起動していることを確認の上、
mrフォルダでcakePHPサーバ起動。
$ bin/cake server -H localhost

クライエントPCのブラウザでサーバアドレスを指定して（もしくはサーバPCで
localhostを指定して）アクセス。
http:192.168.xxx.yyy:8765/acceptance-list/today
（なお、受付作業はORCAで行い、ブラウザでフェッチする仕組み。）


# omcake v0.1

小さな診療所用の電子カルテ（もどき）です。

奥村晴彦先生のtwitter

>オープンソースの電子カルテシステムで、WebベースでクライアントOSを選ばず、
>サーバは普通のLinuxで動くPHPとか、ないんだろうか。

で唐突に召喚され、それ、ウチにありますけど〜、とノコノコ出てきた次第です。

奥村先生曰く、

>もともとは、つるぎ町半田病院の電カルがランサムウェアにやられて
>使えなくなったという話から始まりました。病院にお金がなく、業者もクソで、
>もともと簡単に侵入できるような環境だったようです。
>ちゃんとした電子カルテシステムがオープンソースであれば、
>われわれもボランティアで苦境に立っている病院に導入するのをお手伝いできて、
>クソ業者にひどい状態にされることもないだろうと思った次第です。

奥村先生の問題意識に共感される方、ぜひ御協力ください。

## 動作環境等

ORCAサーバ使用が前提です。

ウチでは
ubuntu20.04  postgresql12  cakePHP4
で動いています。

* ORCAとの連携はORCA→omcakeの一方向。
* エラー処理してません！（CakePHPのデバッガが頼り^^;;）
* セキュリティはIPアドレス制限のみ。ログイン機能なし。

チケットプリンタで「受付順番チケット」を発行する機能をつけてます。（bashを呼んでるだけです。）

ショボいけど、食べてみてください。味は保証しません！

# 謝辞

* 半角から全角へ変換するpostgres用関数(https://postgresweb.com/post-2295)
を使わせてもらっています。
* 電子カルテでは変更履歴をすべて残しておく必要があります。omcakeではOpenDolphinと同じく、差分を記録しないで保存時刻の異なるデータが貯まるようにしています。そして、保存時刻が最新のレコードを普段は表示させるために「同一グループの中で最大のレコードを取得する SQL を書く」(https://www.timedia.co.jp/tech/selecting-max-record-in-group-by/)
が必須でした。これを知ったのでomcakeはできたようなものです。


# ORCA用小品

print_s-label はラベルプリンタで診察券用のID情報ラベルをプリントします。（診察券に手書きする代わりに印字したラベルを貼る。）　ORCAの組み込みユーザプログラムとして登録作業のあたりに組み込むと便利です。

ORCAは新患を登録する際、空き番号を自動で探してくれるのですが、操作間違いで大きな番号を登録してしまい、空き番号のカタマリが出来てしまうことがあります。そういう場合に search_free_num を使います。　last_free_num に適当に小さい番号を入れてから起動して下さい。

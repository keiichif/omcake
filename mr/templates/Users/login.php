<?php $this->assign('script3', $this->Html->script('cover_uncover')) ?>

<?=$this->Form->create() ?>
<?=$this->Flash->render() ?>
<?='<pre>' ?>
<?='ユーザＩＤ' ?>
<?=$this->Form->password('userid', ['id'=>'userid']) ?>
<?=$this->Form->button('表示', ['type'=>'button', 'id'=>'btn_userid', 'tabindex'=>'-1', 'onclick'=>'cover_uncover("userid", "btn_userid");']) ?>
<?='</pre>' ?>
<?='<pre>' ?>
<?='パスワード' ?>
<?=$this->Form->password('password', ['id'=>'password']) ?>
<?=$this->Form->button('表示', ['type'=>'button', 'id'=>'btn_pw', 'tabindex'=>'-1', 'onclick'=>'cover_uncover("password", "btn_pw" );']) ?>
<?='</pre>' ?>
<?='<pre>' ?>
<?='<pre>' ?>
<?=$this->Form->submit('ログイン', ['name'=>'btn_login']) ?>
<?='</pre>' ?>
<?='<pre>' ?>
<?=$this->Form->submit('パスワード変更へ', ['name'=>'btn_change_pw']) ?>
<?='</pre>' ?>
<?= $this->Form->end() ?>
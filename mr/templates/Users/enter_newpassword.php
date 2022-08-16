<?php $this->assign('script3', $this->Html->script('cover_uncover')) ?>

<?=$this->Form->create() ?>
<?=$this->Flash->render() ?>
<?='<pre>' ?>
<?='新しいパスワード ' ?>
<?=$this->Form->password('new_pw', ['id'=>'new_pw']) ?>
<?=$this->Form->button('表示', ['type'=>'button', 'id'=>'btn_display', 'onclick'=>'cover_uncover("new_pw", "btn_display");']) ?>
<?='</pre>' ?>
<?=$this->Form->submit('パスワード登録', ['name'=>'btn_register_pw']) ?>
<?='<br/>' ?>
<?=$this->Form->submit('中止', ['name'=>'btn_cancel']) ?>
<?= $this->Form->end() ?>
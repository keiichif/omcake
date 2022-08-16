<?php $this->assign('title', 'ログアウト') ?>

<main>
    <?=$this->Flash->render() ?>
    <div>ログアウトしました。</div>
    <br/>
    <div>
    <?=$this->Html->link('ログイン', ['action'=>'login']) ?>
    </div>
</main>


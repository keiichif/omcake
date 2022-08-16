<?php $this->assign('title', 'ログイン中') ?>

<main>
    <pre><?=$username ?> ログイン中。</pre>
    <?=$this->Html->link('ログアウト', ['action'=>'logout']) ?>
</main>

<?=$this->element('Mr/head', ['task' => '修正']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>
<?php $this->assign('css2', $this->Html->css('edit-mode')) ?>
<?php $this->assign('script', $this->Html->script('jquery-3.5.1.min')) ?>
<?php $this->assign('script2', $this->Html->script('check_beforeunload')) ?>

<header>
    <?=$this->element('Mr/link_from_edit_mr') ?>
    <?=$this->element('Mr/header_of_mr') ?>
</header>
<main>
    <?=$this->Form->create(null,['name'=>'edit_mr', 'type'=>'post', 'url'=>'mr/save_mr']) ?>
    <?=$this->Form->hidden('ptnumber', ['value'=>$ptnumber]) ?>
    <?=$this->Form->hidden('ac_no', ['value'=>$ac_no]) ?>
    <?=$this->Form->hidden('mrid', ['value'=>$mrid]) ?>
    <?=$this->Form->hidden('time', ['value'=>$time]) ?>
    
    <?=$this->element('Mr/mr_tables') ?>

    <?=$this->Form->end() ?>
</main>

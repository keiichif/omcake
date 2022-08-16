<?=$this->element('Mr/head', ['task' => '修正']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>
<?php $this->assign('css2', $this->Html->css('edit-mode')) ?>
<?php $this->assign('script2', $this->Html->script('chk_beforeunload_and_submit')) ?>

<header>
    <?=$this->element('Mr/links_on_edit_mr') ?>
    <?=$this->element('Mr/ptno_name_age') ?>
    <?=$this->element('Mr/recorded_time') ?>
</header>
<main>
    <?=$this->Form->create(null,['id'=>'form_mr', 'name'=>'form_mr','type'=>'post']) ?>    
    <?=$this->element('Mr/mr_tables') ?>
    <?=$this->Form->end() ?>
</main>

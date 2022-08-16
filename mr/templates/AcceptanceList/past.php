<?php $this->assign('title', '過去の受付一覧') ?>
<?php $this->assign('css', $this->Html->css('acceptance-list')) ?>

<main>
    <div>
        <?=$this->Form->create(null,['type'=>'post']) ?>
        <?=$this->Form->date( '', ['name'=>'date','value'=>h($date)]) ?>
        <?=$this->Form->button('検索')  ?>
        <?=$this->Form->end() ?>
    </div>
    <br />
    <table>
        <?=$this->element('AcceptanceList/table') ?>
    </table>
</main>
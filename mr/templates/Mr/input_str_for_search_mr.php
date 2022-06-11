<?php $ac_no_temp = ($ac_no === '') ? '' : '(' . $ac_no . ')' ?>
<?php $this->assign('title', $ac_no_temp . ' ' . $ptnumber . '__' . preg_replace('/　/',' ',$name) 
                        . '　カルテ内検索') ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>

<header>
    <div>
        <?=$ptnumber . '    ' . $kana_name . '     ' . $age . '歳' ?> 
    </div>
    <div id="name">
        <?=preg_replace('/　/',' ',$name)  ?>
    </div>
</header>               
<br />
<main>
    <div>
        <?=$this->Form->create(null, ['type'=>'post', 'url'=>['controller'=>'Mr', 'action'=>'searchMr']]) ?>
        <?=$this->Form->hidden('ptnumber', ['value'=>$ptnumber]) ?>
        <?=$this->Form->hidden('ac_no', ['value'=>$ac_no]) ?>
        <?=$this->Form->text('data_for_search') ?>
        <?=$this->Form->end() ?>
    </div>
    <br />
    <?php $table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
    <table>
        <?=$this->Html->tableHeaders($table_header) ?>
        <?=$this->Html->tableCells(['', '']) ?>
    </table>
</main>

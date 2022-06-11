<?=$this->element('Mr/head', ['task' => '新規']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>
<?php $this->assign('css2', $this->Html->css('edit-mode')) ?>
<?php $this->assign('script', $this->Html->script('jquery-3.5.1.min')) ?>
<?php $this->assign('script2', $this->Html->script('check_beforeunload')) ?>

<header>
    <?=$this->element('Mr/link_from_new_mr') ?>
    <?=$this->element('Mr/header_of_mr')  ?>
</header>
<br />
<main>
    <?=$this->Form->create(null,['name'=>'new_mr','type'=>'post', 'url'=>'/mr/save_mr']) ?>
    <?=$this->Form->hidden('ptnumber', ['value'=>$ptnumber]) ?>
    <?=$this->Form->hidden('ac_no', ['value'=>$ac_no]) ?>
    <?=$this->Form->hidden('mrid', ['value'=>null]) ?>
    <?=$this->Form->hidden('time', ['value'=>$time]) ?>

    <?=$this->element('Mr/mr_tables') ?>
    
<!--
    <table>
        <?php //$table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
        <?php //echo $this->html->tableHeaders($table_header) ?>
        <tr>
            <td  style="border: none">
                <?php //echo $this->Form->textarea('cc', ['label'=>'cc', 'value'=>'', 'cols'=>'40', 'rows'=>'18']) ?>
            </td>
            <td style="border: none">
                <?php //echo $this->Form->textarea('col_order', ['label'=>'col_order', 'value'=>'', 'cols'=>'40', 'rows'=>'18']) ?>
            </td>
        </tr>
    </table>
    <table>
        <?php //echo $this->Html->tableHeaders(['臨床メモ']) ?>
        <tr style="border: none">
            <td style="border: none">
                <?php //echo $this->Form->textarea('clin_note', ['label'=>'clin_note', 'value'=>$clin_note, 'cols'=>'84', 'rows'=>'5']) ?>
            </td>
        </tr>
    </table>
-->
    <?=$this->Form->end() ?>
</main>

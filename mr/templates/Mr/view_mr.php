<?=$this->element('Mr/head', ['task' => '閲覧']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>

<header>
<?=$this->element('Mr/link_from_view_mr') // $mridをedit_mrに渡す ?>
<?=$this->element('Mr/header_of_mr')  ?>
</header>        
<main>
<?php $table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
<?php $table_body = [preg_replace('/\n/', '<br />', h($cc)), 
                                preg_replace('/\n/', '<br />', h($col_order))] ?>
<table>
<?=$this->Html->tableHeaders($table_header) ?>
<?=$this->Html->tableCells($table_body) ?>
</table>
<br />
    
<?php $arr_temp = explode("\n", $clin_note) ?>
<?php $cnt = 1 ?>
<?php $clin_note_head = '' ?>
<?php foreach ($arr_temp as $obj) : ?>
<?php   $clin_note_head .= $obj . "\n" ?>
<?php   if (++$cnt > 5) break ?>
<?php endforeach; ?>
<?php $clin_note_head = preg_replace('/\n/', '<br />', h($clin_note_head)) ?>
<table>
<?=$this->Html->tableHeaders(['臨床メモ']) ?>
<?=$this->Html->tableCells([$clin_note_head]) ?>
</table>
</main>

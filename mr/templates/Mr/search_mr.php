<?=$this->element('Mr/head', ['task' => 'カルテ内検索']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>

<header>
    <?=$this->element('Mr/link_from_search_mr') ?>
    <?=$this->element('Mr/header_of_mr0') ?>
</header>               
<br />
<main>
<?=$this->Form->create(null, ['type'=>'post', 'url'=>['controller'=>'Mr', 'action'=>'searchMr']]) ?>
<?=$this->Form->hidden('ptnumber', ['value'=>$ptnumber]) ?>
<?=$this->Form->hidden('ac_no', ['value'=>$ac_no]) ?>
<?=$this->Form->text('data_for_search', ['placeholder' => '検索文字列...']) ?>
<?=$this->Form->end() ?>

<table>
<?php $table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
<?php echo $this->Html->tableHeaders($table_header) ?>
<tr>
<td>
<?php $i = 0 ?>
<?php foreach ($cc_list as $obj) : ?>
<?php   $mrid = $obj['mrid'] ?>
<?php   $time = $obj['t_1st_save'] ?>
<?php   $time_lk = '<a href="/mr/view_mr?ptnumber=' . $ptnumber
                    . '&time=' . $time 
                    . '&ac_no=' . $ac_no
                    . '" target="_blank" rel="noopener noreferrer">' 
                    . substr($time, 0, 10) . '</a>' /* 分以下を除去 */ ?>
<?php   echo $time_lk ?>
<?php   if (++$i % 3 === 0)  echo '<br />' ?>
<?php endforeach; ?>
</td>
<td>
<?php $i = 0 ?>
<?php foreach ($col_order_list as $obj) : ?>
<?php   $mrid = $obj['mrid'] ?>
<?php   $time = $obj['t_1st_save'] ?>
<?php   $time_lk = '<a href="/mr/view_mr?ptnumber=' . $ptnumber 
                    . '&time=' . $time 
                    . '&ac_no=' . $ac_no
                    . '" target="_blank" rel="noopener noreferrer">' 
                    . substr($time, 0, 10) . '</a>' /* 分以下を除去 */ ?>
<?php   echo $time_lk ?>
<?php   if (++$i % 3 === 0)  echo '<br />' ?>
<?php endforeach; ?>
</td>
</tr>                
</table>
</main>
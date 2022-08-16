<?=$this->element('Mr/head', ['task' => 'カルテ内検索']) ?>
<?php $this->assign('css', $this->Html->css('mr')) ?>

<header>
    <?=$this->element('Mr/links_on_search_mr') ?>
    <?=$this->element('Mr/ptno_name_age') ?>
</header>               
<br />
<main>
<?=$this->Form->create(null, ['type'=>'post']) ?>
<?=$this->Form->text('str_for_search', ['placeholder' => '検索文字列...']) ?>
<?=$this->Form->button('検索')  ?>
<?=$this->Form->end() ?>

<table>
<?php $table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
<?php echo $this->Html->tableHeaders($table_header) ?>
<tr>
<td>
<?php $i = 0 ?>
<?php foreach ($cc_list as $obj) : ?>
<?php   $time = $obj['t_1st_save'] ?>
<?php   echo $this->Html->link(substr($time, 0, 10), ['action'=>'view-mr', 
                '?'=>['ptnumber'=>$ptnumber, 'time'=>$time, 'ac_no'=>$ac_no]], 
                ['target'=>'_blank']) ?>
<?php   if (++$i % 3 === 0)  echo '<br />' ?>
<?php endforeach; ?>
</td>
<td>
<?php $i = 0 ?>
<?php foreach ($col_order_list as $obj) : ?>
<?php   $time = $obj['t_1st_save'] ?>
<?php   echo $this->Html->link(substr($time, 0, 10), ['action'=>'view-mr', 
                '?'=>['ptnumber'=>$ptnumber, 'time'=>$time, 'ac_no'=>$ac_no]], 
                ['target'=>'_blank']) ?>
<?php   if (++$i % 3 === 0)  echo '<br />' ?>
<?php endforeach; ?>
</td>
</tr>                
</table>
</main>
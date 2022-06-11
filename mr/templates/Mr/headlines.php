<?=$this->element('Mr/head', ['task' => '見出し一覧']) ?>
<?php $this->assign('css', $this->Html->css('headlines')) ?>

<header>
<?=$this->element('Mr/link_from_headlines') ?>
<?=$this->element('Mr/header_of_mr0')  ?>
</header>

<main>
<?php $table_body = [] ?>
<?php foreach ($cc_list as $obj): ?>
<?php   $time = $obj['t_1st_save'] ?>
<?php   $time_lk = '<a href="/mr/view_mr?ptnumber=' . $ptnumber . '&time=' . $time
                    . '&ac_no=' . $ac_no . '" target="_blank" rel="noopener noreferrer">'
                    . substr($time, 0, 10) . '</a>' /* 分以下を除去 */ ?>
<?php   $arr_temp = explode("\n", $obj['cc'], 2) ?>
<?php   $headline = h($arr_temp[0]) /* ccの1行目 */?>
<?php   $table_body[] = [$time_lk, $headline] ?>
<?php endforeach; ?>

<table>
<?=$this->Html->tableHeaders(['日付', '見出し']) ?>
<?=$this->Html->tableCells($table_body, ['style' => 'background:#dcdcdc; '], ['style' => 'background:#fffff']) ?>
</table>
</main>



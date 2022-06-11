<?=$this->element('Mr/head', ['task' => 'メモ']) ?>
<?php $this->assign('css', $this->Html->css('admin-note')) ?>

<header>
    <?=$this->element('Mr/link_from_admin_note') ?>
    <table>
        <?=$this->Html->tableCells($arr_ptinf, ['style' => 'background:#dcdcdc; '], ['style' => 'background:#fffff']) ?>
    </table>
</header>
<br />
<main>
    <table>
        <?=$this->Html->tableCells(h($admin_note), ['style' => 'background:#dcdcdc; '], ['style' => 'background:#fffff']) ?>
    </table>
</main>


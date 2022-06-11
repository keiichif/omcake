<?=$this->element('Mr/head', ['task' => 'メモ修正']) ?>
<?php $this->assign('css', $this->Html->css('admin-note')) ?>
<?php $this->assign('css2', $this->Html->css('edit-mode')) ?>
<?php $this->assign('script', $this->Html->script('jquery-3.5.1.min')) ?>
<?php $this->assign('script2', $this->Html->script('check_beforeunload')) ?>
<?php $input_textsize = 55 ?>

<header>
    <?=$this->element('Mr/link_from_edit_admin_note') ?>
    <table>
        <?=$this->Html->tableCells($arr_ptinf, ['style' => 'background:#dcdcdc; '], ['style' => 'background:#fffff']) ?>
    </table>
</header>
<br />
<main>
    <?=$this->Form->create(null,['type'=>'post', 'name'=>'edit_admin_note','url'=>'/mr/save_admin_note']) ?>
    <?=$this->Form->hidden('ptnumber', ['value'=>$ptnumber]) ?>
    <?=$this->Form->hidden('ac_no', ['value'=>$ac_no]) ?>
    <table>
        <tr style="background:#dcdcdc; ">
            <td><?=$admin_note[0][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note1', 'size'=>$input_textsize, 'value'=>$admin_note[0][1]]) ?></td>
        </tr>
        <tr style="background:#fffff">
            <td><?=$admin_note[1][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note2', 'size'=>$input_textsize, 'value'=>$admin_note[1][1]]) ?></td>
        </tr>
        <tr style="background:#dcdcdc; ">
            <td><?=$admin_note[2][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note3', 'size'=>$input_textsize, 'value'=>$admin_note[2][1]]) ?></td>
        </tr>
        <tr style="background:#fffff">
            <td><?=$admin_note[3][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note4', 'size'=>$input_textsize, 'value'=>$admin_note[3][1]]) ?></td>
        </tr>
        <tr style="background:#dcdcdc; ">
            <td><?=$admin_note[4][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note5', 'size'=>$input_textsize, 'value'=>$admin_note[4][1]]) ?></td>
        </tr>
        <tr style="background:#fffff">
            <td><?=$admin_note[5][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note6', 'size'=>$input_textsize, 'value'=>$admin_note[5][1]]) ?></td>
        </tr>
        <tr style="background:#dcdcdc; ">
            <td><?=$admin_note[6][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note7', 'size'=>$input_textsize, 'value'=>$admin_note[6][1]]) ?></td>
        </tr>
        <tr style="background:#fffff">
            <td><?=$admin_note[7][0] ?></td>
            <td><?=$this->Form->text('', ['name'=>'note8', 'size'=>$input_textsize, 'value'=>$admin_note[7][1]]) ?></td>
        </tr>
    </table>
    <?=$this->Form->end() ?>
</main>


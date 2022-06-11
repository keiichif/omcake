<?php $cols = 42 ?>
<?php $rows = 18 ?>
<?php $cols2 = $cols*2 + 4 ?>
<?php $rows2 = 5 ?>

<table>
    <?php $table_header = ['既往症・原因・主要症状・経過等', '処方・手術・処置等'] ?>
    <?=$this->html->tableHeaders($table_header) ?>
    <tr>
        <td style="border: none;">
            <?=$this->Form->textarea('cc', ['label'=>'cc', 'value'=>$cc, 
                'spellcheck'=>'false', 'cols'=>$cols+1, 'rows'=>$rows]) ?>
        </td>
        <td style="border: none;">
            <?=$this->Form->textarea('col_order', ['label'=>'col_order', 'value'=>$col_order, 
                'spellcheck'=>'false', 'cols'=>$cols-1, 'rows'=>$rows]) ?>
       </td>
    </tr>    
</table>
<table>
    <?=$this->Html->tableHeaders(['臨床メモ']) ?>
    <tr>
        <td style="border: none;">
            <?=$this->Form->textarea('clin_note', ['label'=>'clin_note', 'value'=>$clin_note, 
                'spellcheck'=>'false', 'cols'=>$cols2, 'rows'=>$rows2]) ?>
        </td>
    </tr>
</table>

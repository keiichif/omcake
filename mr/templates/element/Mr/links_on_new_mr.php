<div id="header_link">
    <?=$this->Html->link('メモ', ['action'=>'view-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('中止', ['action'=>'headlines',  '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]) ?>
    <?=$this->Html->link('保存', 'javascript:form_mr.submit()', ['id'=>'submit']) ?>
</div>
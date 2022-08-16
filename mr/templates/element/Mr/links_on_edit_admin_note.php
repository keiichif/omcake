<div id="header_link">
    <?=$this->Html->link('中止', ['action'=>'view-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]) ?>
    <?=$this->Html->link('保存', 'javascript:document.form_mr.submit()', ['id'=>'submit']) ?>
</div>

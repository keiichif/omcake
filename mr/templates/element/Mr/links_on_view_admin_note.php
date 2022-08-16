<div id="header_link">
    <?=$this->Html->link('見出し', ['action'=>'headlines', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('修正', ['action'=>'edit-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]]) ?>
</div>

<div id="header_link">    
    <?=$this->Html->link('メモ',     ['action'=>'view-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('修正',     ['action'=>'edit-mr',    '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no, 'time'=>$time]]) ?>
    <?=$this->Html->link('新規',     ['action'=>'new-mr',     '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('見出し',   ['action'=>'headlines',  '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
</div>
<div id="header_link">
    <?=$this->Html->link('メモ',    ['action'=>'view-admin-note', '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('検索',    ['action'=>'search-mr',  '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
    <?=$this->Html->link('新規',    ['action'=>'new-mr',     '?'=>['ptnumber'=>$ptnumber, 'ac_no'=>$ac_no]], ['target'=>'_blank']) ?>
</div>

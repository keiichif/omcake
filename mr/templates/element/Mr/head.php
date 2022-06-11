<?php $ac_no_temp = ($ac_no === '') ? '' : '(' . $ac_no . ')' ?>
<?php if (preg_match('/新規|修正/', $task)) $ac_no_temp = '★' . $ac_no_temp ?>
<?php $this->assign('title', $ac_no_temp . ' ' . $ptnumber . '__' . preg_replace('/　/',' ',$name) . 
        '　' . $task) ?>
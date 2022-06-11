<?php $this->assign('title', '受付一覧') ?>
<?php $this->assign('css', $this->Html->css('acceptance-list'))  ?>
    
<header>
<?=$this->element('AcceptanceList/link_from_aclist_today') ?>
</header>
<br/>
<main>
    <table>
        <?=$this->element('AcceptanceList/table') ?>
    </table>
</main>

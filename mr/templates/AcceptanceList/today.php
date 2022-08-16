<?php $this->assign('title', '受付一覧') ?>
<?php $this->assign('css', $this->Html->css('acceptance-list'))  ?>
<?php $this->assign('script', $this->Html->script('reload_when_focused')) ?>
    
<main>
    <table>
        <?=$this->element('AcceptanceList/table') ?>
    </table>
</main>

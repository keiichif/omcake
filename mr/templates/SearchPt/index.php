<?php $this->assign('title', '患者検索') ?>
<?php $this->assign('css', $this->Html->css('search-pt')) ?>

<main>
    <div>
        <?=$this->Form->create() ?>

        <?=$this->Form->text('data_for_search', ['placeholder' => '患者番号、名前、ふりがな']) ?>
        <?=$this->Form->end() ?>
    </div>
    <br />
    <table>
        <?=$this->Html->tableHeaders($ptlist_header, ['style' => 'background:#006; color:white']) ?>
        <?=$this->Html->tableCells($ptlist_body, ['style' => 'background:#dcdcdc; '], ['style' => 'background:#fffff']) ?>
    </table>               
</main>
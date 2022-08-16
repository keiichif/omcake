<!DOCTYPE html>
<html>
<head>
    <?=$this->Html->charset() ?>
    <title><?=$this->fetch('title') ?></title>
    
    <?=$this->Html->css('mr-common') ?>
    <?=$this->fetch('css') ?>
    <?=$this->fetch('css2') ?>
        
    <?=$this->fetch('meta')  ?>
</head>
    
<body>
    <?=$this->fetch('content') ?>
    <?=$this->fetch('script')  ?>        
    <?=$this->fetch('script2') ?>
    <?=$this->fetch('script3') ?>
</body>
</html>
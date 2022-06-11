<!-- templates/layout/search-pt.php -->
<!DOCTYPE html>
<html>
    <head>
        <?=$this->Html->charset() ?>
        <title><?=$this->fetch('title') ?></title>
    </head>
    
    <body>
        <div class="content row">
            <?=$this->fetch('content') ?>
        </div>
    </body>
</html>
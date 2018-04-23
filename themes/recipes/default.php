<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <meta name="theme-color" content="#000000">
        <link rel="manifest" href="/manifest.json">
        <link rel="shortcut icon" href="/favicon.ico">
        <?php Loader::element('header_required') ?>
        <link href="<?php echo $this->getThemePath();?>/build/static/css/main.982ca1be.css" rel="stylesheet">
    </head>
    <body>
        <div class="<?= $c->getPageWrapperClass();?>"
        <div id="root"></div>
        </div>
        <script type="text/javascript" src="<?php echo $this->getThemePath();?>/build/static/js/main.21ddfc23.js"></script>
        <?php Loader::element('footer_required') ?>
    </body>
</html>
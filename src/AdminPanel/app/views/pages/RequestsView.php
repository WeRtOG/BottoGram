<?php 
    use WeRtOG\BottoGram\AdminPanel\AdminPanel;
?>
<section class="request-logs">
    <div class="loading">   
        <?php
            $PreloaderPath = BOTTOGRAM_ADMIN_ASSETS . '/production/images/preloader.svg';
            if(file_exists($PreloaderPath))
                include $PreloaderPath;
            
        ?>
    </div>
    <section class="items faded"></section>
</section>

<?php AdminPanel::AsyncConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/page/RequestLogs.js'); ?>
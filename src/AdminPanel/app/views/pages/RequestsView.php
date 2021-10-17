<section class="request-logs">
    <div class="loading">   
        <?php 
            $PreloaderPath = BOTTOGRAM_ADMIN_ASSETS . '/images/preloader.svg';
            if(file_exists($PreloaderPath))
                include $PreloaderPath;
        ?>
    </div>
    <section class="items faded"></section>
</section>
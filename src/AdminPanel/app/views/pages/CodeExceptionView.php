<?php 
    $Exception = $this->Data['Exception'];
    $Message = $Exception->getMessage();
?>
<h3 class="text-danger mt-5">
    <i class="bi bi-exclamation-octagon-fill mr-1"></i>
    <span style="line-height: 50px; position: relative; top: -2px">
        <?= !empty($Message) ? $Message : 'Unexpected exception'?>
    </span>
    
</h3>
<p>
    <span class="text-muted">in&nbsp;</span>
    <?=$Exception->getFile()?>

    <span class="text-muted">&nbsp;on line&nbsp;</span>
    <?=$Exception->getLine()?>
</p>
<p class="mt-5">
    Stack trace:
</p>
<pre class="text-muted" style="white-space: pre-wrap;">
<?=(string)$Exception->getTraceAsString()?>
</pre>
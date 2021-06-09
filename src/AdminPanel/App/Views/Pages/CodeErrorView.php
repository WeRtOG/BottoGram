<?php 
    $Error = $this->Data['Error'];
    $Message = $Error->getMessage();
?>
<h3 class="text-danger mt-5">
    <i class="bi bi-exclamation-octagon-fill mr-1"></i>
    <span style="line-height: 50px; position: relative; top: -2px">
        <?= !empty($Message) ? $Message : 'Unexpected error'?>
    </span>
    
</h3>
<p>
    <span class="text-muted">in&nbsp;</span>
    <?=$Error->getFile()?>

    <span class="text-muted">&nbsp;on line&nbsp;</span>
    <?=$Error->getLine()?>
</p>
<p class="mt-5">
    Stack trace:
</p>
<pre class="text-muted" style="white-space: pre-wrap;">
<?=(string)$Error->getTraceAsString()?>
</pre>
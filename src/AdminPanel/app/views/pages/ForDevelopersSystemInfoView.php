<div class="version-info p-3">
    <p><b>Операционная система: </b><?=$this->Data['SystemInfo']['OS']?>
    <p><b>Версия PHP: </b><?=$this->Data['SystemInfo']['PHPVersion']?></p>
    <p><b>Версия MySQL: </b><?=$this->Data['SystemInfo']['MySQLVersion']?></p>
    <p><b>Версия BottoGram: </b><?=$this->Data['SystemInfo']['BottoGramVersion']?></p>
    <button class="mt-2 btn btn-primary copy-to-buffer" data-to-copy="<?=print_r($this->Data['SystemInfo'], true)?>" data-title-after-copy="Скопировано!">
        <i class="bi bi-files"></i>
        <span class="title">Копировать в буфер обмена</span>
    </button>
</div>
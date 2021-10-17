<div class="version-info p-3">
    <p><b>Версия PHP: </b><?=$this->Data['SystemInfo']['PHPVersion']?></p>
    <p><b>Версия BottoGram: </b><?=$this->Data['SystemInfo']['BottoGramVersion']?></p>
    <button class="mt-2 btn btn-primary copy-to-buffer" data-copy-data="<?=print_r($this->Data['SystemInfo'], true)?>" data-after-copy-title="Скопировано!">
        <span class="title">Копировать в буфер обмена</span>
    </button>
</div>
<div class="version-info p-3">
    <p><b>Операционная система: </b><?=$this->Data['SystemInfo']['OS']?></p>
    <br>
    <p><b>Версия PHP: </b><?=$this->Data['SystemInfo']['PHPVersion']?></p>
    <p><b>Версия MySQL: </b><?=$this->Data['SystemInfo']['MySQLVersion']?></p>
    <p><b>Версия BottoGram: </b><?=$this->Data['SystemInfo']['BottoGramVersion']?></p>
    <br>
    <p><b>Путь к BottoGram: </b><?=$this->Data['SystemInfo']['BottoGramPath']?></p>
    <p><b>Путь к папке проекта: </b><?=$this->Data['SystemInfo']['ProjectPath']?></p>
    <p><b>Путь к административной панели: </b><?=$this->Data['SystemInfo']['AdminPanelPath']?></p>
    <br>
    <button class="mt-2 btn btn-primary copy-to-buffer" data-to-copy="<?=print_r($this->Data['SystemInfo'], true)?>" data-title-after-copy="Скопировано!">
        <i class="bi bi-files"></i>
        <span class="title">Копировать в буфер обмена</span>
    </button>
</div>
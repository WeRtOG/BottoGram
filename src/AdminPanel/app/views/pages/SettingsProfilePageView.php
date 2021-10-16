<form class="mb-5" method="POST" autocomplete="off" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
    <input type="hidden" name="form" value="UpdateMainInfo" />
    <h4 class="mb-4">Смена пароля</h4>
    <div class="mb-3">
        <label for="BottoConfig_Name" class="form-label">Новый пароль</label>
        <input required type="password" minlength="8" class="form-control" name="NewPassword" placeholder="Здесь можно ввести новый пароль">
    </div>
    <?php if($this->Data['Notify'] != null) { ?>
        <div class="mb-3">
            <p class="text-warning"><?=$this->Data['Notify']?></p>
        </div>
    <?php } ?>
    <div class="mt-4 mb-3">
        <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
    </div>
</form>
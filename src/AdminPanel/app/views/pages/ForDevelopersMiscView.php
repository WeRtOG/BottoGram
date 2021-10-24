<div class="p-4">
    <form action="<?=$this->Root?>/fordevelopers/updateOptimizationSettings" class="mb-5" method="POST" autocomplete="off" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
        <h4 class="mt-5">Оптимизация</h4>
        <div class="mt-4">
            <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox"<?=$this->GlobalData['BottoConfig']->UseMinifedAssetsInAdminPanel ? ' checked' : ''?> name="BottoConfig_UseMinifedAssetsInAdminPanel" id="BottoConfig_UseMinifedAssetsInAdminPanel">
                <label class="form-check-label">Использовать минифицированный CSS и JS</label>
            </div>
        </div>
        <div class="mt-5 mb-3">
            <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
            <a href="<?=$this->Root?>/fordevelopers/reloadMinificationCache"><div class="btn btn-primary">Обновить кеш минификации</div></a>
        </div>
    </form>
</div>
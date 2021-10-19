<section class="bot-users">
    <div class="search-bar">
        <div class="input-wrapper">
            <input type="text" placeholder="Поиск" />
            <button class="search">
                <i class="bi bi-search"></i>
            </button>
            <button class="cancel-search" disabled>
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>
    <div class="search-results faded hidden">
        <div class="items"></div>
    </div>
    <?php if(count($this->Data['Users']) > 0) { ?>
    <table class="table search">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">ChatID</th>
                <th scope="col">UserName</th>
                <th scope="col">FullName</th>
                <th scope="col">Nav</th>
                <th scope="col">Cache</th>
                <th scope="col">LastMediaGroup</th>
                <th scope="col">RegistrationDate</th>
            </tr>
        </thead>
        <?php foreach($this->Data['Users'] as $User) { ?>
        <tr <?=$User->ID == $this->Data['Highlight'] ? 'class="highlight"' : ''?>>
            <th scope="row"><?=$User->ID ?? 'null'?></th>
            <td><?=$User->ChatID ?? 'null'?></td>
            <?php if($User->UserName != $User->ChatID) { ?>
            <td><a href="https://t.me/<?=$User->UserName?>" target="_blank">@<?=$User->UserName ?? 'null'?></a></td>
            <?php } else { ?>
            <td class="no-username"><?=$User->UserName ?? 'null'?></td>
            <?php } ?>
            
            <td><?=$User->FullName ?? 'null'?></td>
            <td><?=$User->Nav ?? 'null'?></td>
            <td><pre><code class="language-javascript"><?=$User->Cache ? json_encode($User->Cache, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : 'null'?></code></pre></td>
            <td><?=$User->LastMediaGroup ?? 'null'?></td>
            <td><?=$User->RegistrationDate ?? 'null'?></td>
        </tr>
        <?php } ?>
    </table>
    <?php } else { ?>
    <p class="p-4">Нет данных.</p>
    <?php } ?>
</section>
<?php if($this->Data['PageCount'] > 1) { ?>
<!-- Pagination -->
<nav class="mt-5">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php if($this->Data['CurrentPage'] <= 1){ echo 'disabled'; } ?>">
            <a data-reload=".settings-page .sub-page" class="async page-link"
                href="<?=$this->Root . '/' . CurrentMVCController . '/' . CurrentMVCAction . '/?page=1'?>">&laquo;</a>
        </li>

        <?php if($this->Data['PaginationLeft'] > 1) { ?>
        <li class="page-item disabled">
            <a data-reload=".settings-page .sub-page" class="async page-link">...</a>
        </li>
        <?php } ?>

        <?php for($i = $this->Data['PaginationLeft']; $i <= $this->Data['PaginationRight']; $i++ ): ?>
        <li class="page-item <?php if($this->Data['CurrentPage'] == $i) {echo 'active'; } ?>">
            <a data-reload=".settings-page .sub-page" class="async page-link" href="<?=$this->Root . '/' . CurrentMVCController . '/' . CurrentMVCAction . '/?page=' . $i?>"> <?= $i; ?> </a>
        </li>
        <?php endfor; ?>

        <?php if($this->Data['PaginationRight'] < $this->Data['PageCount']) { ?>
        <li class="page-item disabled">
            <a data-reload=".settings-page .sub-page" class="async page-link">...</a>
        </li>
        <?php } ?>

        <li class="page-item <?php if($this->Data['CurrentPage'] >= $this->Data['PageCount']) { echo 'disabled'; } ?>">
            <a data-reload=".settings-page .sub-page" class="async page-link"
                href="<?php if($this->Data['CurrentPage'] >= $this->Data['PageCount']){ echo '#'; } else { echo $this->Root . '/' . CurrentMVCController . '/' . CurrentMVCAction . '/?page=' . ($this->Data['PageCount']); } ?>">&raquo;</a>
        </li>
    </ul>
</nav>
<?php } ?>
<form action="<?=$this->Root?>/auth" class="form-signin anix" data-speed="200" data-fx="move" data-direction="top" method="POST">
    <h1 class="h3 mb-3 font-weight-normal">Необходима авторизация</h1>
    <input name="Login" type="login" id="inputLogin" class="form-control" placeholder="Логин" required autofocus>
    <input name="Password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
    <input class="btn btn-md btn-primary btn-block" type="submit" class="form-control" value="Войти"/>
    <?php if(isset($this->Data['Error'])) { ?>
    <p class="error"><?=$this->Data['Error']?></p>
    <?php } ?>
    <p class="mt-5 mb-3 text-muted">&copy;BottoGram <?=date('Y')?></p>
</form>
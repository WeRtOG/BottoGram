<!DOCTYPE html>
<html lang="ru" class="<?=$this->GlobalData['BottoConfig']->DarkTheme ? 'dark' : 'white'?>-theme">
<head>
	<!-- Место для метаданных -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$this->PageTitle?></title>

	<!-- Bootstrap CSS -->
	<?php if($this->GlobalData['BottoConfig']->DarkTheme) { ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.0.0/dist/cyborg/bootstrap.min.css">
	<?php } else { ?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
	<?php } ?>

	<!-- Место для CSS -->
	<?php
		$this->LoadCSS(BOTTOGRAM_ADMIN_ASSETS . '/css/Main.css');
		$this->LoadCSS(BOTTOGRAM_ADMIN_ASSETS . '/css/Auth.css');
	?>

</head>
<body class="text-center">

	<?php
		include __DIR__ . '/UIComponents/Forms/AuthForm.php';
	?>

	<!-- transition.js + AniX -->
	<?php
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/lib/transition.min.js');
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/lib/anix.nova.js');
	?>

	<!-- Место для скриптов -->
	<?php
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/Main.js');
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/Auth.js');
	?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
</html>
<?php         
	use WeRtOG\BottoGram\AdminPanel\AdminPanel;
?>
<!DOCTYPE html>
<html lang="ru" class="<?=$this->GlobalData['BottoConfig']->DarkTheme ? 'dark' : 'white'?>-theme">
<head>
	<!-- Место для метаданных -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$this->PageTitle?></title>

	<link rel="icon" type="image/x-icon" href="<?=$this->GenerateFilePublicPath(BOTTOGRAM_ADMIN_ASSETS . '/images/logo/icon.ico', AdminPanel::GetBuiltInСomponentsPathIntOffset())?>"/>

	<!-- Bootstrap CSS -->
	<?php if($this->GlobalData['BottoConfig']->DarkTheme) { ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.0.0/dist/cyborg/bootstrap.min.css">
	<?php } else { ?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
	<?php } ?>

	<!-- Место для CSS -->
	<?php
		$this->LoadCSS(BOTTOGRAM_ADMIN_ASSETS . '/css/main.css', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		$this->LoadCSS(BOTTOGRAM_ADMIN_ASSETS . '/css/auth.css', AdminPanel::GetBuiltInСomponentsPathIntOffset());
	?>

</head>
<body class="text-center">

	<?php
		include __DIR__ . '/ui/forms/AuthForm.php';
	?>

	<!-- transition.js + AniX -->
	<?php
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/lib/transition.min.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/lib/anix.nova.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
	?>

	<!-- Место для скриптов -->
	<?php
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/main.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/js/auth.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
	?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
</html>
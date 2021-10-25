<?php         
	use WeRtOG\BottoGram\AdminPanel\AdminPanel;
?>
<!DOCTYPE html>
<html lang="ru" class="<?=$this->GlobalData['DarkTheme'] ? 'dark' : 'white'?>-theme">
<head>
	<!-- Место для метаданных -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$this->PageTitle?></title>

	<link rel="icon" type="image/x-icon" href="<?=$this->GenerateFilePublicPath(BOTTOGRAM_ADMIN_ASSETS . '/generic/images/logo/icon.ico', AdminPanel::GetBuiltInСomponentsPathIntOffset())?>"/>

	<!-- Bootstrap CSS -->
	<?php if($this->GlobalData['DarkTheme']) { ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.0.0/dist/cyborg/bootstrap.min.css">
	<?php } else { ?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
	<?php } ?>

	<script>
		const MVCRoot = "<?=$this->Root?>";
		var UITheme = "<?=$this->GlobalData['DarkTheme'] ? 'dark' : 'white'?>";
	</script>

	<?php
		if($this->GlobalData['UseMinifedAssets'])
		{
			AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/css/main.css', true);
			AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/css/auth.css', true);

			AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/navigation.js', true);
		}
		else
		{
			AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/css/main.css');
			AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/css/auth.css');

			AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.asyncevents.js');
			AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.pagescripts.js');
		}
	?>

</head>
<body class="text-center">

	<?php
		include __DIR__ . '/ui/forms/AuthForm.php';
	?>

	<!-- transition.js + AniX -->
	<?php
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/generic/js/lib/transition.min.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/generic/js/lib/anix.nova.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/generic/js/lib/tinycolor.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
	?>

	<!-- Место для скриптов -->
	<?php
			if($this->GlobalData['UseMinifedAssets'])
			{
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/admin-ui.js', AdminPanel::GetBuiltInСomponentsPathIntOffset(), true);
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/main.js', AdminPanel::GetBuiltInСomponentsPathIntOffset(), true);
			}
			else
			{
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.personalization.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.smartcharts.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/main.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
			}
	?>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

	<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
</body>
</html>
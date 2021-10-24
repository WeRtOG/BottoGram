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
	
		<link rel="icon" type="image/x-icon" href="<?=$this->GenerateFilePublicPath(BOTTOGRAM_ADMIN_ASSETS . '/production/images/logo/icon.ico', AdminPanel::GetBuiltInСomponentsPathIntOffset())?>"/>

		<!-- Bootstrap CSS -->
		<?php if($this->GlobalData['DarkTheme']) { ?>
		<link class="bootstrap-theme" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.0.0/dist/cyborg/bootstrap.min.css" crossorigin />
		<link class="prismjs-theme" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism-tomorrow.min.css" crossorigin />
		<?php } else { ?>
		<link class="bootstrap-theme" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" crossorigin />
		<link class="prismjs-theme" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css" crossorigin />
		<?php } ?>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
		<script>
			const MVCRoot = "<?=$this->Root?>";
			
			var UITheme = "<?=$this->GlobalData['DarkTheme'] ? 'dark' : 'white'?>";

			const ThemeAssetsCSS = [
				{
					selector: '.bootstrap-theme',
					darkTheme: 'https://cdn.jsdelivr.net/npm/bootswatch@5.0.0/dist/cyborg/bootstrap.min.css',
					whiteTheme: 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css'
				},
				{
					selector: '.prismjs-theme',
					darkTheme: 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism-tomorrow.min.css',
					whiteTheme: 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css'
				}
			];
		</script>

		<!-- Место для CSS -->
		<?php
			if($this->GlobalData['UseMinifedAssets'])
			{
				AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/css/main.css', true);
				AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/css/cabinet.css', true);

				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/navigation.js', true);
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/admin-ui.js', AdminPanel::GetBuiltInСomponentsPathIntOffset(), true);
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/main.js', AdminPanel::GetBuiltInСomponentsPathIntOffset(), true);
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/production/js/cabinet.js', AdminPanel::GetBuiltInСomponentsPathIntOffset(), true);
			}
			else
			{
				AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/css/main.css');
				AdminPanel::ConnectCSS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/css/cabinet.css');

				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.asyncnavigation.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.asyncevents.js');
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.pagescripts.js');
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.personalization.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/class.smartcharts.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/main.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
				AdminPanel::ConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/cabinet.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
			}
		?>
	</head>
	<body>
		<div class="d-flex cabinet">
			<!-- Sidebar-->
			<div data-speed="100" class="anix bg-light border-right sidebar-wrapper">
				<div class="sidebar-heading">
					<img class="logo dark-theme-only" src="<?=$this->GenerateFilePublicPath(BOTTOGRAM_ADMIN_ASSETS . '/production/images/logo/icon-white.svg', AdminPanel::GetBuiltInСomponentsPathIntOffset())?>" />
					<img class="logo white-theme-only" src="<?=$this->GenerateFilePublicPath(BOTTOGRAM_ADMIN_ASSETS . '/production/images/logo/icon-black.svg', AdminPanel::GetBuiltInСomponentsPathIntOffset())?>" />
					<h3><?=$this->GlobalData['BottoConfig']->Name?></h3>
					<p class="powered-by">Powered by BottoGram</p>
				</div>
				
				<div class="list-group list-group-flush">
					<a class="async list-group-item list-group-item-action bg-light<?=CurrentMVCController == 'dashboard' ? ' active' : ''?>" href="<?=$this->Root . '/dashboard'?>"><i class="bi bi-speedometer2"></i>Главная</a>
					<?php if(isset($this->GlobalData['CurrentUser']) && $this->GlobalData['CurrentUser']->CanViewRequestLogs) { ?>
					<a class="async list-group-item list-group-item-action bg-light<?=CurrentMVCController == 'requests' ? ' active' : ''?>" href="<?=$this->Root . '/requests'?>"><i class="bi bi-clock"></i>История запросов</a>
					<?php } ?>
					<?php foreach($this->GlobalData['SidebarCustomItems'] as $Item) { ?>
						<a class="async list-group-item list-group-item-action bg-light<?=$this->Route == $Item->Link ? ' active' : ''?>" href="<?=$this->Root . $Item->Link?>"><i class="bi bi-<?=$Item->Icon?>"></i><?=$Item->Name?></a>
					<?php } ?>
					<?php if(isset($this->GlobalData['CurrentUser']) && $this->GlobalData['CurrentUser']->CanChangeConfig) { ?>
					<a class="async list-group-item list-group-item-action bg-light<?=CurrentMVCController == 'binding' ? ' active' : ''?>" href="<?=$this->Root . '/binding'?>"><i class="bi bi-cloud"></i>Привязка к Telegram</a>
					<?php } ?>
					<?php if(isset($this->GlobalData['CurrentUser']) && $this->GlobalData['CurrentUser']->Login == 'admin') { ?>
					<a class="async list-group-item list-group-item-action bg-light<?=CurrentMVCController == 'fordevelopers' ? ' active' : ''?>" href="<?=$this->Root . '/fordevelopers'?>"><i class="bi bi-code-slash"></i>Для разработчиков</a>
					<?php } ?>
					<a class="async list-group-item list-group-item-action bg-light<?=CurrentMVCController == 'settings' ? ' active' : ''?>" href="<?=$this->Root . '/settings'?>"><i class="bi bi-gear"></i>Настройки BottoGram</a>
					<a class="list-group-item list-group-item-action bg-light" href="<?=$this->Root . '/auth/logout'?>"><i class="bi bi-arrow-left-square"></i>Выйти</a>
				</div>
			</div>
			<!--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>-->
			<!-- Page Content-->
			<div class="page-content-wrapper anix" data-speed="300">
				<?php include $this->ContentView; ?>
			</div>
		</div>

		<!-- transition.js + AniX -->
		<?php
			$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/production/js/lib/transition.min.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
			$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/production/js/lib/anix.nova.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
			$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/production/js/lib/tinycolor.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
			$this->LoadJS(BOTTOGRAM_ADMIN_ASSETS . '/production/js/lib/apexcharts.min.js', AdminPanel::GetBuiltInСomponentsPathIntOffset());
		?>

		<!-- Bootstrap JS + Jquery + Popper.js -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
		
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js" integrity="sha512-YBk7HhgDZvBxmtOfUdvX0z8IH2d10Hp3aEygaMNhtF8fSOvBZ16D/1bXZTJV6ndk/L/DlXxYStP8jrF77v2MIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</body>
</html>
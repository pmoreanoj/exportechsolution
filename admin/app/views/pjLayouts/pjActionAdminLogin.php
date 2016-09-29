<!doctype html>
<html>
	<head>
		<!--Start Modification -->
		<title>Exportech Solution | Admin Panel</title>
		<link rel="icon" href="https://www.exportechsolution.com/admin/app/web/img/media/favicon.png" sizes="32x32">
		<link rel="icon" href="https://www.exportechsolution.com/admin/app/web/img/media/favicon.png" sizes="192x192">
		<!--End Modification -->
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<!-- Start Modification-->
				<img src="https://www.exportechsolution.com/admin/app/web/img/media/medium_logo.png" width="350" height="106" alt="Exportech Solution">
				<!-- End Modifcation -->
			</div>
			<div id="middle">
				<div id="login-content">
				<?php require $content_tpl; ?>
				</div>
			</div> <!-- middle -->
		</div> <!-- container -->
		<div id="footer-wrap">
			<div id="footer">
				<!--Start Modifcation-->
			   	<p>Copyright &copy; <?php echo date("Y"); ?> Exportech Solution, Inc.</p>
                                <p>Developed by -- PM/FF</p>
			   	<!--End Modification-->
	        </div>
        </div>
	</body>
</html>
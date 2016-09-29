<!DOCTYPE html>
<html>
	<head>
		<title>Shopping Cart by PHPJabbers.com</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="fragment" content="!">
		<meta name="viewport" content="width=device-width">
	    <link href="core/framework/libs/pj/css/pj.bootstrap.min.css" type="text/css" rel="stylesheet" />
	    <link href="index.php?controller=pjFront&action=pjActionLoadCss<?php echo isset($_GET['theme']) ? '&theme=' . $_GET['theme'] : null;?>" type="text/css" rel="stylesheet" />
	<head>
	<body>
		<div style="max-width: 1024px;">
			<script type="text/javascript" src="index.php?controller=pjFront&action=pjActionLoad<?php echo isset($_GET['theme']) ? '&theme=' . $_GET['theme'] : null;?><?php echo isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : null;?>"></script>
		</div>
	</body>
</html>
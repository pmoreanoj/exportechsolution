<nav class="navbar navbar-default">
  <div class="container-fullwidth">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topNav" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="topNav">	

	<ul class="nav navbar-nav shopnav navbar-right">
	   <?php
			$myFavs = array();
			$number_of_favs = 0;
			if (isset($_COOKIE[$controller->defaultCookie]) && !empty($_COOKIE[$controller->defaultCookie]))
			{
				$myFavs = unserialize(stripslashes($_COOKIE[$controller->defaultCookie]));
				$number_of_favs = count($myFavs);
				foreach ($myFavs as $fav => $whatever)
				{
					$item = unserialize($fav);
					$product = NULL;
					if(in_array($item['product_id'], $tpl['hidden_ids_arr']))
					{
						$number_of_favs--;
					}
				}
			}
		?>
		    <li<?php echo $_GET['action'] == 'pjActionFavs' ? ' class="active"' : null;?>><a href="<?php echo pjUtil::getReferer(); ?>#!/Favs" ><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>&nbsp&nbsp<?php __('front_favs'); ?><span class="badge"><?php echo !empty($myFavs) ? sprintf(" (%u)", $number_of_favs): NULL; ?></span></a></li>
		<?php
		   	if (!$controller->isLoged())
		   	{ 
		?>
		       	<li<?php echo $_GET['action'] == 'pjActionLogin' ? ' class="active"' : null;?>><a href="<?php echo pjUtil::getReferer(); ?>#!/Register" class="scSelectorLogin"><i class="fa fa-user" aria-hidden="true"></i>&nbsp&nbsp<?php __('front_login'); ?></a></li>
				<li<?php echo $_GET['action'] == 'pjActionRegister' ? ' class="active"' : null;?>><a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="scSelectorRegister"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp&nbsp<?php __('front_register'); ?></a></li>
		<?php
		   	}else{
	    ?>
				<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Logout" class="scSelectorLogout"><i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp&nbsp<?php __('front_logout'); ?></a></li>
		        <li<?php echo $_GET['action'] == 'pjActionProfile' ? ' class="active"' : null;?>><a href="<?php echo pjUtil::getReferer(); ?>#!/Profile" class="scSelectorProfile"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp&nbsp<?php __('front_profile'); ?></a></li>
		<?php
		   	}			   	
		?>
		<!--3-->
		<li><a href="#" class="scSelectorLocale scLocaleFocus" data-id="ES">Español</a></li>
		<!--4-->
		<li><a href="#" class="scSelectorLocale scLocaleFocus" data-id="EN">English</a></li>
	</ul> 
 </div><!-- /.navbar-collapse -->
 </div><!-- /.container-fluid -->
</nav>
<a href="http://www.exportechsolution.com/">
	<img id="exportechLogo" src="https://exportechsolution.com/admin/app/web/img/media/medium_logo.png" alt="Exportech Logo"/>
</a>
<nav class="navbar navbar-inverse">
  <div class="container-fullwidth">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainMenu" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-bars" aria-hidden="true"></i>
		<span>Menu</span>
      </button>
    </div>

	<?php
		$locale_id = $controller->pjActionGetLocale();
		$selected_lang = '';
		foreach ($tpl['locale_arr'] as $locale)
		{
			if($locale_id == $locale['id'])
			{
				$selected_lang = pjSanitize::html($locale['title']);
			}
			
		}
    ?>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="mainMenu">
    <?php 
     switch ($selected_lang) {
       	case 'English':
       		echo "<ul class=\"nav navbar-nav mainMenu\">
     			<li><a href=\"https://www.exportechsolution.com/en/homepage/\">Home</a></li>
		  		<li><a href=\"https://www.exportechsolution.com/en/about/\">About</a></li>
		  		<li><a href=\"https://www.exportechsolution.com/en/contact/\">Contact</a></li>
				<li><a href=\"https://www.exportechsolution.com/en/products/\">Products</a></li>
				<li class=\"current-menu-item\"><a href=\"#\">Shop</a></li>
				<li><a href=\"http://te-veo.com/\">Te-Veo</a></li>
				</ul>";
     		break;
     	default:
     	echo "<ul class=\"nav navbar-nav mainMenu\">
     			<li><a href=\"https://www.exportechsolution.com/es/inicio/\">Inicio</a></li>
		  		<li><a href=\"https://www.exportechsolution.com/es/quienes-somos/\">Quienes Somos</a></li>
		  		<li><a href=\"https://www.exportechsolution.com/es/contacto/\">Contacto</a></li>
				<li><a href=\"https://www.exportechsolution.com/es/productos/\">Productos</a></li>
				<li class=\"current-menu-item\"><a href=\"#\">Tienda</a></li>
				<li><a href=\"http://www.te-veo.com/\">Te-Veo</a></li>
				</ul>";
     		break;    		
    }
     ?>
    		
	</div><!-- /.navbar-collapse -->
 </div><!-- /.container-fluid -->
</nav>
	
<div class="container-fluid">
	<div class="row pjScBar">
		<div class="col-sm-6">
            <form action="" method="get" class="scSearchForm scSelectorSearchForm">
            	<div class="input-group">
                	<input type="text" name="q" value="<?php echo pjSanitize::html(urldecode(@$_GET['q'])); ?>" class="form-control" placeholder="<?php echo pjSanitize::html(__('front_search', true)); ?>">
                	<span class="input-group-btn">
                  		<button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                	</span>
              	</div>
            </form>
            <br>
		</div>
		<?php
		$isCheckoutReady = isset($tpl['price_arr']) && $tpl['price_arr']['status'] == 'OK';
		if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
		{
			$qty = 0;
			foreach($tpl['cart_arr'] as $v)
			{
				$qty += $v['qty'];
			}
			?>
	        <div class="col-sm-6">
	            <div class="clearfix">
	              	<div class="btn-group pull-right" role="group" aria-label="...">
	                	<a href="<?php echo pjUtil::getReferer(); ?>#!/Cart" class="btn btn-default">
	                  		<span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
	                  		<span class="text-warning"><?php echo $qty; ?></span>
	                  		<span class="text-uppercase"><?php $qty !== 1 ? __('front_items') : __('front_item'); ?></span>
	                	</a>
	                	<a href="<?php echo pjUtil::getReferer(); ?>#!/Cart" class="btn btn-default">
	                  		<span class="text-warning"><?php echo pjUtil::formatCurrencySign($isCheckoutReady ? number_format($tpl['price_arr']['data']['total'], 2) : '0.00', $tpl['option_arr']['o_currency']); ?></span>
	                	</a>
	                	<a href="<?php echo pjUtil::getReferer(); ?>#!/Cart" class="btn btn-default"><?php __('front_cart'); ?></a>
	              	</div>
	            </div>
			</div>
			<?php
		} 
		?>
	</div>
	<br class="visible-xs-inline-block">
	<?php
	if(1)
	{
		$isProduct = $_GET['controller'] == 'pjFrontPublic' && $_GET['action'] == 'pjActionProduct';
		$isProducts = $_GET['controller'] == 'pjFrontPublic' && $_GET['action'] == 'pjActionProducts' && isset($_GET['category_id']) && (int) $_GET['category_id'] > 0;
		if ($isProduct || $isProducts)
		{
			$category_id = NULL;
			if ($isProduct)
			{
				if (!empty($tpl['product_arr']['category_ids']))
				{
					$category_id = max($tpl['product_arr']['category_ids']);
					$_GET['category_id'] = $category_id;
				}
			} elseif ($isProducts) {
				$category_id = (int) $_GET['category_id'];
			}
		}
		?>
		<ul class="hidden-xs nav nav-pills pjScSort">
			<li class="<?php echo (!isset($_GET['category_id']) || empty($_GET['category_id'])) && !in_array($_GET['action'],
				array('pjActionCart', 'pjActionCheckout', 'pjActionPreview', 'pjActionLogin',
				'pjActionRegister', 'pjActionForgot', 'pjActionFavs', 'pjActionProfile', 'pjActionGetPaymentForm')) ? ' active' : NULL; ?>" role="presentation">
				<a href="#"><?php __('front_all'); ?></a>
			</li>
			
			<?php
			if (isset($tpl['category_arr']) && !empty($tpl['category_arr']))
			{
				if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
				{
					$ancestor = pjUtil::getAncestor($tpl['category_arr'], $_GET['category_id']);
				}
				
				foreach ($tpl['category_arr'] as $category)
				{
					if ($category['deep'] == 0)
					{
						?>
						<li class="dropdown<?php echo !isset($ancestor) || $ancestor != $category['data']['id'] ? NULL : ' active'; ?>" role="presentation">
							<a aria-expanded="false" role="button" href="javascript:void(0);" data-href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:/category:<?php echo $category['data']['id']; ?>/page:1" data-hover="dropdown" class="scDropDownMenu dropdown-toggle">
				             	<?php echo pjSanitize::html($category['data']['name']); ?> <span class="caret"></span>
				            </a>
				            <?php pjUtil::treeMenuLayout3($tpl['category_arr'], $category); ?>
						</li>
						<?php
					}
				}
			}
			?>
		</ul>
		<select name="category_id" class="form-control visible-xs scSelectorCategoryId">
			<option value=""><?php __('front_select_category'); ?></option>
			<?php
			foreach ($tpl['category_arr'] as $category)
			{
				?><option value="<?php echo $category['data']['id']; ?>"<?php echo !isset($_GET['category_id']) || (int) $_GET['category_id'] != $category['data']['id'] ? NULL : ' selected="selected"'; ?>><?php echo str_repeat("-----", $category['deep']) . " " .pjSanitize::html($category['data']['name']); ?></option><?php
			}
			?>
		</select>
		<br>
		<?php
	} 
	?>
</div>

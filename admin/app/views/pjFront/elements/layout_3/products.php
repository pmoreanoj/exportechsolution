<?php
if (isset($tpl['product_arr']) && !empty($tpl['product_arr']))
{
	?>
	<div class="container-fluid">
		<ul class="list-unstyled row text-center pjScProducts">
			<?php
			foreach($tpl['product_arr'] as $product)
			{
				$slug = NULL;
				if ((int) $tpl['option_arr']['o_seo_url'] === 1)
				{
					# Seo friendly URLs ---------
					$category_id = NULL;
					if (!empty($product['category_ids']))
					{
						$category_id = max($product['category_ids']);
					}
		
					$category_slug = array();
					if (!is_null($category_id))
					{
						$arr = array();
						pjUtil::getBreadcrumbTree($arr, $tpl['category_arr'], $category_id);
						krsort($arr);
						$arr = array_values($arr);
											
						foreach ($arr as $k => $category)
						{
							$category_slug[] = pjAppController::friendlyURL($category['data']['name']);
						}
					}
				
					$slug = sprintf("%s-%u.html", pjAppController::friendlyURL($product['name']), $product['id']);
					if (!empty($category_slug))
					{
						$slug = join("/", $category_slug) . '/' . $slug;
					}
					$href = pjUtil::getReferer() . '#!/' . $slug;
				} else {
					# Non-Seo friendly URLs ---------------------
					$href = pjUtil::getReferer() . '#!/Product/' . $product['id'];
				}
				$src = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png';
				if (is_file(PJ_INSTALL_PATH . $product['pic']))
				{
					$src = PJ_INSTALL_URL . $product['pic'];
				}
				?>
				<li class="col-xs-6 col-sm-4 col-md-3 pjScProduct">
					<p>
			        	<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>">
			        		<img class="img-responsive pjScProductImage" src="<?php echo $src; ?>" alt="<?php echo pjSanitize::html($product['name']); ?>" />
			        	</a>
			     	</p>
			     	<a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><?php echo pjSanitize::html($product['name']); ?></a>
			     	<?php
			     	if($product['is_digital'] == 0)
			     	{ 
				     	?>
			     		<p class="pjScProductPrice"><?php if ((float) $product['min_price'] < (float) $product['max_price']){ __('front_price_from'); }?> <?php echo pjUtil::formatCurrencySign(number_format($product['min_price'], 2), $tpl['option_arr']['o_currency']); ?></p>
			     		<?php
			     	}else{
			     		?>
     					<p class="pjScProductPrice"><?php echo pjUtil::formatCurrencySign(number_format($product['price'], 2), $tpl['option_arr']['o_currency']); ?></p>
     					<?php
			     	} 
			     	?>
			     	<?php
			     	if (($product['status'] != 3 && $product['stockQty'] - (int) @$tpl['order_arr'][$product['stockId']] > 0 && !empty($product['stockId']) && $product['is_digital'] == 0 ) || $product['is_digital'] == 1)
			     	{
			     		?>
			     		<div aria-label="Default button group" role="group" class="btn-group">
				     		<?php
				     		if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
				     		{
				     			?>
				     			<form action="" method="post" class="scSelectorBuyNowForm" style="display: inline; vertical-align: top">
				     				<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
									<input type="hidden" name="is_digital" value="<?php echo (int) $product['is_digital']; ?>" />
									<?php
									if (!empty($product['stockId_attr']))
									{
										$attrs = explode(",", $product['stockId_attr']);
										foreach ($attrs as $attr)
										{
											list($attr_id, $attr_parent_id) = explode("_", $attr);
											?>
											<input type="hidden" name="attr[<?php echo $attr_parent_id; ?>]" value="<?php echo $attr_id; ?>" />
											<?php
										}
									}
									if (!empty($product['m_extras']))
									{
										foreach ($product['m_extras'] as $ek => $ev)
										{
											?><input type="hidden" name="extra[<?php echo $ek; ?>]" value="<?php echo $ev; ?>" /><?php
										}
									}
									?>
									<input type="hidden" name="qty" value="1" />
									<input type="hidden" name="stock_id" value="<?php echo $product['stockId']; ?>" class="btn btn-primary scSelectorButton"/>
									<button class="btn btn-default scButton scButtonDark scSelectorButton pjScBtnPrimary" type="submit"><?php __('front_buy_now', false, true); ?></button>
				        			<a href="<?php echo $href; ?>" data-id="<?php echo $product['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"  class="btn btn-default scSelectorButton scSelectorProduct pjScBtnSecondary" type="button"><?php __('front_view_details'); ?></a>
				     			</form>
				     			<?php
				     		}
				     		?>
				      	</div>
			     		<?php	
			     	}else{
		     			if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
		     			{
		     				?>
		     				<button class="btn btn-primary pjScBtnPrimary" disabled="disabled" type="button"><?php __('front_out_of_stock'); ?></button>
							<?php		     				
		     			}
		     		} 
			     	?>
			      	<br><br>
				</li>
				<?php
			} 
			?>
		</ul>
	</div>
	<?php
	if (isset($tpl['paginator']))
	{
	 	if($tpl['paginator']['count'] > $tpl['paginator']['row_count'])
	 	{
			?>
			<div align="center">
				<nav>
					<ul class="pagination">
						<?php
						if ($tpl['paginator']['pages'] > 1 && $tpl['paginator']['page'] > 1)
						{ 
							$i = $tpl['paginator']['page'] - 1;
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode(@$_GET['q']); ?>/category:<?php echo @$_GET['category_id']; ?>/page:<?php echo $i; ?>" class="scSelectorPage pjScPaginationPrev" data-page="<?php echo $i; ?>" title="<?php __('front_prev', false, true); ?>"><span aria-hidden="true">&laquo;</span><span class="sr-only"><?php __('front_prev');?></span></a></li><?php
						}
						for ($i = 1; $i <= $tpl['paginator']['pages']; $i++)
						{
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode(@$_GET['q']); ?>/category:<?php echo @$_GET['category_id']; ?>/page:<?php echo $i; ?>" class="scSelectorPage<?php echo $tpl['paginator']['page'] != $i ? NULL : ' scPaginatorFocus'; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li><?php
						}
						if ($tpl['paginator']['pages'] > $tpl['paginator']['page'])
						{
							$i = $tpl['paginator']['page'] + 1;
							?><li><a href="<?php echo pjUtil::getReferer(); ?>#!/Products/q:<?php echo urlencode(@$_GET['q']); ?>/category:<?php echo @$_GET['category_id']; ?>/page:<?php echo $i; ?>" class="scSelectorPage pjScPaginationNext" data-page="<?php echo $i; ?>" title="<?php __('front_next', false, true); ?>"><span aria-hidden="true">&raquo;</span><span class="sr-only"><?php __('front_next');?></span></a></li><?php
						}
						?>
					</ul>
				</nav>
			</div>
			<?php
	 	}
	}
} 
?>
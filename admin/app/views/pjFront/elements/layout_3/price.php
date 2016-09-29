<?php
if ($tpl['price_arr']['status'] == 'OK' && ($_GET['action'] != 'pjActionCheckout' && $_GET['action'] != 'pjActionPreview'))
{
	foreach ($tpl['cart_arr'] as $cart_item)
	{
		$item = unserialize($cart_item['key_data']);
		$product = NULL;
		foreach ($tpl['arr'] as $p)
		{
			if ($p['id'] == $cart_item['product_id'])
			{
				$product = $p;
				break;
			}
		}
		if (is_null($product))
		{
			continue;
		}
		?>
		<div class="row">
			<label class="col-xs-12 text-right text-uppercase pjScProductPriceTitle"><?php echo pjSanitize::html($product['name']); ?></label>
			<?php
			if (isset($item['attr']) && !empty($item['attr']))
			{
				$attributes = array();
				foreach ($item['attr'] as $attr_parent_id => $attr_id)
				{
					foreach ($tpl['attr_arr'] as $attr)
					{
						if ($attr['id'] == $attr_parent_id && isset($attr['child']) && is_array($attr['child']))
						{
							foreach ($attr['child'] as $child)
							{
								if ($child['id'] == $attr_id)
								{
									$attributes[] = sprintf('%s: %s', pjSanitize::html($attr['name']), pjSanitize::html($child['name']));
									break;
								}
							}
						}
					}
				}
			}
			if (isset($item['extra']) && !empty($item['extra']))
			{
				$extras = array();
				foreach ($item['extra'] as $eid)
				{
					if (strpos($eid, ".") === FALSE)
					{
						foreach ($tpl['extra_arr'] as $extra)
						{
							if ($extra['id'] == $eid)
							{
								$extras[] = sprintf("%s %s", pjSanitize::html($extra['name']), pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']));
								break;
							}
						}
					} else {
						list($e_id, $ei_id) = explode(".", $eid);
						foreach ($tpl['extra_arr'] as $extra)
						{
							if ($extra['id'] == $e_id && isset($extra['extra_items']) && !empty($extra['extra_items']))
							{
								foreach ($extra['extra_items'] as $extra_item)
								{
									if ($extra_item['id'] == $ei_id)
									{
										$extras[] = sprintf("%s %s", pjSanitize::html($extra_item['name']), pjUtil::formatCurrencySign(number_format($extra_item['price'], 2), $tpl['option_arr']['o_currency']));
										break;
									}
								}
								break;
							}
						}
					}
				}
			} 
	  		if (!empty($attributes))
	  		{
	  			?><span class="col-xs-12 text-muted text-right text-uppercase">(<?php echo join(", ", $attributes);?>)</span><?php
	  		}
	  		if (!empty($extras))
	  		{
	  			?><span class="col-xs-12 text-muted text-right text-uppercase">(<?php echo join(", ", $extras);?>)</span><?php
	  		} 
	  		?>
	  		<label class="col-xs-12">&nbsp;</label>
		</div>
		<?php
	}
}
?>
<div class="row">
	<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_sub_total'); ?></div>
	<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['price'], 2), $tpl['option_arr']['o_currency']); ?></div>
	<label class="col-xs-12">&nbsp;</label>
</div>
<?php
if($tpl['price_arr']['data']['discount'] > 0)
{ 
	?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_discount'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['discount'], 2), $tpl['option_arr']['o_currency']); ?></div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
	<?php
}
if($tpl['price_arr']['data']['insurance'] > 0)
{ 
	?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_insurance'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['insurance'], 2), $tpl['option_arr']['o_currency']); ?></div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
	<?php
} 
if($tpl['price_arr']['data']['shipping'] > 0)
{
	?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_shipping'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['shipping'], 2), $tpl['option_arr']['o_currency']); ?></div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
	<?php
}
if($tpl['price_arr']['data']['tax'] > 0)
{ 
	?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_tax'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['tax'], 2), $tpl['option_arr']['o_currency']); ?></div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
	<?php
} 
?>
<div class="row">
	<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_total'); ?></div>
	<label class="col-xs-6 text-uppercase pjScCheckoutPrice"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['total'], 2), $tpl['option_arr']['o_currency']); ?></label>
</div>
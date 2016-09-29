<div class="container-fluid pjScPreviewOrder">
	<h2 class="text-uppercase text-primary pjScPreviewTitle"><strong><?php __('front_preview_order'); ?></strong></h2><br>
	<?php
	if (isset($tpl['status']) && $tpl['status'] == 'OK')
	{
		$STORAGE = @$_SESSION[$controller->defaultForm];
		?>
		<form action="" method="post" class="scForm scSelectorPreviewForm">
			<input type="hidden" name="sc_preview" value="1" />
			
			<div class="col-sm-3">
				<?php //include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/price.php';?>
				<!-- CODIGO DE price.php-->
				<?php
			//Compute tax
			if($controller->isLoged()){
				$pjClientModel = new pjClientModel();
				$query = $pjClientModel
						->select('tax_exempt')
						->where('id',$controller->getUserId())
						->findAll()
						->getData();
				$tax_exempt = $query[0]['tax_exempt'];
			}else{
				$pjClientModel = new pjClientModel();
				$query = $pjClientModel
						->select('tax_exempt')
						->where('email',pjSanitize::html(@$STORAGE['email']))
						->findAll()
						->getData();
				$tax_exempt = $query[0]['tax_exempt'];
			}

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
	$tax_int = floatval($tpl['price_arr']['data']['tax']);
	
	if($tax_exempt == 'F'){
	?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_tax'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['tax'], 2), $tpl['option_arr']['o_currency']); ?></div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
<?php
	}else{
?>
    <div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_tax'); ?></div>
		<div class="col-xs-6 text-muted text-uppercase"><?php echo pjUtil::formatCurrencySign(number_format(0, 2), $tpl['option_arr']['o_currency']); ?>&nbsp&nbsp&nbsp&nbspTax Exempt</div>
		<label class="col-xs-12">&nbsp;</label>
	</div>
<?php	
	}
}
if($tax_exempt == 'T' && isset($tax_int)){
	$total_int = floatval($tpl['price_arr']['data']['total']);
	$total_int = $total_int - $tax_int;
?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_total'); ?></div>
		<label class="col-xs-6 text-uppercase pjScCheckoutPrice"><?php echo pjUtil::formatCurrencySign(number_format($total_int, 2), $tpl['option_arr']['o_currency']); ?></label>
	</div>
<?php	
}else{
?>
	<div class="row">
		<div class="col-xs-6 text-right text-muted text-uppercase"><?php __('front_total'); ?></div>
		<label class="col-xs-6 text-uppercase pjScCheckoutPrice"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['data']['total'], 2), $tpl['option_arr']['o_currency']); ?></label>
	</div>

<?php	
}
?>
			</div>
			<div class="col-sm-9">
				<div class="row">
					<?php
					if (!$controller->isLoged())
					{
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_customer'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<label class="control-label"><?php __('client_email'); ?></label><br/>
									    <span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['email']); ?></span>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_password'); ?></label><br/>
									   		<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['password']); ?></span>
									  	</div>
									</div>
								</div>
								<?php
								ob_start();
								$number_of_cols = 0;
								if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3)))
								{
									?>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_name'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['client_name']); ?></span>
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3)))
								{
									?>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_phone'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['phone']); ?></span>
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								if($number_of_cols == 2)
								{
									$ob_fields = ob_get_contents();
									ob_end_clean();
									?>
									<div class="row"><?php echo $ob_fields; ?></div>
									<?php
									ob_start();
									$number_of_cols = 0;
								}
								if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3)))
								{
									?>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('client_url'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['url']); ?></span>
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
							</div>
						</div>
						<?php
					} 
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><strong><?php __('order_billing_details'); ?></strong></div>
						<div class="panel-body">
							<?php
							ob_start();
							$number_of_cols = 0;
							if (in_array((int) $tpl['option_arr']['o_bf_b_name'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_name'); ?></label><br/>
								    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_name']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_country_id'], array(2,3)))
							{ 
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_country'); ?></label><br/>
								    	<span class="text-muted">
								    		<?php
								    		foreach ($tpl['country_arr'] as $country)
								    		{
								    			if ($country['id'] == @$STORAGE['b_country_id'])
								    			{
								    				echo pjSanitize::html($country['name']);
								    				break;
								    			}
								    		}
								    		?>
								    	</span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_state'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_state'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_state']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							
							if (in_array((int) $tpl['option_arr']['o_bf_b_city'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_city'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_city']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_zip'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_zip'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_zip']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							} 
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_address_1'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_address_1'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_address_1']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if($number_of_cols == 2)
							{
								$ob_fields = ob_get_contents();
								ob_end_clean();
								?>
								<div class="row"><?php echo $ob_fields; ?></div>
								<?php
								ob_start();
								$number_of_cols = 0;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_address_2'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('client_address_2'); ?></label><br/>
						   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['b_address_2']); ?></span>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							$ob_fields = ob_get_contents();
							ob_end_clean();
							?><div class="row"><?php echo $ob_fields; ?></div>
							
						</div>
					</div><!-- panel-default -->
					<?php
					if ($controller->pjActionShowShipping())
					{
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_shipping_details'); ?></strong></div>
							<div class="panel-body">
								<?php
								if (isset($STORAGE['same_as']))
								{
									?>
									<div class="row">
										<div class="col-sm-6">
										  	<div class="form-group">
								   	 			<span class="text-muted"><?php __('order_same'); ?></span>
										  	</div>
										</div>
									</div>
									<?php
								}else{
									ob_start();
									$number_of_cols = 0;
									if (in_array((int) $tpl['option_arr']['o_bf_s_name'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_name'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_name']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_country_id'], array(2,3)))
									{ 
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_country'); ?></label><br/>
										    	<span class="text-muted">
										    		<?php
													foreach ($tpl['country_arr'] as $country)
													{
														if ($country['id'] == @$STORAGE['s_country_id'])
														{
															echo pjSanitize::html($country['name']);
															break;
														}
													}
													?>
										    	</span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_state'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_state'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_state']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_city'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_city'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_city']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_zip'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_zip'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_zip']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									} 
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_address_1'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_address_1'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_address_1']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									if($number_of_cols == 2)
									{
										$ob_fields = ob_get_contents();
										ob_end_clean();
										?>
										<div class="row"><?php echo $ob_fields; ?></div>
										<?php
										ob_start();
										$number_of_cols = 0;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_address_2'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group">
										    	<label class="control-label"><?php __('client_address_2'); ?></label><br/>
								   	 			<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['s_address_2']); ?></span>
										  	</div>
										</div>
										<?php
										$number_of_cols++;
									}
									$ob_fields = ob_get_contents();
									ob_end_clean();
									?>
									<div class="row"><?php echo $ob_fields; ?></div>
									<?php
								} 
								?>
							</div>
						</div>
						<?php
					}
					if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
					{
						$payment_methods = __('payment_methods', true);
						$cc_types = __('cc_types', true);
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_payment_details'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_payment'); ?></label><br/>
									    	<span class="text-muted"><?php echo @$payment_methods[$STORAGE['payment_method']]; ?></span>
									  	</div>
									</div>
									<div class="col-sm-6 scBankWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'bank' ? 'none' : NULL; ?>">
										<div class="form-group">
									    	<label class="control-label"><?php __('bf_bank_account'); ?></label><br/>
									    	<span class="text-muted"><?php echo nl2br($tpl['option_arr']['o_bank_account']); ?></span>
									  	</div>
									</div>
								</div>
								<div class="row scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_cc_type'); ?></label><br/>
									    	<span class="text-muted"><?php echo @$cc_types[@$STORAGE['cc_type']]; ?></span>
									  	</div>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_cc_num'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['cc_num']); ?></span>
									  	</div>
									</div>
								</div>
								<div class="row scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_cc_sec'); ?></label><br/>
									    	<span class="text-muted"><?php echo pjSanitize::html(@$STORAGE['cc_code']); ?></span>
									  	</div>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group">
									    	<label class="control-label"><?php __('bf_cc_exp'); ?></label><br/>
									    	<span class="text-muted"><?php printf("%s/%s", @$STORAGE['cc_exp_month'], @$STORAGE['cc_exp_year']); ?></span>
									  	</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><strong>ATTENTION<?php //__('order_other_details'); ?></strong></div>
						<div class="panel-body">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_notes'], array(2,3)))
							{
								?>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
									    	<label class="control-label"><?php __('bf_notes'); ?></label><br/>
									    	<div class="text-muted"><?php echo nl2br(pjSanitize::html(@$STORAGE['notes'])); ?></div>
									  	</div>
									</div>
								</div>
								<?php
							} 
							?>
							<!-- Add spam notes-->
								<p style="color:red;">***After confirming your order, you will receive an email with a summary of all your order details, please
								take into account that mails can go to your SPAM folder. If you do not receive the email please contact us as soon
								as possible.</p>
						</div>
					</div>
					
					<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
					
					<div>
						<button type="button" class="btn btn-default scSelectorButton scSelectorEditOrder pjScBtnSecondary"><?php __('front_edit_order', false, true); ?></button>
						<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary" ><?php __('front_confirm_procees', false, true); ?></button>
					</div>
					<br/>
					
				</div><!-- col-sm-9 -->
			</div>
		</form>
		<?php
	}elseif (isset($tpl['status']) && $tpl['status'] == 'ERR') {
		if (isset($tpl['code']))
		{
			switch ($tpl['code'])
			{
				case 100:
					?><div class="alert alert-warning" role="alert"><?php __('front_empty_shipping_location'); ?></div><?php
					break;
				case 101:
					?><div class="alert alert-warning" role="alert"><?php __('front_empty_shopping_cart'); ?></div><?php
					break;
				case 102:
					?><div class="alert alert-warning" role="alert">v<?php __('front_empty_checkout_form'); ?></div><?php
					break;
			}
		}
	} 
	?>
</div>
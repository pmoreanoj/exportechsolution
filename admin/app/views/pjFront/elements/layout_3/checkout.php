<?php
	$language = $controller->pjActionGetLocale();
	$tax_id = $tpl['tax_name'];
	$pjMultiLangModel = new pjMultiLangModel();
	$query = $pjMultiLangModel
				->select('content')
				->where('model', 'pjTax')
				->where('locale', $language)
				->where('foreign_id', $tax_id)
				->findAll()
				->getData();
	$s_state = $query[0]['content'];
?>
<div class="container-fluid pjScCheckout">
	<h2 class="text-uppercase text-primary pjScCheckoutTitle"><strong><?php __('front_checkout'); ?></strong></h2><br>
	<?php
	if (isset($tpl['status']) && $tpl['status'] == 'OK')
	{
		$bSaveReady = $sSaveReady = $bSaveChecked = $sSaveChecked = false;
		
		$STORAGE = @$_SESSION[$controller->defaultForm];
		$billing = $shipping = $STORAGE;
		$isLoged = $controller->isLoged();
		if ($isLoged && is_null($STORAGE))
		{
			if (isset($tpl['address_arr']) && !empty($tpl['address_arr']))
			{
				foreach ($tpl['address_arr'] as $address)
				{
					if ((int) $address['is_default_shipping'] === 1)
					{
						$shipping = array(
								's_address_id' => $address['id'],
								's_name' => $address['name'],
								's_country_id' => $address['country_id'],
								's_city' => $address['city'],
								's_state' => $address['state'],
								's_zip' => $address['zip'],
								's_address_1' => $address['address_1'],
								's_address_2' => $address['address_2']
						);
					}
		
					if ((int) $address['is_default_billing'] === 1)
					{
						$billing = array(
								'b_address_id' => $address['id'],
								'b_name' => $address['name'],
								'b_country_id' => $address['country_id'],
								'b_city' => $address['city'],
								'b_state' => $address['state'],
								'b_zip' => $address['zip'],
								'b_address_1' => $address['address_1'],
								'b_address_2' => $address['address_2']
						);;
					}
				}
			}
				
			if (empty($shipping))
			{
				$shipping = array(
						's_name' => $_SESSION[$controller->defaultUser]['client_name']
				);
			}
			if (empty($billing))
			{
				$billing = array(
						'b_name' => $_SESSION[$controller->defaultUser]['client_name']
				);
			}
		}
		if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']) && isset($STORAGE['b_save']))
		{
			$bSaveReady = true;
			$bSaveChecked = true;
		}
		
		if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']) && isset($STORAGE['s_save']))
		{
			$sSaveReady = true;
			$sSaveChecked = true;
		}
		
		?>
		<form action="" method="post" class="scSelectorCheckoutForm">
			<input type="hidden" name="sc_checkout" value="1" />
			
			<div class="col-sm-3">
				<?php include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/price.php';?>
			</div>
			<div class="col-sm-9">
				<div class="row">
					<?php
					if (!$isLoged)
					{ 
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_customer'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('client_email'); ?></label>
									    	<input type="text" name="email" class="form-control required email" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['email']); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>">
									  	</div>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('client_password'); ?></label>
									    	<input type="password" name="password" class="form-control required" placeholder="<?php __('front_placeholder_password', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['password']); ?>" data-err="<?php echo $validate['password'];?>">
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
									  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : null;?>">
									    	<label class="control-label"><?php __('client_name'); ?></label>
									    	<input type="text" name="client_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['client_name']); ?>" data-err="<?php echo $validate['name'];?>">
									  	</div>
									</div>
									<?php
									$number_of_cols++;
								}
								if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3)))
								{
									?>
									<div class="col-sm-6">
									  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : null;?>">
									    	<label class="control-label"><?php __('client_phone'); ?></label>
									    	<input type="text" name="phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['phone']); ?>" data-err="<?php echo $validate['phone'];?>">
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
									  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : null;?>">
									    	<label class="control-label"><?php __('client_url'); ?></label>
									    	<input type="text" name="url" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo pjSanitize::html(@$STORAGE['url']); ?>" data-err="<?php echo $validate['url'];?>">
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
							if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']))
							{ 
								?>
								<div class="col-sm-6">
								  	<div class="form-group">
								    	<label class="control-label"><?php __('order_address'); ?></label>
								    	<select name="b_address_id" class="form-control scSelectorAddressId">
								    		<option value=""><?php __('choose_address'); ?></option>
								    		<?php
											foreach ($tpl['address_arr'] as $address)
											{
												$addr = array($address['city'], $address['zip'], $address['address_1']);
												?><option value="<?php echo $address['id']; ?>"<?php echo $address['id'] != @$billing['b_address_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html(join(", ", array_filter($addr))); ?></option><?php
											}
											?>
								    	</select>
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							if (in_array((int) $tpl['option_arr']['o_bf_b_name'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_name'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_name'); ?></label>
						   	 			<input type="text" name="b_name" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_name']); ?>" placeholder="<?php __('front_placeholder_b_name', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_name']); ?>" data-err="<?php echo $validate['name'];?>">
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_country_id'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_country'); ?></label>
								    	<select name="b_country_id" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_country_id'] === 3 ? ' required' : NULL; ?>" data-original="<?php echo pjSanitize::html(@$billing['b_country_id']); ?>">
											<option value=""><?php __('choose_country'); ?></option>
											<?php
											foreach ($tpl['country_arr'] as $country)
											{
												?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] != @$billing['b_country_id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
											}
											?>
										</select>
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_state'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_state'); ?></label>
						   	 			<input type="text" name="b_state" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_state']); ?>" placeholder="<?php __('front_placeholder_b_state', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_state']); ?>" data-err="<?php echo $validate['state'];?>">
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_city'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_city'); ?></label>
						   	 			<input type="text" name="b_city" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_city']); ?>" placeholder="<?php __('front_placeholder_b_city', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_city']); ?>" data-err="<?php echo $validate['city'];?>">
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_zip'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_zip'); ?></label>
						   	 			<input type="text" name="b_zip" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_zip']); ?>" placeholder="<?php __('front_placeholder_b_zip', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_zip']); ?>" data-err="<?php echo $validate['zip'];?>">
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_address_1'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_address_1'); ?></label>
						   	 			<input type="text" name="b_address_1" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_address_1'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_address_1']); ?>" placeholder="<?php __('front_placeholder_b_address_1', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_address_1']); ?>" data-err="<?php echo $validate['address_1'];?>">
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
								  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_b_address_2'] === 3 ? ' required' : null;?>">
								    	<label class="control-label"><?php __('client_address_2'); ?></label>
						   	 			<input type="text" name="b_address_2" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_b_address_2'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$billing['b_address_2']); ?>" placeholder="<?php __('front_placeholder_b_address_2', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['b_address_2']); ?>" data-err="<?php echo $validate['address_2'];?>">
								  	</div>
								</div>
								<?php
								$number_of_cols++;
							}
							$ob_fields = ob_get_contents();
							ob_end_clean();
							?><div class="row"><?php echo $ob_fields; ?></div>
							
							<div class="row scSelectorSaveB" style="display:<?php echo $bSaveReady ? NULL : 'none'; ?>">
								<div class="col-sm-6">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="b_save" value="1"<?php echo $bSaveChecked ? ' checked="checked"' : NULL; ?> /> <?php __('save_to_address_book'); ?>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div><!-- panel-default -->
					<?php
					if ($controller->pjActionShowShipping())
					{
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_shipping_details'); ?></strong></div>
							<div class="panel-body">
					 			<div class="scSelectorBoxShipping" style="display: <?php echo isset($STORAGE['same_as']) ? 'none' : NULL; ?>">
									<?php
									ob_start();
									$number_of_cols = 0;
									if ($isLoged && isset($tpl['address_arr']) && !empty($tpl['address_arr']))
									{ 
										?>
										<?php
										$number_of_cols++;
									}
									if (in_array((int) $tpl['option_arr']['o_bf_s_name'], array(2,3)))
									{
										?>
										<div class="col-sm-6">
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_name'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_name'); ?></label>
								   	 			<input type="text" name="s_name" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_name']); ?>" placeholder="<?php __('front_placeholder_s_name', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_name']); ?>" data-err="<?php echo $validate['name'];?>">
										  	</div>
										</div>
										<div class="col-sm-6">
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_country_id'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_country'); ?></label>
										    	<select name="s_country_id" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_country_id'] === 3 ? ' required' : NULL; ?>" data-original="<?php echo pjSanitize::html(@$billing['s_country_id']); ?>" data-err="<?php echo $validate['country'];?>">
													<option value="236">United States</option>
												</select>
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_state'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_state'); ?></label> 
												<br />
												<label class="form-control scSelectorOriginalB"><?php echo $s_state;?></label>
								   	 			<div style="display:none;"><input type="text" name="s_state" class="form-control scSelectorOriginalB<?php echo (int) $tpl['option_arr']['o_bf_s_state'] === 3 ? ' required' : NULL; ?>" value=<?php echo $s_state;?> data-err="<?php echo $validate['state'];?>" >
												</div>
											<!-- In here include the state you selected in the cart for the shipping. Just the state. -->
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_city'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_city'); ?></label>
												
												<?php if($s_state == 'Pick-Up'){ ?>
													<label class="form-control scSelectorOriginalB">Miami</label>
													<div style="display:none;"> <input type="text" name="s_city" class="form-control scSelectorOriginalB <?php echo (int) $tpl['option_arr']['o_bf_s_city'] === 3 ? ' required' : NULL; ?>" value="Miami" />
													</div>
												<?php
												}
												else{ ?>
													<input type="text" name="s_city" class="form-control scSelectorOriginalB <?php echo (int) $tpl['option_arr']['o_bf_s_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_city']); ?>" placeholder="<?php __('front_placeholder_s_city', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_city']); ?>" data-err="<?php echo $validate['city'];?>" />
												<?php
												}
												?>
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_zip'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_zip'); ?></label>
												
												<?php if($s_state == 'Pick-Up'){ ?>
													<label class="form-control scSelectorOriginalB">33166</label>
													<div style="display:none;">
														<input type="text" name="s_zip" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_zip'] === 3 ? ' required' : NULL; ?>" value="33166">
													</div>
												<?php 
												}
												else{ ?>
													<input type="text" name="s_zip" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_zip']); ?>" placeholder="<?php __('front_placeholder_s_zip', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_zip']); ?>" data-err="<?php echo $validate['zip'];?>">
												<?php
												}
												?>
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_address_1'] === 3 ? ' required' : null;?>">
										    	<label class="control-label"><?php __('client_address_1'); ?></label>
												
												<?php if($s_state == 'Pick-Up'){ ?>
													<label class="form-control scSelectorOriginalB">4418 N.W 74th Avenue</label>
													<div style="display:none;">
														<input type="text" name="s_address_1" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_address_1'] === 3 ? ' required' : NULL; ?>" value="4418 N.W 74th Avenue">
													</div>
												<?php
												}
												else{ ?>
													<input type="text" name="s_address_1" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_address_1'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_address_1']); ?>" placeholder="<?php __('front_placeholder_s_address_1', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_address_1']); ?>" data-err="<?php echo $validate['address_1'];?>">
												<?php
												}
												?>
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
										  	<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_s_address_2'] === 3 ? ' required' : null;?>">
												
												<?php if($s_state == 'Pick-Up'){ 
												
												}
												else{ ?>
													<label class="control-label"><?php __('client_address_2'); ?></label>
													<input type="text" name="s_address_2" class="form-control scSelectorOriginalS<?php echo (int) $tpl['option_arr']['o_bf_s_address_2'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$shipping['s_address_2']); ?>" placeholder="<?php __('front_placeholder_s_address_2', false, true); ?>" data-original="<?php echo pjSanitize::html(@$billing['s_address_2']); ?>" data-err="<?php echo $validate['address_2'];?>">
												<?php
												}
												?>
												
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
						</div>
						<?php
					}
					if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
					{
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><?php __('order_payment_details'); ?></strong></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('bf_payment'); ?></label>
									    	<select name="payment_method" class="form-control required" data-err="<?php echo $validate['payment'];?>">
									    		<option value="">-- Choose payment --</option>
												<option value="bank">Bank account</option>
												<option value="paypal">Credit Card\PayPal</option>
									    	</select>
									  	</div>
									</div>
									<div class="col-sm-6 scBankWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'bank' ? 'none' : NULL; ?>">
										<div class="form-group">
									    	<label class="control-label"><?php __('bf_bank_account'); ?></label>
									    	<div class="text-muted"><?php echo nl2br($tpl['option_arr']['o_bank_account']); ?></div>
									  	</div>
									</div>
								</div>
								<div class="row scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('bf_cc_type'); ?></label>
									    	<select name="cc_type" class="form-control required">
									    		<option value="">---</option>
									    		<?php
												foreach (__('cc_types', true) as $k => $v)
												{
													?><option value="<?php echo $k; ?>"<?php echo @$STORAGE['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
												}
												?>
									    	</select>
									  	</div>
									</div>
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('bf_cc_num'); ?></label>
									    	<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$STORAGE['cc_num']); ?>" placeholder="<?php __('front_placeholder_cc_number', false, true); ?>" />
									  	</div>
									</div>
								</div>
								<div class="row scCcWrap" style="display: <?php echo @$STORAGE['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
									<div class="col-sm-6">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('bf_cc_sec'); ?></label>
									    	<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$STORAGE['cc_code']); ?>" placeholder="<?php __('front_placeholder_cc_code', false, true); ?>" />
									  	</div>
									</div>
									<div class="col-sm-3">
									  	<div class="form-group required">
									    	<label class="control-label"><?php __('bf_cc_exp'); ?></label>
									    	<select name="cc_exp_month" class="form-control required">
									    		<option value="">---</option>
									    		<?php
									    		$month_arr = __('months', true);
									    		ksort($month_arr);
												foreach ($month_arr as $k => $v)
												{
													$key = str_pad($k, 2, '0', STR_PAD_LEFT); 
													?><option value="<?php echo $key; ?>"<?php echo @$STORAGE['cc_exp_month'] != $key ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
												}
												?>
									    	</select>
									  	</div>
									</div>
									<div class="col-sm-3">
									  	<div class="form-group">
									    	<label class="control-label">&nbsp;</label>
									    	<?php
											$time = pjTime::factory()
												->attr('name', 'cc_exp_year')
												->attr('id', 'cc_exp_year_' . $rand)
												->attr('class', 'form-control required')
												->prop('left', 0)
												->prop('right', 10);
											if (isset($STORAGE['cc_exp_year']) && !is_null($STORAGE['cc_exp_year']))
											{
												$time->prop('selected', $STORAGE['cc_exp_year']);
											}
											echo $time->year();
											?>
									  	</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><strong><?php __('order_other_details'); ?></strong></div>
						<div class="panel-body">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_notes'], array(2,3)))
							{
								?>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_notes'] === 3 ? ' required' : null;?>">
									    	<label class="control-label"><?php __('bf_notes'); ?></label>
									    	<textarea name="notes" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_notes'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_notes', false, true); ?>" rows="10" data-err="<?php echo $validate['notes'];?>"><?php echo pjSanitize::html(@$STORAGE['notes']); ?></textarea>
									  	</div>
									</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_captcha'], array(3)))
							{
								?>
								<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_captcha'] === 3 ? ' required' : null;?>">
								  	<label class="control-label"><?php __('bf_captcha'); ?></label>
									<div class="row">
									  	<div class="col-xs-6 col-md-3">
									    	<input type="text" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>">
									  	</div>
									  	<div class="col-xs-6">
									    	<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?>" alt="Captcha" style="vertical-align: middle" />
									  	</div>
									</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_terms'], array(3)))
							{
								?>
								<div class="checkbox form-group required">
								    <label style="display: block;">
								      	<input type="checkbox" name="terms" value="1" class="<?php echo (int) $tpl['option_arr']['o_bf_terms'] === 3 ? ' required' : NULL; ?>" data-err="<?php echo $validate['terms'];?>">
								      	<?php
								      	if (isset($tpl['terms']) && isset($tpl['terms']['terms_url']) && !empty($tpl['terms']['terms_url']) && preg_match('/^http(s)?:\/\//i', $tpl['terms']['terms_url']))
								      	{
								      		?><a href="<?php echo $tpl['terms']['terms_url']; ?>" target="_blank"><?php __('bf_terms'); ?></a><?php
								      	}elseif (isset($tpl['terms']) && isset($tpl['terms']['terms_body']) && !empty($tpl['terms']['terms_body'])) {
								      		?><a href="javascript:void(0);" data-toggle="modal" data-target="#scTermModal" data-title="<?php __('front_terms_title', false, true); ?>"><?php __('bf_terms'); ?></a><?php
								      	}else{
								      		__('bf_terms');
								      	} 
								      	?> 
								    </label>
								</div>
								<?php
								if (isset($tpl['terms']) && isset($tpl['terms']['terms_body']) && !empty($tpl['terms']['terms_body']))
								{
									?>
									<div class="modal fade" id="scTermModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									  	<div class="modal-dialog">
									    	<div class="modal-content">
									      		<div class="modal-header">
									        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									        		<h4 class="modal-title" id="myModalLabel"><?php __('front_terms_title', false, true); ?></h4>
									      	</div>
										    <div class="modal-body">
										    	<?php echo nl2br(stripslashes($tpl['terms']['terms_body'])); ?>
										    </div>
									      	<div class="modal-footer">
									        	<button type="button" class="btn btn-default pjScBtnSecondary" data-dismiss="modal">OK</button>
									      	</div>
									    </div>
									  </div>
									</div>
									<div class="scSelectorTermsBody" style="display: none"><?php echo nl2br(stripslashes($tpl['terms']['terms_body'])); ?></div>
									<?php
								}
							}
							?>
						</div>
					</div>
					
					<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
					
					<div>
						<button class="btn btn-default scSelectorButton scSelectorViewCart pjScBtnSecondary"><?php __('front_back', false, true); ?></button>
						<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary" ><?php __('front_preview_order', false, true); ?></button>
					</div>
					<br/>
				</div>
			</div>
		</form>
		<?php
	}else{
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
			}
		}
	} 
	?>
</div>
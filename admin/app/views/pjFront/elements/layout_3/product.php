<div class="container-fluid">
	<div class="row pjScProductDetails">
		<div class="col-sm-3">
			<?php
			$medium_pic = $large_pic = NULL;
			if (!empty($tpl['product_arr']['pic']))
			{
				list($medium_pic, $large_pic) = explode("~:~", $tpl['product_arr']['pic']);
			}
			if (!empty($medium_pic) && is_file($medium_pic))
			{
				?>
				<p><a href="<?php echo PJ_INSTALL_URL . $large_pic; ?>" rel="fancy_groupXXX" class="scSelectorFancy"><img src="<?php echo PJ_INSTALL_URL . $medium_pic; ?>" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="img-responsive scSelectorProductPic" /></a></p>
				<?php	
			} elseif (!empty($tpl['product_arr']['gallery_arr'][0]['medium_path']) && is_file($tpl['product_arr']['gallery_arr'][0]['medium_path'])) {
				?><p><a href="<?php echo PJ_INSTALL_URL . $tpl['product_arr']['gallery_arr'][0]['large_path']; ?>" rel="fancy_groupXXX" class="scSelectorFancy"><img src="<?php echo PJ_INSTALL_URL . $tpl['product_arr']['gallery_arr'][0]['medium_path']; ?>" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="img-responsive scSelectorProductPic" /></a></p><?php
			} else {
				?><p><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>frontend/noimg.png" alt="<?php echo pjSanitize::html($tpl['product_arr']['name']); ?>" class="img-responsive scSelectorProductPic" /></p><?php
			}
			
			$img = 0;
			$cnt = count($tpl['product_arr']['gallery_arr']);
			foreach ($tpl['product_arr']['gallery_arr'] as $k => $image)
			{
				if ($img === 0)
				{
					?>
					<ul class="list-inline">
					<?php
				}
				if (!empty($image['small_path']) && is_file(PJ_INSTALL_PATH . $image['small_path']))
				{
					?>
					<li>
						<p>
							<a href="<?php echo $href; ?>" rel="nofollow" data-src="<?php echo PJ_INSTALL_URL . $image['medium_path']; ?>" data-large="<?php echo PJ_INSTALL_URL . $image['large_path']; ?>" class="scSelectorProductThumb"><img src="<?php echo PJ_INSTALL_URL . $image['small_path']; ?>" alt="<?php echo pjSanitize::html($image['alt']); ?>" /></a>
							<a href="<?php echo PJ_INSTALL_URL . $image['large_path']; ?>" rel="fancy_group" style="display:none"></a>
						</p>
					</li>
					<?php
					$img++;
				}
				if ($k == $cnt - 1)
				{
					?>
					</ul>
					<?php
				}
			}
			$product_statuses = __('product_statuses', true);
			foreach ($tpl['product_arr']['image_arr'] as $k => $v)
			{
				?><span class="scSelectorStockThumb" data-stock_id="<?php echo $v['stock_id']?>" data-src="<?php echo PJ_INSTALL_URL . $v['medium_path']; ?>" data-large="<?php echo PJ_INSTALL_URL . $v['large_path']; ?>" style="display: none"></span><?php
			}
			?>
		</div>
		<div class="col-sm-9">
			<h3 class="text-primary text-uppercase pjScProductTitle"><strong><?php echo pjSanitize::html($tpl['product_arr']['name']); ?></strong></h3>
			<?php
			if ((int) $tpl['product_arr']['status'] == 1)
			{ 
				?>
				<p class="h4">
					<?php
					if($tpl['product_arr']['price'] == $tpl['product_arr']['max_price'])
					{ 
						?>
						<strong class="scSelectorPrice"><?php echo pjUtil::formatCurrencySign(number_format($tpl['product_arr']['price'], 2), $tpl['option_arr']['o_currency']); ?></strong>
						<span style="display:none;" class="scHiddenMinPrice"><?php echo pjUtil::formatCurrencySign(number_format($tpl['product_arr']['price'], 2), $tpl['option_arr']['o_currency']); ?></span>
						<?php
					}else{
						?>
						<strong class="scSelectorPrice"><?php __('front_price_from');?> <?php echo pjUtil::formatCurrencySign(number_format($tpl['product_arr']['price'], 2), $tpl['option_arr']['o_currency']); ?></strong>
						<span style="display:none;" class="scHiddenMinPrice"><?php __('front_price_from');?> <?php echo pjUtil::formatCurrencySign(number_format($tpl['product_arr']['price'], 2), $tpl['option_arr']['o_currency']); ?></span>
						<?php
					} 
					?>
					<input type="hidden" class="scInputMinPrice" value="<?php echo $tpl['product_arr']['price'];?>" />
				</p>
				<?php
			} else {
				?><p class="h4"><strong><?php __('front_not_available'); ?></strong></p><?php
			}
			?>
			<div class="panel panel-default">
				<div class="panel-body">
					<form action="" method="post" class="scSelectorProductForm">
						<input type="hidden" name="product_id" value="<?php echo @$_GET['id']; ?>" />
						<input type="hidden" name="is_digital" value="<?php echo (int) $tpl['product_arr']['is_digital']; ?>" />
						<div class="row">
							<?php
							$inStock = (isset($tpl['stock_attr_arr']) && !empty($tpl['stock_attr_arr'])) || ($tpl['product_arr']['is_digital'] == '1');
							if ($inStock)
							{
								if (isset($tpl['attr_arr']) && !empty($tpl['attr_arr']))
								{
									foreach ($tpl['attr_arr'] as $row => $attr)
									{
										?>
										<div class="col-xs-6 col-md-3">
											<div class="form-group">
												<label><?php echo pjSanitize::html($attr['name']); ?></label>
												<select name="attr[<?php echo $attr['id']; ?>]" class="form-control scSelectorAttr pjScAttributes required" data-row="<?php echo $row; ?>" data-id="<?php echo $attr['id']; ?>" data-choose="-- <?php __('front_choose'); ?> --" data-msg-required="<?php __('front_field_required');?>">
													<option value="">-- <?php __('front_choose'); ?> --</option>
													<?php
													if (isset($attr['child']) && !empty($attr['child']))
													{
														foreach ($attr['child'] as $child_index => $child)
														{
															foreach ($tpl['stock_attr_arr'] as $stock_id => $stock)
															{
																
																if (in_array($child['id'], $stock) || (isset($stock[$child['parent_id']]) && (int) $stock[$child['parent_id']] === 0))
																{
																	if ($row == 0 && $child_index == 0)
																	{
																		$tmp_stock_id = $stock_id;
																	}
																	if ($row > 0)
																	{
																		if (!isset($tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']]) ||
																				$tpl['stock_attr_arr'][$tmp_stock_id][$child['parent_id']] != $child['id'])
																		{
																			continue;
																		}
																	}
																	?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
																	break;
																}
															}
														}
													}else {
														?><option value=""><?php __('front_not_available'); ?></option><?php
													} 
													?>
								    			</select>
											</div>
										</div>
										<?php
									}
								}
								if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
								{
									$max_qty = (int) $tpl['stock_arr'][0]['qty'];
									if($tpl['product_arr']['is_digital'] == '1')
									{
										$max_qty = 999999;
									}
									?>
									<div class="col-xs-6 col-md-3">
										<div class="form-group">
							    			<label><?php __('front_quantity'); ?></label>
							    			<div class="input-group">
							    				<span class="input-group-btn scSelectorSpin" data-direction="down">
							    					<button class="btn btn-default" type="button">
							    						<span class="glyphicon glyphicon-minus"></span>
							    					</button>
							    				</span>
							    				<input type="text" name="qty" class="form-control scProductSpinValue scSelectorSpinValue text-center" value="1" data-step="1" data-min="1" data-max="<?php echo $max_qty; ?>" maxlength="<?php echo strlen($max_qty); ?>" readonly="readonly"/>
							    				<span class="input-group-btn scSelectorSpin" data-direction="up" >
							    					<button class="btn btn-default" type="button">
							    						<span class="glyphicon glyphicon-plus"></span>
							    					</button>
							    				</span>
							    			</div>
						    			</div>
						    		</div>
						    		<div class="col-xs-6 col-md-3">
						    			<div class="form-group">
						    				<label>&nbsp;</label>
						    				<div>
						    					<?php
						    					if ((int) $tpl['product_arr']['status'] == 1 && !empty($tpl['product_arr']['stockId']))
						    					{
						    						?><button class="btn btn-primary scButton scButtonDark scButtonDarkCart scSelectorButton scSelectorAdd2Cart pjScBtnPrimary"><?php __('front_buy_now', false, true); ?></button><?php
						    					}else{
						    						?><button class="btn btn-primary" disabled="disabled"><?php __('front_out_of_stock', false, true); ?></button><?php
						    					} 
						    					?>
							    			</div>
						    			</div>
						    		</div>
						    		<div class="col-sm-12 text-warning scMaximumItems" data-text="<?php __('front_maximum_items'); ?>"></div>
						    	</div>
						    	<div class="row">
									<?php
									if (isset($tpl['extra_arr']) && !empty($tpl['extra_arr']))
									{ 
										foreach ($tpl['extra_arr'] as $extra)
										{
											switch ($extra['type'])
											{
												case 'single':
													?>
													<div class="col-xs-12<?php echo (int) $extra['is_mandatory'] === 0 ? ' checkbox' : ' radio'?>">
														<label>
															<?php
															if ((int) $extra['is_mandatory'] === 0)
															{
																?><input type="checkbox" name="extra[]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']); ?>)<?php
															}else{
																?><input type="radio" checked="checked" name="extra[]" value="<?php echo $extra['id']; ?>" data-price="<?php echo $extra['price']; ?>" class="scSelectorExtra" /> <?php echo pjSanitize::html($extra['name']); ?> (<?php echo pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']); ?>)<?php
															} 
															?>
														</label>
													</div>
													<?php
													break;
												case 'multi':
													?>
													<div class="col-md-4">
														<?php echo pjSanitize::html($extra['title']); ?>
														<select name="extra[]" class="form-control scSelectorExtra<?php echo (int) $extra['is_mandatory'] !== 0 ? ' required' : NULL;?>"  data-msg-required="<?php __('front_field_required');?>">
															<option value="" data-price="0"><?php __('front_select_extra'); ?></option>
															<?php
															foreach ($extra['extra_items'] as $ei)
															{
																?>
																<option value="<?php echo $extra['id']; ?>.<?php echo $ei['id']; ?>" data-price="<?php echo $ei['price']; ?>"><?php echo pjSanitize::html($ei['name']); ?> (<?php echo pjUtil::formatCurrencySign(number_format($ei['price'], 2), $tpl['option_arr']['o_currency']); ?>)</option>
																<?php
															}
															?>
														</select>
														<br/>
													</div>
													<?php
													break;
											}
										} 
											
									} 
								}
							}else{
								if ((int) $tpl['option_arr']['o_disable_orders'] === 0)
								{
									?><div class="col-sm-12"><span class="scProductOutOfStock"><?php __('front_out_of_stock'); ?></span></div><?php
								}
							}
							?>
						</div>
					</form>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-sm-5">
							<?php
							if ($inStock)
							{
								?>
								<a href="#" class="btn scButtonAdd2Favs scSelectorAdd2Favs">
					  				<span class="glyphicon glyphicon-heart"></span>
					  				<?php __('front_add_to_favs'); ?>
					  			</a>
					  			<?php
							} 
					  		?>
				  			<a href="#" class="btn scButtonSend2Friend scSelectorSend2Friend">
				  				<span class="glyphicon glyphicon-user"></span>
				  				<?php __('front_send_to_friend'); ?>
				  			</a>
						</div>
						<?php
						if (!isset($share_url))
						{
							$share_url = urlencode($href);
						}
						if (!isset($share_title))
						{
							$share_title = urlencode(stripslashes($tpl['product_arr']['name']));
						}
						?>
						<div class="col-sm-7">
							<a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&t=<?php echo $share_title; ?>" href="#" class="btn">
				  				<i class="fa fa-facebook-square" aria-hidden="true"></i>
				  				Facebook
				  			</a>
				  			<a target="_blank" href="https://twitter.com/intent/tweet?source=webclient&text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" class="btn">
				  				<i class="fa fa-twitter-square" aria-hidden="true"></i>
				  				Twitter
				  			</a>
				  			<a target="_blank" href="https://plusone.google.com/_/+1/confirm?hl=en&url=<?php echo $share_url; ?>&title=<?php echo $share_title;?>" class="btn">
				  				<i class="fa fa-google-plus-square" aria-hidden="true"></i>
				  				Google+
				  			</a>
				  			<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>&source=<?php echo $linkedin_source; ?>" class="btn">
				  				<i class="fa fa-linkedin-square" aria-hidden="true"></i>
				  				Linkedin
				  			</a>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default scSelectorSend2FriendBox" style="display:none;">
				<div class="panel-body">
					<h5 class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_send_to_friend'); ?></strong></h5>
					<form role="form" action="" method="post" class="scSelectorSend2FriendForm">
						<input type="hidden" name="id" value="<?php echo (int) $tpl['product_arr']['id']; ?>" />
						<input type="hidden" name="url" value="<?php echo pjSanitize::html($href); ?>" />
						
						<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
						
						<div class="row">
							<div class="col-md-6">
							  	<div class="form-group">
							    	<label for="your_email"><?php __('front_s2f_your_email'); ?></label>
							    	<input type="email" class="form-control" name="your_email" id="your_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
							  	</div>
							</div>
							<div class="col-md-6">
							  	<div class="form-group">
							    	<label for="your_name"><?php __('front_s2f_your_name'); ?></label>
							    	<input type="text" class="form-control" name="your_name" id="your_name" data-err="<?php echo $validate['name'];?>">
							  	</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    <label for="friend_email"><?php __('front_s2f_friend_email'); ?></label>
								    <input type="email" class="form-control" name="friend_email" id="friend_email" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
								</div>
							</div>
							<div class="col-md-6">
							  	<div class="form-group">
							    	<label for="friend_name"><?php __('front_s2f_friend_name'); ?></label>
							    	<input type="text" class="form-control" name="friend_name" id="friend_name" data-err="<?php echo $validate['name'];?>">
							  	</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
								    <label for="captcha"><?php __('bf_captcha'); ?></label>
								    <div>
									    <img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?>" alt="Captcha" style="vertical-align: middle; display:block;float: left; margin-right: 3px;" class="scSelectorCaptcha"/>
									    <input type="text" class="form-control" name="captcha" id="captcha" maxlength="6" style="width: 100px;" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>">
								    </div>
								</div>
							</div>
							<div class="col-md-6">
							  	<div class="form-group">
							  		<button type="submit" class="btn btn-default scSelectorSend2FriendSubmit pjScBtnPrimary"><?php __('front_send', false, true); ?></button>
					 				<button type="submit" class="btn btn-default scSelectorSend2FriendCancel pjScBtnSecondary"><?php __('front_cancel', false, true); ?></button>
							  	</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<p class="text-uppercase pjScProductDescriptionTitle"><strong><?php __('front_description'); ?></strong></p>
			<p><?php echo stripslashes($tpl['product_arr']['full_desc']); ?></p>
		</div>
	</div>
	<br/><br/>
</div>
<?php 
if (isset($tpl['similar_arr']) && !empty($tpl['similar_arr']))
{
	?>
	<div class="container">
		<h4 class="text text-uppercase"><strong><?php __('front_similar'); ?></strong></h4>
		<br/>
		<ul class="list-unstyled row text-center">
			<?php
			foreach ($tpl['similar_arr'] as $similar)
			{
				$slug = NULL;
				if ((int) $tpl['option_arr']['o_seo_url'] === 1)
				{
					# Seo friendly URLs ---------
					$category_id = NULL;
					if (!empty($similar['category_ids']))
					{
						$category_id = max($similar['category_ids']);
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
						
					$slug = sprintf("%s-%u.html", pjAppController::friendlyURL($similar['name']), $similar['id']);
					if (!empty($category_slug))
					{
						$slug = join("/", $category_slug) . '/' . $slug;
					}
					$href = pjUtil::getReferer() . '#!/' . $slug;
				} else {
					# Non-Seo friendly URLs ---------------------
					$href = pjUtil::getReferer() . '#!/Product/' . $similar['id'];
				}
				?>
				<li class="col-md-2">
					<p>
						<a href="<?php echo $href; ?>" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>" class="scSelectorProduct"><img class="img-responsive" src="<?php echo is_file(PJ_INSTALL_PATH . $similar['pic']) ? PJ_INSTALL_URL . $similar['pic'] : PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/noimg.png'; ?>" alt="<?php echo pjSanitize::html($similar['name']); ?>" /></a>
			     	</p>
			     	<p><a href="<?php echo $href; ?>" class="scSelectorProduct" data-id="<?php echo $similar['id']; ?>" data-slug="<?php echo pjSanitize::html($slug); ?>"><strong><?php echo pjSanitize::html($similar['name']); ?></strong></a></p>
			     	<p><strong><?php echo pjUtil::formatCurrencySign(number_format($similar['price'], 2), $tpl['option_arr']['o_currency']); ?></strong></p>
				</li>
				<?php
			} 
			?>
		</ul>
	</div>
	<?php
} 
?>
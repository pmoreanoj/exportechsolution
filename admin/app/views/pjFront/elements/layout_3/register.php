<div class="container-fluid pjScLogReg">
	<div class="panel panel-default">
		<div class="panel-heading"><strong class="text-primary text-uppercase pjScLogRegTitle"><?php __('front_register'); ?></strong></div>
		
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-4">
					<p><?php __('front_register_note'); ?></p>
				</div>
				<div class="col-sm-8">
					<form action="" method="post" class="scForm scSelectorRegisterForm">
						<input type="hidden" name="sc_register" value="1" />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group required">
							    	<label class="control-label"><?php __('client_email'); ?></label>
									<div class="input-group">
									  	<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
									  	<input type="email" name="email" class="form-control" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo isset($_POST['email']) ? pjSanitize::html($_POST['email']) : NULL; ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>">
									</div>
						  		</div>
						  	</div>
						  	<div class="col-sm-6">
						  		<div class="form-group required">
							    	<label class="control-label"><?php __('client_password'); ?></label>
									<div class="input-group">
									  	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
									  	<input type="password" name="password" class="form-control" placeholder="<?php __('front_placeholder_password', false, true); ?>" data-err="<?php echo $validate['password'];?>" >
									  	<span class="input-group-addon pjScEyeIcon" title="<?php __('front_show_password');?>" data-show="<?php __('front_show_password');?>" data-hide="<?php __('front_hide_password');?>"><span class="glyphicon glyphicon-eye-open"></span></span>
									</div>
						  		</div>
							</div>
						</div>
						<div class="row">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
									<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : null;?>">
							   			<label class="control-label"><?php __('client_name'); ?></label>
							    		<input name="client_name" class="form-control required" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo isset($_POST['client_name']) ? pjSanitize::html($_POST['client_name']) : NULL; ?>" data-err="<?php echo $validate['name'];?>" >
							  		</div>
								</div>
								<?php
							}
							if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
									<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : null;?>">
							    		<label class="control-label"><?php __('client_phone'); ?></label>
										<div class="input-group">
									  		<span class="input-group-addon"><span class="glyphicon glyphicon-earphone"></span></span>
									  		<input name="phone" class="form-control" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo isset($_POST['phone']) ? pjSanitize::html($_POST['phone']) : NULL; ?>" data-err="<?php echo $validate['phone'];?>" >
										</div>
							  		</div>
								</div>
								<?php
							}
							?>
						</div>
						<div class="row">
							<?php
							if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3)))
							{
								?>
								<div class="col-sm-6">
									<div class="form-group<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : null;?>">
							   			<label class="control-label"><?php __('client_url'); ?></label>
										<input name="url" class="form-control" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo isset($_POST['url']) ? pjSanitize::html($_POST['url']) : NULL; ?>" data-err="<?php echo $validate['url'];?>" >
							  		</div>
								</div>
								<?php
							} 
							?>
							<div class="col-sm-6">
								<div class="form-group required">
							    	<label class="control-label"><?php __('bf_captcha'); ?></label>
									<div class="row">
										
									  	<div class="col-xs-6">
										    <input type="text" name="captcha" class="form-control" maxlength="6" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>">
									  	</div>
									  	<div class="col-xs-6">
									    	<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1,99999); ?>" alt="Captcha" class="scCaptcha scSelectorCaptcha" />
									  	</div>
									  	
									</div>
							  	</div>
							</div>
						</div>
						
						<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
						
						<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary"><?php __('front_register', false, true); ?></button>
					  	<a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="btn btn-link scSelectorLogin pjScBtnLink"><?php __('front_login'); ?></a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
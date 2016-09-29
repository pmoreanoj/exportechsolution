<?php
include PJ_VIEWS_PATH . 'pjFront/elements/header.php';
$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
if($_GET['layout'] != 3)
{
	?>
	<h1 class="scHeading"><?php __('front_register'); ?></h1>
	<form action="" method="post" class="scForm scSelectorRegisterForm">
		<input type="hidden" name="sc_register" value="1" />
		
		<div class="scPaper">
			<div class="scPaperSidebar">
				<div class="scPaperSidebarText"><?php __('front_register_note'); ?></div>
			</div>
			<div class="scPaperSheet">
				<div class="scPaperHeading"><?php __('front_register'); ?></div>
				<div class="scPaperContent">
					<p class="scPaperChain">
						<label class="scTitle"><?php __('client_email'); ?> <span class="scRequired">*</span>:</label>
						<input type="text" name="email" class="scText" placeholder="<?php __('front_placeholder_email', false, true); ?>" value="<?php echo isset($_POST['email']) ? pjSanitize::html($_POST['email']) : NULL; ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>"/>
					</p>
					<p class="scPaperChain">
						<label class="scTitle"><?php __('client_password'); ?> <span class="scRequired">*</span>:</label>
						<input type="password" name="password" class="scText" placeholder="<?php __('front_placeholder_password', false, true); ?>" data-err="<?php echo $validate['password'];?>" />
					</p>
					<?php if (in_array((int) $tpl['option_arr']['o_bf_c_name'], array(2,3))) : ?>
					<p class="scPaperChain">
						<label class="scTitle"><?php __('client_name'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_name'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?>:</label>
						<input type="text" name="client_name" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_name'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_name', false, true); ?>" value="<?php echo isset($_POST['client_name']) ? pjSanitize::html($_POST['client_name']) : NULL; ?>" data-err="<?php echo $validate['name'];?>"/>
					</p>
					<?php endif; ?>
					<?php if (in_array((int) $tpl['option_arr']['o_bf_c_phone'], array(2,3))) : ?>
					<p class="scPaperChain">
						<label class="scTitle"><?php __('client_phone'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_phone'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?>:</label>
						<input type="text" name="phone" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_phone'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_phone', false, true); ?>" value="<?php echo isset($_POST['phone']) ? pjSanitize::html($_POST['phone']) : NULL; ?>" data-err="<?php echo $validate['phone'];?>" />
					</p>
					<?php endif; ?>
					<?php if (in_array((int) $tpl['option_arr']['o_bf_c_url'], array(2,3))) : ?>
					<p class="scPaperChain">
						<label class="scTitle"><?php __('client_url'); ?><?php if ((int) $tpl['option_arr']['o_bf_c_url'] === 3) : ?> <span class="scRequired">*</span><?php endif; ?>:</label>
						<input type="text" name="url" class="scText<?php echo (int) $tpl['option_arr']['o_bf_c_url'] === 3 ? ' required' : NULL; ?>" placeholder="<?php __('front_placeholder_url', false, true); ?>" value="<?php echo isset($_POST['url']) ? pjSanitize::html($_POST['url']) : NULL; ?>" data-err="<?php echo $validate['url'];?>" />
					</p>
					<?php endif; ?>
					<p class="scPaperChain">
						<label class="scTitle"><?php __('bf_captcha'); ?></label>
						<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1,99999); ?>" alt="Captcha" class="scCaptcha scSelectorCaptcha" />
						<input type="text" name="captcha" class="scText scW100" maxlength="6" data-err="<?php echo $validate['captcha'];?>" data-captcha="<?php echo $validate['captcha_wrong'];?>"/>
					</p>
					<div class="scClearLeft scNotice scSelectorNoticeMsg" style="display: none"></div>
				</div>
			</div>
			<div class="scPaperControl">
				<div class="scPaperControlInner">
					<input type="submit" value="<?php __('front_register', false, true); ?>" class="scButton scButtonDark scButtonDarkNext scSelectorButton" />
					<a href="<?php echo pjUtil::getReferer(); ?>#!/Login" class="scLink scSelectorLogin"><?php __('front_login'); ?></a>
				</div>
			</div>
		</div>
	</form>
	<?php
}else{
	include PJ_VIEWS_PATH . 'pjFront/elements/layout_3/register.php';
} 
?>
<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	
	pjUtil::printNotice(__('infoAddUserTitle', true), __('infoAddUserDesc', true));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionCreate" method="post" id="frmCreateUser" class="form pj-form" autocomplete="off">
		<input type="hidden" name="user_create" value="1" />
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="text" name="password" id="password" class="pj-form-field required w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('client_phone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="phone" id="phone" class="pj-form-field w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == 'T' ? ' selected="selected"' : null;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
        
		<?php 
			if($tpl['role'] == 1){
		?>
		<!--Begin Edit-->
                 <p>
			<label class="title">Role</label>
			<span class="inline_block">
				<select name="role" id="role" class="pj-form-field required">
					<option value="" selected="selected">-- Choose--</option>
					<option value="1">Admin</option>
					<option value="2">Manager</option>
                                        <option value="3" >Editor</option>				
                                </select>
			</span>
		</p>
                 <!-- End Edit -->
		<?php 
			}
			else{
				?>
				<p>
			<label class="title">Role</label>
			<span class="inline_block">
				<select name="role" id="role" class="pj-form-field required">
					<option value="" selected="selected">-- Choose--</option>
					<option value="2">Manager</option>
                                        <option value="3" >Editor</option>				
                                </select>
			</span>
		</p>
			<?php
			}
		?>


		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminUsers&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('vr_email_taken'); ?>";
	</script>
	<?php
}
?>
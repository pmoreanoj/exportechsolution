<br class="clear_both" />
<input type="hidden" id="orderAttributes" name="orderAttributes" value="" class="w300"/>
<div id="boxAttributes" class="b10 t10">
	<?php include dirname(__FILE__) . '/attributes_only.php'; ?>
</div>

<div class="h30">
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnAddAttribute"><?php __('product_attr_add'); ?></a>
	<?php __('product_attr_or'); ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnCopyAttribute"><?php __('product_attr_copy'); ?></a>
</div>
<input type="submit" class="pj-button" value="<?php __('btnSave', false, true); ?>" />
<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex';" />
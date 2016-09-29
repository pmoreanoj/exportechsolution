<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 1:
			pjUtil::printNotice($status[1]);
			break;
		case 2:
			pjUtil::printNotice($status[2]);
			break;
		case 9:
			pjUtil::printNotice($status[9]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$errors = __('errors', true);
		$titles = __('titles', true);
		$bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$errors[$_GET['err']]);
		pjUtil::printNotice(@$titles[$_GET['err']], $bodies_text);
	}
	
	?>
	<style type="text/css">
	.pj-status{
		width: 83px !important;
	}
	.pj-status-1{
		background-position: 70px 3px !important;
	}
	.s-Img{
		background-color: #fff;
		border: solid 1px #ccc;
		max-height: 75px;
		max-width: 75px;
		padding: 1px;
		vertical-align: middle;
	}
	</style>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts"><?php __('lblProductsList'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionStock"><?php __('product_stock_tab'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoProductsTitle', true), __('infoProductsDesc', true));
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminProducts" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnPlusAddProduct'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$product_statuses = __('product_statuses', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all<?php echo !isset($_GET['is_active_out']) && !isset($_GET['is_out']) ? ' pj-button-active' : null;?>"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="1"><?php echo $product_statuses[1]; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="2"><?php echo $product_statuses[2]; ?></a>
			<a href="#" class="pj-button btn-filter btn-status<?php echo isset($_GET['is_out']) || isset($_GET['is_active_out']) ? ' pj-button-active' : NULL;?>" data-column="status" data-value="3"><?php echo __('lblOutOfStock'); ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="float_left w400">
				<p>
					<label class="title"><?php __('product_name'); ?></label>
					<input type="text" name="name" class="pj-form-field w150" value="<?php echo isset($_GET['name']) ? pjSanitize::html($_GET['name']) : NULL; ?>" />
				</p>
				<p>
					<label class="title"><?php __('product_sku'); ?></label>
					<input type="text" name="sku" class="pj-form-field w150" value="<?php echo isset($_GET['sku']) ? pjSanitize::html($_GET['sku']) : NULL; ?>" />
				</p>
				<p>
					<label class="title"><?php __('product_category'); ?></label>
					<select name="category_id" class="pj-form-field w150">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach ($tpl['category_arr'] as $category)
					{
						?><option value="<?php echo $category['data']['id']; ?>"<?php echo isset($_GET['category_id']) && $_GET['category_id'] == $category['data']['id'] ? ' selected="selected"' : NULL; ?>><?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?></option><?php
					}
					?>
					</select>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="float_right w300">
				<p>
					<label class="title" style="width: 110px"><?php __('product_status'); ?></label>
					<select name="status" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($product_statuses as $k => $v)
						{
							?><option value="<?php echo $k; ?>"<?php echo isset($_GET['status']) && $_GET['status'] == $k ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v); ?></option><?php
						}
						?>
					</select>
				</p>
				<p>
					<label class="title" style="width: 110px"><?php __('product_is_digital'); ?></label>
					<span class="left"><input type="checkbox" name="is_digital" value="1"<?php echo isset($_GET['is_digital']) ? ' checked="checked"' : NULL; ?> /></span>
				</p>
				<p>
					<label class="title" style="width: 110px"><?php __('product_is_featured'); ?></label>
					<span class="left"><input type="checkbox" name="is_featured" value="1"<?php echo isset($_GET['is_featured']) ? ' checked="checked"' : NULL; ?> /></span>
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>

	<div id="grid"></div>
	
	<div id="dialogDeleteProduct" style="display: none" title="<?php __('delete_confirmation'); ?>"></div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['is_out']))
	{
		?>pjGrid.queryString += "&is_out=yes";<?php
	}
	if (isset($_GET['is_active_out']))
	{
	?>pjGrid.queryString += "&is_active_out=yes";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.sku = "<?php __('product_sku'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.sc_delete_product = "<?php __('delete_product_confirmation'); ?>";
	myLabel.sc_delete_confirmation = "<?php __('sc_delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.image = "<?php __('product_image'); ?>";
	myLabel.stock = "<?php __('product_stock'); ?>";
	myLabel.price = "<?php __('product_stock_price'); ?>";
	</script>
	<?php
}
?>
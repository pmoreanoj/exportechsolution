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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts"><?php __('lblProductsList'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionStock"><?php __('product_stock_tab'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><?php __('menuCategories'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(@$titles['AC12'], @$bodies['AC12']);
	?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<?php //pjUtil::printNotice($SC_LANG['info']['add_category']); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate" method="post" id="frmCreateCategory" class="pj-form form">
		<input type="hidden" name="category_create" value="1" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('category_name'); ?>:</label>
				<span class="inline_block">
					<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p><label class="title"><?php __('category_parent'); ?>:</label>
			<select name="parent_id" id="parent_id" class="pj-form-field">
				<option value="1"><?php __('category_no_parent'); ?></option>
				<?php
				foreach ($tpl['node_arr'] as $node)
				{
					?><option value="<?php echo $node['data']['id']; ?>"><?php echo str_repeat('------', $node['deep']) . " " . $node['data']['name']; ?></option><?php
				}
				?>
			</select>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCategories&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>
<?php
if (isset($tpl['attr_arr']) && count($tpl['attr_arr']) > 0)
{
	foreach ($tpl['attr_arr'] as $attr)
	{
		$x = $attr['id'];
		if (isset($init))
		{
			mt_srand();
			$x = 'x_' . mt_rand(0, 999999);
		}
		?>
		<div id="attrBox_<?php echo $attr['id']; ?>" class="attrBox">
			<input type="hidden" name="attr[<?php echo $attr['id']; ?>]" value="1" />
			<div class="attrBoxRow" style="position: relative;">
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('product_attr_group_name'); ?></label>
						<span class="inline_block">
							<input type="text" name="i18n[<?php echo $v['id']; ?>][attr_group][<?php echo $attr['id']; ?>]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html($attr['i18n'][$v['id']]['name']); ?>" />
							<a href="#" class="pj-icon-delete align_top btnAttrGroupDelete" data-id="<?php echo $attr['id']; ?>"></a>
						</span>
					</p>
					<?php
				}
				?>
				<a href="javascript:void(0);" class="group-move-icon"></a>
			</div>
			<input type="hidden" id="orderItems_<?php echo $attr['id']; ?>" name="orderItems_<?php echo $attr['id']; ?>" value="" class="w300"/>
			<div class="attrBoxRow">
				<label class="title"><?php __('product_attr_name'); ?></label>
				<div id="attrBoxRowStick_<?php echo $attr['id']; ?>" class="attrBoxRowStick" data-id="<?php echo $attr['id']; ?>">
				<?php
				if (isset($attr['child']) && !empty($attr['child']))
				{
					foreach ($attr['child'] as $k => $child)
					{
						$y = $child['id'];
						if (isset($init))
						{
							mt_srand();
							$y = 'y_' . mt_rand(0, 999999);
						}
						?>
						<div id="attrBoxRowItems_<?php echo $child['id']; ?>" class="attrBoxRowItems">
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<span class="inline_block">
										<input type="text" name="i18n[<?php echo $v['id']; ?>][attr_item][<?php echo $attr['id']; ?>][<?php echo $child['id']; ?>]" class="pj-form-field r5 w80<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html($child['i18n'][$v['id']]['name']); ?>" />
										<?php
										if($k > 0)
										{ 
											?><a href="#" class="pj-icon-delete align_top btnAttrDelete" data-id="<?php echo $child['id']; ?>"></a><?php
										} 
										?>
									</span>
								</p>
								<?php
							}
							?>
							<a href="javascript:void(0);" class="item-move-icon"></a>
						</div>
						<?php
					}
				}
				?>
				</div>
			</div>
			<div>
				<label class="title">&nbsp;</label>
				<a href="#" class="pj-button btnAddAttr" rel="<?php echo $attr['id']; ?>"><?php __('product_attr_create'); ?></a>
			</div>
		</div>
		<?php
	}
}else{
	echo __('lblNoAttributesFound', true) . '<br/><br/>';
}
?>
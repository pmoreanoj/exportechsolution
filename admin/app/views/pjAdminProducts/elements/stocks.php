<div class="stockContainer b10">
	<?php
	$table_width = 420;
	if (isset($tpl['attr_arr']))
	{
		foreach ($tpl['attr_arr'] as $attr)
		{
			if(isset($attr['child']) && count(($attr['child'])) > 0)
			{
				$table_width += 150;
			}
		}
	}
	if($table_width < 742)
	{
		$table_width = 742;
	} 
	?>
	<table class="pj-table tblStocks" cellpadding="0" cellspacing="0" style="width: <?php echo $table_width;?>px;">
		<thead>
			<tr>
				<th class="sub w130"><?php __('product_stock_image'); ?></th>
				<?php
				if (isset($tpl['attr_arr']))
				{
					foreach ($tpl['attr_arr'] as $attr)
					{
						if(isset($attr['child']) && count(($attr['child'])) > 0)
						{
							?><th class="sub w150"><?php echo pjSanitize::html($attr['name']); ?></th><?php
						}
					}
				}
				?>
				<th class="sub w80"><?php __('product_stock_qty'); ?></th>
				<th class="sub w150"><?php __('product_stock_price'); ?></th>
				<?php
				if(count($tpl['attr_arr']) > 0)
				{ 
					?>
					<th class="sub w40">&nbsp;</th>
					<?php
				} 
				?>
			</tr>
		</thead>
		<tbody>
		<?php
		if (isset($tpl['stock_arr']) && count($tpl['stock_arr']) > 0)
		{
			foreach ($tpl['stock_arr'] as $stock)
			{
				?>
				<tr>
					<td>
						<?php
						if (!empty($stock['small_path']))
						{
							?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btnImageStock" rel="<?php echo $stock['image_id']; ?>"><img src="<?php echo PJ_INSTALL_URL . $stock['small_path']; ?>" alt="" class="in-stock" /></a><?php
						} else {
							?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnImageStock"><?php __('product_stock_choose_image'); ?></a><?php
						}
						?>
						<span class="boxStockImageId"><span><input type="hidden" name="stock_image_id[<?php echo $stock['id'] ?>]" value="<?php echo $stock['image_id']; ?>" class="required" /></span></span>
					</td>
					<?php
					foreach ($tpl['attr_arr'] as $attr)
					{
						if(isset($attr['child']) && count(($attr['child'])) > 0)
						{
							?>
							<td>
								<span>
								<select name="stock_attribute[<?php echo $stock['id'] ?>][<?php echo $attr['id']; ?>]" class="pj-form-field required">
									<option value="">---</option>
									<?php
									foreach ($attr['child'] as $child)
									{
										?><option value="<?php echo $child['id']; ?>"<?php echo isset($stock['attrs'][$attr['id']]) && $stock['attrs'][$attr['id']] == $child['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($child['name']); ?></option><?php
									}
									?>
								</select>
								</span>
							</td>
							<?php
						}
					}
					?>
					<td><span><input type="text" name="stock_qty[<?php echo $stock['id'] ?>]" class="pj-form-field w40 align_right<?php echo (int) $tpl['arr']['is_digital'] === 1 ? null : ' required';?> digits pjScQuantity" value="<?php echo $stock['qty']; ?>" /></span></td>
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="stock_price[<?php echo $stock['id']; ?>]" class="pj-form-field w60 align_right required number" value="<?php echo $stock['price']; ?>" />
						</span>
					</td>
					<?php
					if(count($tpl['attr_arr']) > 0)
					{ 
						?>
						<td>
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>" rel="<?php echo $stock['id']; ?>" class="pj-table-icon-delete btnDeleteStock"></a>
						</td>
						<?php
					} 
					?>
				</tr>
				<?php
			}
		} else {
			mt_srand();
			$index = 'x_' . mt_rand(0, 999999);
			?>
			<tr>
				<td>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnImageStock"><?php __('product_stock_choose_image'); ?></a>
					<span class="boxStockImageId"><span><input type="hidden" name="stock_image_id[<?php echo $index; ?>]" value="" class="required"/></span></span>
				</td>
				<?php
				if (isset($tpl['attr_arr']))
				{
					foreach ($tpl['attr_arr'] as $attr)
					{
						?>
						<td>
							<span>
							<select name="stock_attribute[<?php echo $index; ?>][<?php echo $attr['id']; ?>]" class="pj-form-field required">
								<option value="">---</option>
								<?php
								foreach ($attr['child'] as $child)
								{
									?><option value="<?php echo $child['id']; ?>"><?php echo pjSanitize::html($child['name']); ?></option><?php
								}
								?>
							</select>
							</span>
						</td>
						<?php
					}
				}
				?>
				<td><span><input type="text" name="stock_qty[<?php echo $index; ?>]" class="pj-form-field w40 align_right<?php echo (int) $tpl['arr']['is_digital'] === 1 ? null : ' required';?> digits pjScQuantity" /></span></td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="stock_price[<?php echo $index; ?>]" class="pj-form-field w60 align_right required number" />
					</span>
				</td>
				<?php
				if(count($tpl['attr_arr']) > 0)
				{ 
					?>
					<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-table-icon-delete btnRemoveStock"></a></td>
					<?php
				} 
				?>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>
<?php
if(count($tpl['attr_arr']) > 0)
{ 
	?>
	<div class="h30">
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-button btnStockAdd"><?php __('product_stock_add'); ?></a>
	</div>
	<?php
} 
if(count($tpl['gallery_arr']) == 1)
{
	?><input type="hidden" id="scHiddenImageId" name="scHiddenImageId" value="<?php echo $tpl['gallery_arr'][0]['id'];?>" data-src="<?php echo PJ_INSTALL_URL . (!empty($tpl['gallery_arr'][0]['small_path']) ? $tpl['gallery_arr'][0]['small_path'] : PJ_IMG_PATH . 'no_image.png'); ?>?<?php echo rand(1, 9999999); ?>"/><?php
}
?>
<div>
	<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
	<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex';" />
</div>
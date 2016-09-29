<div id="boxClone" style="display: none">
	<div class="extraBox">
	
		<table class="tblExtras">
			<tbody>
				<tr>
					<td style="width: 85px;"><?php __('product_extra_type'); ?></td>
					<td style="width: 90px"><?php __('product_extra_mandatory'); ?></td>
					<td style="width: 350px">
						<span class="boxSingle"><?php __('product_extra_name'); ?></span>
						<span class="boxMulti" style="display: none"><?php __('product_extra_title'); ?></span>
					</td>
					<td class=""><span class="boxSingle"><?php __('product_extra_price'); ?></span></td>
				</tr>
				<tr>
					<td class="align_top">
						<select name="extra_type[{INDEX}]" class="pj-form-field">
							<?php
							$product_extra_types = __('product_extra_types', true);
							krsort($product_extra_types);
							foreach ($product_extra_types as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</td>
					<td class="align_top"><label><input type="checkbox" name="extra_is_mandatory[{INDEX}]" value="1" /></label></td>
					<td class="align_top tdExtrasClean" colspan="2">
						<div class="boxSingle">
							<table style="width: 100%">
								<tbody>
									<tr>
										<td class="align_top">
										<?php
										foreach ($tpl['lp_arr'] as $v)
										{
											?>
											<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
												<span class="inline_block">
													<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
													<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
													<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
													<?php endif; ?>
												</span>
											</p>
											<?php
										}
										?>
										</td>
										<td class="align_top">
											<span class="pj-form-field-custom pj-form-field-custom-before">
												<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
												<input type="text" name="extra_price[{INDEX}]" class="pj-form-field w80 align_right required number" />
											</span>
										</td>
										<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtraTmp" title="<?php __('product_extra_delete'); ?>"></a></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="boxMulti" style="display: none">
							<table style="width: 100%">
								<tbody>
									<tr>
										<td style="width: 342px">
										<?php
										foreach ($tpl['lp_arr'] as $v)
										{
											?>
											<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
												<span class="inline_block">
													<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_title][{INDEX}]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" disabled="disabled" />
													<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
													<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
													<?php endif; ?>
												</span>
											</p>
											<?php
										}
										?>
										</td>
										<td></td>
										<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnDeleteExtraTmp" title="<?php __('product_extra_delete'); ?>"></a></td>
									</tr>
									<tr>
										<td><?php __('product_extra_name'); ?></td>
										<td><?php __('product_extra_price'); ?></td>
										<td></td>
									</tr>
									<tr>
										<td class="align_top">
										<?php
										foreach ($tpl['lp_arr'] as $v)
										{
											?>
											<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
												<span class="inline_block">
													<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}][{X}]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" disabled="disabled" />
													<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
													<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
													<?php endif; ?>
												</span>
											</p>
											<?php
										}
										?>
										</td>
										<td class="align_top">
											<span class="pj-form-field-custom pj-form-field-custom-before">
												<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
												<input type="text" name="extra_price[{INDEX}][{X}]" class="pj-form-field w80 align_right required number" disabled="disabled" />
											</span>
										</td>
										<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnRemoveExtraItem"></a></td>
									</tr>
								</tbody>
							</table>
							<input type="button" class="pj-button btnAddExtraItem l5" data-index="{INDEX}" value="<?php __('product_extra_item_add'); ?>" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	
	</div>
</div>

<table id="boxCloneTbl" style="display: none">
	<tbody>
		<tr>
			<td class="align_top">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][extra_name][{INDEX}][{X}]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			?>
			<?php /*<input type="text" name="extra_name[{INDEX}][{X}]" class="pj-form-field w250" />*/ ?>
			</td>
			<td class="align_top">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="extra_price[{INDEX}][{X}]" class="pj-form-field w80 align_right required number" />
				</span>
			</td>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="pj-icon-delete btnRemoveExtraItem"></a></td>
		</tr>
	</tbody>
</table>

<div id="dialogDeleteExtra" style="display: none" title="<?php __('product_extra_delete_title'); ?>">
<?php __('product_extra_delete_desc'); ?>
</div>
<div id="dialogCopyExtra" style="display: none" title="<?php __('product_extra_copy_title'); ?>"></div>
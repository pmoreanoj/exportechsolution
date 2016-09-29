<?php
$info = __('info', true);
pjUtil::printNotice($info['product_history_title'], $info['product_history_body']);
if (count($tpl['history_arr']) > 0)
{
	?>
	<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('product_history_created'); ?></th>
				<th><?php __('product_history_product'); ?></th>
				<?php
				if($tpl['product']['is_digital'] == 0)
				{ 
					?>
					<th><?php __('product_history_attribute'); ?></th>
					<?php
				}
				?>
				<th><?php __('product_history_qty_before'); ?></th>
				<th><?php __('product_history_qty_after'); ?></th>
				<th><?php __('product_history_price_before'); ?></th>
				<th><?php __('product_history_price_after'); ?></th>
				
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($tpl['history_arr'] as $k => $history)
		{
			$before = unserialize(base64_decode($history['before']));
			$after = unserialize(base64_decode($history['after']));
			?>
			<tr class="<?php echo $k % 2 === 0 ? 'pj-table-row-odd' : 'pj-table-row-even'; ?>">
				<td><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($history['created'])); ?></td>
				<td><?php echo pjSanitize::html($history['name']); ?></td>
				<?php
				if($tpl['product']['is_digital'] == 0)
				{ 
					?>
					<td><?php echo stripslashes($history['attributes']); ?></td>
					<?php
				}
				?>
				
				<td><?php echo $before['qty']; ?></td>
				<td><?php echo $after['qty']; ?></td>
				<td><?php echo pjUtil::formatCurrencySign($before['price'], $tpl['option_arr']['o_currency']); ?></td>
				<td><?php echo pjUtil::formatCurrencySign($after['price'], $tpl['option_arr']['o_currency']); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	pjUtil::printNotice(__('product_history_empty', true), '');
}
?>
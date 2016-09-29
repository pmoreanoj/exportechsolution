<!doctype html>
<html>
	<head>
		<title>Shopping Cart by PHPJabbers.com</title>
		<meta charset="utf-8">
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		?>
	</head>
	<body>
		<div class="A4">
		
			<table class="pj-table" style="width: 100%">
				<thead>
					<tr>
						<th style="width: 80px"><?php __('product_stock_image'); ?></th>
						<th><?php __('lblName'); ?></th>
						<th style="width: 70px"><?php __('product_stock_price'); ?></th>
						<th style="width: 70px"><?php __('product_stock_qty'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($tpl['arr'] as $item)
				{
					?><tr>
						<td><img src="<?php echo PJ_INSTALL_URL . $item['pic']; ?>" alt="" class="stock_pic" /></td>
						<td><?php
						echo pjSanitize::html($item['name']);
						if (!empty($item['stock_attr']))
						{
							printf('<br>(%s)', str_replace('~:~', ': ', join(', ', $item['stock_attr'])));
						}
						?></td>
						<td><?php echo pjUtil::formatCurrencySign(number_format($item['price'], 2), $tpl['option_arr']['o_currency']); ?></td>
						<td><?php echo $item['qty']; ?></td>
					</tr><?php
				}
				?>
				</tbody>
			</table>
		
		</div>
		
		<script type="text/javascript">
		window.setTimeout(function () {
			window.print();
		}, 500);
		</script>
	</body>
</html>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/header.php'; 
?>
<div class="container-fluid">
	<h2 class="text-uppercase text-primary"><strong><?php __('front_order_completed'); ?></strong></h2><br>
	<div class="alert alert-success" role="alert">
		<?php
		if (isset($tpl['get']['payment_method']))
		{
			$status = __('front_booking_status', true);
			$message = $status[1];
			switch ($tpl['get']['payment_method'])
			{
				case 'paypal':
					echo $status[11];
					if (pjObject::getPlugin('pjPaypal') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
					break;
				case 'authorize':
					echo $status[11];
					if (pjObject::getPlugin('pjAuthorize') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
					break;
				case 'bank':
					echo $message; ?><br/><?php echo pjSanitize::html(nl2br($tpl['option_arr']['o_bank_account']));
					break;
				case 'creditcard':
				case 'cod':
				default:
					echo $message;
			}
		}
		?>
	</div>
</div>
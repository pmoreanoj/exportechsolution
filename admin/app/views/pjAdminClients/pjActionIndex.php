<?php
session_start();

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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$u_statarr = __('u_statarr', true);
	pjUtil::printNotice(__('infoClientsTitle', true), __('infoClientsDesc', true));
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminClients" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnPlusAddClient'); ?>" />
		</form>
		<form action="" method="get" class="pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['client_ids']) && $_GET['client_ids'] != '')
	{
		?>pjGrid.queryString += "&client_ids=<?php echo $_GET['client_ids']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('client_client_name'); ?>";
	myLabel.email = "<?php __('client_email'); ?>";
	myLabel.last_order = "<?php __('client_last_order'); ?>";
	myLabel.orders = "<?php __('client_orders'); ?>";
	myLabel.status = "<?php __('client_status'); ?>";
	myLabel.active = "<?php echo $u_statarr['T']; ?>";
	myLabel.inactive = "<?php echo $u_statarr['F']; ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('gridDeleteConfirmation'); ?>";
	</script>
	<?php
}
?>
<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOrders extends pjAdmin
{
	public function pjActionDeleteOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjOrderModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$os_arr = $pjOrderStockModel->where('order_id', $_GET['id'])->findAll()->getDataPair('stock_id', 'qty');
				if (!empty($os_arr))
				{
					$pjOrderStockModel->reset()->where('order_id', $_GET['id'])->eraseAll();
					$pjStockModel = pjStockModel::factory();
					foreach ($os_arr as $stock_id => $qty)
					{
						$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty + " . (int) $qty));
					}
				}
				pjOrderExtraModel::factory()->where('order_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteOrderBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjOrderModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				$pjOrderStockModel = pjOrderStockModel::factory();
				$os_arr = $pjOrderStockModel->whereIn('order_id', $_POST['record'])->findAll()->getData();
				if (!empty($os_arr))
				{
					$pjOrderStockModel->reset()->whereIn('order_id', $_POST['record'])->eraseAll();
					$pjStockModel = pjStockModel::factory();
					foreach ($os_arr as $item)
					{
						$pjStockModel->reset()->set('id', $item['stock_id'])->modify(array('qty' => ":qty + " . (int) $item['qty']));
					}
				}
				pjOrderExtraModel::factory()->whereIn('order_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportOrder()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{			
			$export_fields = array('id',
				'uuid',
				'client_id',
				'address_id',
				'locale_id',
				'tax_id',
				'status',
				'payment_method',
				'txn_id',
				'processed_on',
				'price',
				'discount',
				'insurance',
				'shipping',
				'tax',
				'total',
				'voucher',
				'notes',
				'cc_type',
				'cc_num',
				'cc_exp_month',
				'cc_exp_year',
				'cc_code',
				'created',
				'ip',
				'same_as',
				's_name',
				's_country_id',
				's_state',
				's_city',
				's_zip',
				's_address_1',
				's_address_2',
				'b_name',
				'b_country_id',
				'b_state',
				'b_city',
				'b_zip',
				'b_address_1',
				'b_address_2'
			);
			
			$pjOrderModel = pjOrderModel::factory();
			$pjOrderStockModel = pjOrderStockModel::factory();
			$pjOrderExtraModel = pjOrderExtraModel::factory();
			$pjProductModel = pjProductModel::factory();
			$pjExtraModel = pjExtraModel::factory();
			$pjExtraItemModel = pjExtraItemModel::factory();
			
			$separator = '~:~';
			$sep = '|';
			
			$arr = $pjOrderModel->reset()->select(sprintf("%1\$s, 
					(SELECT GROUP_CONCAT(CONCAT(COALESCE(`id`, '-1'), '$sep', COALESCE(`product_id`, '-1'), '$sep', COALESCE(`qty`, '0'), '$sep', COALESCE(`price`, '0')) SEPARATOR '$separator') FROM `%2\$s` WHERE `order_id` = `t1`.`id` LIMIT 1) AS `product_ids`,
					(SELECT GROUP_CONCAT(CONCAT(COALESCE(`id`, '-1'), '$sep', COALESCE(`order_stock_id`, '-1'), '$sep', COALESCE(`extra_id`, '-1'), '$sep', COALESCE(`extra_item_id`, '-1')) SEPARATOR '$separator') FROM `%3\$s` WHERE `order_id` = `t1`.`id` LIMIT 1) AS `extra_ids` 
					"
					, join(", ", $export_fields), $pjOrderStockModel->getTable(), $pjOrderExtraModel->getTable()
					))
				->whereIn('id', $_POST['record'])
				->findAll()->getData();
			
			$product_arr = $pjProductModel->reset()
				->select("t1.*, t2.content AS name")
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')				
				->findAll()
				->getDataPair('id', null);
						
			$extra_arr = $pjExtraModel->reset()
				->select('t1.*, t2.content AS name, t3.content AS title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')				
				->orderBy('`title` ASC, `name` ASC')
				->findAll()
				->getDataPair('id', null);
			
			$extra_item_arr = $pjExtraItemModel->reset()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')			
				->orderBy('t1.price ASC')
				->findAll()
				->getDataPair('id', null);
			
			$_os_arr = pjOrderStockModel::factory()
				->select("t1.*, t2.sku, t3.content AS name,
					(SELECT GROUP_CONCAT(CONCAT_WS('_', `attribute_id`, `attribute_parent_id`))
						FROM `".pjStockAttributeModel::factory()->getTable()."`
						WHERE `stock_id` = `t1`.`stock_id`
						LIMIT 1) AS `attr`")
				->join('pjProduct', 't2.id=t1.product_id', 'left outer')
				->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->whereIn('t1.order_id', $_POST['record'])
				->findAll()
				->getData();
			$new_os_arr = array();
			$att_product_id = array();
			foreach ($_os_arr as $item)
			{
				$att_product_id[] = $item['product_id'];
				$new_os_arr[$item['order_id']][] = $item;
			}
			$attr_arr = $a_arr = array();
			if (!empty($att_product_id))
			{
				$a_arr = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.product_id', $att_product_id)
					->orderBy('t1.parent_id ASC, `name` ASC')
					->findAll()
					->getData();
			}
			foreach ($a_arr as $attr)
			{
				if ((int) $attr['parent_id'] === 0)
				{
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child']))
					{
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}
			$attr_arr = array_values($attr_arr);
			$export_attr_arr = array();
			foreach ($new_os_arr as $order_id => $os_arr)
			{
				foreach($os_arr as $item)
				{
					$ex_attr_arr = array();
					if (isset($item['attr']) && !empty($item['attr']))
					{
						$at = array();
						$a = explode(",", $item['attr']);
						foreach ($a as $v)
						{
							$t = explode("_", $v);
							$at[$t[1]] = $t[0];
						}
						foreach ($at as $attr_parent_id => $attr_id)
						{
							foreach ($attr_arr as $attr)
							{
								if ($attr['id'] == $attr_parent_id)
								{
									foreach ($attr['child'] as $child)
									{
										if ($child['id'] == $attr_id)
										{
											$ex_attr_arr[] = pjSanitize::html($attr['name']) . ': ' . pjSanitize::html($child['name']);
											break;
										}
									}
								}
							}
						}
					}
					$export_attr_arr[$order_id][$item['product_id']][$item['id']] = $ex_attr_arr;
				}
			}
						
			$data = array();
			
			foreach ($arr as $k => $v) {				
				$product_ids = $v['product_ids'];
				$extra_ids = $v['extra_ids'];
				
				$order_product_arr = array();
				$product_ids_arr = explode($separator, $product_ids);
				foreach ($product_ids_arr as $str) {
					list($order_stock_id, $product_id, $qty, $price) = explode($sep, $str);
					if(!isset($order_product_arr[$order_stock_id])) {
						$order_product_arr[$order_stock_id] = array();
					}										
					$order_product_arr[$order_stock_id]['product_id'] = $product_id;
					$order_product_arr[$order_stock_id]['qty'] = $qty;
					$order_product_arr[$order_stock_id]['price'] = $price;
				}
				
				$order_extra_arr = array();
				$extra_ids_arr = explode($separator, $extra_ids);
				foreach ($extra_ids_arr as $str) {
					if(strlen($str) == '') continue;
					list($order_extra_id, $order_stock_id, $extra_id, $extra_item_id) = explode($sep, $str);
					if(intval($order_stock_id) > 0) {
						if(!isset($order_extra_arr[$order_stock_id])) {
							$order_extra_arr[$order_stock_id] = array();
						}
						$order_extra_arr[$order_stock_id][] = array('extra_id' => $extra_id, 'extra_item_id' => $extra_item_id);
					}
				}
				$product_list_arr = array();
				foreach ($order_product_arr as $order_stock_id => $_order_product) {
					$product_id = $_order_product['product_id'];
					$qty = $_order_product['qty'];
					$price = $_order_product['price'];
					
					$extra_str = NULL;
					if(isset($order_extra_arr[$order_stock_id])) {
						$extra_list_arr = array();
						foreach ($order_extra_arr[$order_stock_id] as $osindex => $osarr) {							
							if(intval($osarr['extra_id']) > 0 && intval($osarr['extra_item_id']) > 0) {								
								$extra_list_arr[] = $extra_arr[$osarr['extra_id']]['title'] . '('.$extra_item_arr[$osarr['extra_item_id']]['name'].')';
							} else if(intval($osarr['extra_id']) > 0) {								
								$extra_list_arr[] = $extra_arr[$osarr['extra_id']]['name'];
							}
						}
						$extra_str = join("; ", $extra_list_arr);
					}
					$_attr_str = '';
					if(isset($export_attr_arr[$v['id']][$product_id][$order_stock_id]))
					{
						$_attr_str =  join(' / ', $export_attr_arr[$v['id']][$product_id][$order_stock_id]);
					}
					$product_list_arr[] = $product_arr[$product_id]['name'] . ' | ' . $_attr_str . ' x ' . $qty . (!empty($extra_str) ? ' ('.$extra_str.')' : NULL);
				}
				
				$arr[$k]['Product x Qty'] = join("; \n", $product_list_arr);
				unset($arr[$k]['product_ids']);
				unset($arr[$k]['extra_ids']); 
			} 
			
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Orders-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$this->set('client_arr', pjClientModel::factory()->find($_GET['client_id'])->getData());
			}
		}
	}
	
	public function pjActionGetAddress()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$address_arr = pjAddressModel::factory()
					->select('t1.*, t2.content AS country_name')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();
				if (!isset($_GET['json']))
				{
					$this->set('address_arr', $address_arr);
				} else {
					pjAppController::jsonResponse($address_arr);
				}
			}
		}
	}
	
	public function pjActionGetAddressBook()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$this->set('order_arr', pjOrderModel::factory()->find($_GET['order_id'])->getData());
				$this->set('address_arr', pjAddressModel::factory()->where('t1.client_id', $_GET['client_id'])->orderBy('t1.address_1 ASC')->findAll()->getData());
			}
		}
	}
	
	public function pjActionGetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOrderModel = pjOrderModel::factory()
				->join('pjClient', 't2.id=t1.client_id', 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjOrderModel->where('t1.uuid LIKE', "%$q%");
				$pjOrderModel->orWhere('t2.client_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t2.email LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_address_1 LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.s_city LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_name LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_address_1 LIKE', "%$q%");
				$pjOrderModel->orWhere('t1.b_city LIKE', "%$q%");
			}
			
			# Update order (other orders list)
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$pjOrderModel->where('t1.client_id', (int) $_GET['client_id']);
			}
			if (isset($_GET['order_id']) && (int) $_GET['order_id'] > 0)
			{
				$pjOrderModel->where('t1.id !=', (int) $_GET['order_id']);
			}
			# ---
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$pjOrderModel->where(sprintf("t1.id IN (SELECT `order_id` FROM `%s` WHERE `product_id` = '%u')", pjOrderStockModel::factory()->getTable(), (int) $_GET['product_id']));
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('new', 'pending', 'cancelled', 'completed')))
			{
				$pjOrderModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['payment_method']) && !empty($_GET['payment_method']) && in_array($_GET['payment_method'], array('paypal', 'authorize', 'creditcard', 'bank', 'cod')))
			{
				$pjOrderModel->where('t1.payment_method', $_GET['payment_method']);
			}
			if (isset($_GET['total_from']) && (float) $_GET['total_from'] > 0)
			{
				$pjOrderModel->where('t1.total >=', $_GET['total_from']);
			}
			if (isset($_GET['total_to']) && (float) $_GET['total_to'] > 0)
			{
				$pjOrderModel->where('t1.total <=', $_GET['total_to']);
			}
			if (isset($_GET['date_from']) && !empty($_GET['date_from']))
			{
				$pjOrderModel->where('DATE(t1.created) >=', pjUtil::formatDate($_GET['date_from'], $this->option_arr['o_date_format']));
			}
			if (isset($_GET['date_to']) && !empty($_GET['date_to']))
			{
				$pjOrderModel->where('DATE(t1.created) <=', pjUtil::formatDate($_GET['date_to'], $this->option_arr['o_date_format']));
			}
			
			$column = 't1.id';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjOrderModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = $pjOrderModel
				->select('t1.id, t1.uuid, t1.total, t1.status, t1.created, t1.client_id, t2.client_name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			foreach ($data as $k => $v)
			{
				$data[$k]['total_formated'] = pjUtil::formatCurrencySign(number_format($v['total'], 2), $this->option_arr['o_currency']);
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (in_array($this->getRoleId(), array(1,2,3)))  //change using this role id
		{
			$this->set('product_arr', pjProductModel::factory()
				->select('t1.id, t2.content AS `name`')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('`name` ASC')
				->findAll()->getData()
			);
			
			$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminOrders.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
			$this->set('role', $this->getRoleId());//get role id for their views and left menu
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetPrice()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$price = $discount = $tax = $shipping = $insurance = 0;
			
			$pjOrderStockModel = pjOrderStockModel::factory()->where('t1.order_id', $_POST['id'])->findAll();
			
			$os_arr = $pjOrderStockModel->getData();
			$oe_arr = pjOrderExtraModel::factory()->where('t1.order_id', $_POST['id'])->findAll()->getData();
			
			if (!empty($_POST['voucher']))
			{
				$product_ids = $pjOrderStockModel->getDataPair(null, 'product_id');
				$product_ids = array_unique($product_ids);
				
				$pre = array();
				$pre['code'] = $_POST['voucher'];
				list($pre['date'], $pre['hour'], $pre['minute']) = explode(",", date("Y-m-d,H,i"));
	
				$response = pjAppController::getDiscount($pre, $this->option_arr);
				if ($response['status'] == 'OK')
				{
					$intersect = array_intersect($response['voucher_products'], $product_ids);
					if (empty($response['voucher_products'][0]) || !empty($intersect))
					{
						$voucher = array(
							'voucher_code' => $response['voucher_code'],
							'voucher_type' => $response['voucher_type'],
							'voucher_discount' => $response['voucher_discount'],
							'voucher_products' => empty($response['voucher_products'][0]) ? 'all' : $response['voucher_products']
						);
					}
				}
			}
			
			$calc_price = pjAppController::pjActionCalcPrices($_POST['id'], array(), $os_arr, null, @$voucher, $this->option_arr, isset($_POST['tax_id']) ? $_POST['tax_id'] : null, 'back');
			
			$data['price'] = $calc_price['price'];
			$data['discount'] = $calc_price['discount'];
			$data['insurance'] = $calc_price['insurance'];
			$data['shipping'] = $calc_price['shipping'];
			$data['tax'] = $calc_price['tax'];
			$data['total'] = $calc_price['total'];
			$data['total'] = $data['total'] > 0 ? $data['total'] : 0;
			
			$data = array_map('floatval', $data);
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data));
		}
		exit;
	}
	
	public function pjActionSaveOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOrderModel = pjOrderModel::factory();
			if (!in_array($_POST['column'], @$pjOrderModel->getI18n()))
			{
				$data = array($_POST['column'] => $_POST['value']);
				if ($_POST['column'] == 'status' && $_POST['value'] == 'completed')
				{
					$before = $pjOrderModel->find($_GET['id'])->getData();
					if ($before['status'] != 'completed')
					{
						$data['processed_on'] = ':NOW()';
					}
				}
				$pjOrderModel->reset()->set('id', $_GET['id'])->modify($data);
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjOrder');
			}
		}
		exit;
	}
	
	public function pjActionSend()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['form_send']))
			{
				if (!isset($_POST['to']) || !isset($_POST['from']) || !isset($_POST['subject']) || !isset($_POST['body']) ||
					!pjValidation::pjActionEmail($_POST['to']) || !pjValidation::pjActionEmail($_POST['from']) ||
					!pjValidation::pjActionNotEmpty($_POST['subject']) ||
					!pjValidation::pjActionNotEmpty($_POST['body']))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Form does not validate'));
				}
				
				$pjEmail = new pjEmail();
				$pjEmail->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
					;
				}
			
				$message = $_POST['body'];
				$pos = strrpos($message, "bank");
				if ($pos !== false) {
				 	$rep = "<br><strong>Bank Information: <br></strong><p style='padding-left:30px'>Citibank<br /> Owner of account: Siaproci-INH Inc<br />" .
				         "Account number: 067004764<br /> Routing number: 3290291435<br /></p> <br />*Please after making the payment, send the transfer" . 
				         " information with the order id from our shop to accounting@exportechsolution.com and edministration@exportechsolution.com<br />"; 
				 	$pos = $pos + 4;
				 	$message = substr_replace($message, $rep, $pos, 0);
				}


				    $r = $pjEmail
                       ->setTo($_POST['to'])
                       ->setFrom($_POST['from'])
                       ->setSubject($_POST['subject'])
                       ->send($message);

				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email has not been sent'));
			}
			exit;
		}
	}
	
	public function pjActionStockDelete()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$arr = $pjOrderStockModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Stock not found.'));
				}
				if (1 == $pjOrderStockModel->set('id', $_POST['id'])->erase()->getAffectedRows())
				{
					pjOrderExtraModel::factory()->where('order_stock_id', $_POST['id'])->eraseAll();
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + " . (int) $arr['qty']));
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been deleted.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Stock has not been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing parameters.'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
		exit;
	}
	
	public function pjActionStockGet()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			$stack = pjAppController::pjActionGetOrderStock($_GET['order_id'], $this->getLocaleId());
			
			$this
				->set('os_arr', $stack['os_arr'])
				->set('extra_arr', $stack['extra_arr'])
				->set('attr_arr', $stack['attr_arr'])
			;
		}
	}
	
	public function pjActionStockAdd()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['stock_add']))
			{
				if (isset($_POST['qty']) && !empty($_POST['qty']))
				{
					$pjOrderStockModel = pjOrderStockModel::factory();
					$pjOrderExtraModel = pjOrderExtraModel::factory();
					$pjStockModel = pjStockModel::factory();
					
					$stock_id = $_POST['stock_id'];
					
					$order_stock_id = $pjOrderStockModel->reset()->setAttributes(array(
						'order_id' => $_POST['order_id'],
						'product_id' => $_POST['product_id'],
						'stock_id' =>  $stock_id,
						'price' => $_POST['price'],
						'qty' => $_POST['qty']
					))->insert()->getInsertId();
					
					if ($order_stock_id !== FALSE && (int) $order_stock_id > 0 &&
						isset($_POST['extra_id']) && isset($_POST['extra_id']) && !empty($_POST['extra_id']))
					{
						$pjStockModel->reset()->set('id', $stock_id)->modify(array('qty' => ":qty - " . (int) $_POST['qty']));

						$oe_data = array(
							'order_id' => $_POST['order_id'],
							'order_stock_id' => $order_stock_id
						);
						foreach ($_POST['extra_id'] as $extra_id => $value)
						{
							if (!empty($value) && strpos($value, "|") !== false)
							{
								$e_arr = array();
								$e_arr = explode("|", $value);
								switch ($e_arr[0])
								{
									case 'single':
										$oe_data['extra_item_id'] = NULL;
										break;
									case 'multi':
										$oe_data['extra_item_id'] = $e_arr[2];
										break;
								}
								$oe_data['extra_id'] = $extra_id;
								$oe_data['price'] = $e_arr[1];
								
								$pjOrderExtraModel->reset()->setAttributes($oe_data)->insert();
							}
						}
					}
					
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Stock has been added.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Stock couldn\'t be empty.'));
			}
			
			$this->set('product_arr', pjProductModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status != 2')
				->where("t1.id IN (SELECT TS.`product_id` FROM `".pjStockModel::factory()->getTable()."` AS TS WHERE TS.product_id = t1.id AND (qty > 0 OR t1.is_digital='1'))")
				->orderBy("`name` ASC")
				->findAll()->getData());
		}
	}
	
	public function pjActionStockGetByProduct()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjExtraItemModel = pjExtraItemModel::factory();
				
				$arr = pjProductModel::factory()
					->select(sprintf("t1.*, t2.content AS name, t3.content AS full_desc,
						(SELECT MIN(`price`) FROM `%2\$s`
							WHERE `product_id` = `t1`.`id`
							LIMIT 1) AS `price`,
						(SELECT `id` FROM `%2\$s`
							WHERE `product_id` = `t1`.`id`
							ORDER BY `price` ASC
							LIMIT 1) AS `stockId`
						", pjGalleryModel::factory()->getTable(), pjStockModel::factory()->getTable(), pjProductCategoryModel::factory()->getTable()))
					->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='full_desc'", 'left outer')
					->find($_GET['product_id'])
					->getData();
				$this->set('product_arr', $arr);
				
				$extra_arr = pjExtraModel::factory()
					->select('t1.*, t2.content AS name, t3.content AS title')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
					->where('t1.product_id', $_GET['product_id'])
					->orderBy('`title` ASC, `name` ASC')
					->findAll()
					->getData();
	
				foreach ($extra_arr as $k => $extra)
				{
					$extra_arr[$k]['extra_items'] = $pjExtraItemModel
						->reset()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
						->where('t1.extra_id', $extra['id'])
						->orderBy('t1.price ASC')
						->findAll()
						->getData();
				}
				
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = pjAttributeModel::factory()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.product_id', $_GET['product_id'])
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), pjStockModel::factory()->getTable()))
					->orderBy('t1.`order_group` ASC, `order_item` ASC')
					->findAll()
					->getData();
				foreach ($a_arr as $attr)
				{
					if ((int) $attr['parent_id'] === 0)
					{
						$attr_arr[$attr['id']] = $attr;
					} else {
						if (!isset($attr_arr[$attr['parent_id']]['child']))
						{
							$attr_arr[$attr['parent_id']]['child'] = array();
						}
						$attr_arr[$attr['parent_id']]['child'][] = $attr;
					}
				}
	
				$stock_arr = pjStockModel::factory()
					->join('pjProduct', 't1.product_id=t2.id', 'left')
					->where('t1.product_id', $_GET['product_id'])
					->where("(t1.qty > 0 OR t2.is_digital='1')")
					->findAll()
					->getData();
				$_arr = array();
				foreach ($stock_arr as $k => $stock)
				{
					$_qty = $stock['qty'];
					if (isset($order_arr[$stock['id']]))
					{
						$_qty -= $order_arr[$stock['id']];
						if ($_qty < 1)
						{
							unset($stock_arr[$k]);
							continue;
						}
					}
					$stock_arr[$k]['qty'] = $_qty;
					$_arr[$stock['id']] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
				}
				
				$this
					->set('stock_attr_arr', $_arr)
					->set('extra_arr', $extra_arr)
					->set('attr_arr', array_values($attr_arr))
					->set('stock_arr', array_values($stock_arr))
				;
			}
		}
	}
	
	public function pjActionStockEdit()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['stock_edit']))
			{
				$pjOrderStockModel = pjOrderStockModel::factory();
				$arr = $pjOrderStockModel->find($_POST['order_stock_id'])->getData();
				if (empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Order/stock not found'));
				}
				$qty = (int) $_POST['qty'];
				$pjOrderStockModel->modify(array('qty' => $qty));
				if ($arr['qty'] > $qty)
				{
					$diff = $arr['qty'] - $qty;
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty + $diff"));
				} elseif ($arr['qty'] < $qty) {
					$diff = $qty - $arr['qty'];
					pjStockModel::factory()->set('id', $arr['stock_id'])->modify(array('qty' => ":qty - $diff"));
				}
				
				$pjOrderExtraModel = pjOrderExtraModel::factory();
				
				$pjOrderExtraModel->reset()->where('order_stock_id', $_POST['order_stock_id']);
				if (isset($_POST['extra_id']) && !empty($_POST['extra_id']))
				{
					$pjOrderExtraModel->whereNotIn('extra_id', array_keys($_POST['extra_id']));
				}
				$pjOrderExtraModel->eraseAll();
				
				if (isset($_POST['extra_id']) && !empty($_POST['extra_id']))
				{
					$empty_id = $exist_id = array();
					foreach ($_POST['extra_id'] as $extra_id => $value)
					{
						if (empty($value))
						{
							$empty_id[] = $extra_id;
							continue;
						}

						$stack = explode("|", $value);
						switch ($stack[0])
						{
							case 'single':
								if (0 == $pjOrderExtraModel->reset()
									->where('order_stock_id', $_POST['order_stock_id'])
									->where('extra_id', $extra_id)
									->where('extra_item_id IS NULL')
									->findCount()->getData())
								{
									$pjOrderExtraModel->reset()->setAttributes(array(
										'order_id' => $_POST['order_id'],
										'order_stock_id' => $_POST['order_stock_id'],
										'extra_id' => $extra_id,
										'price' => $stack[1]
									))->insert();
								} else {
									//do nothing
									$exist_id[] = $extra_id;
								}
								break;
							case 'multi':
								if (0 == $pjOrderExtraModel->reset()
									->where('order_stock_id', $_POST['order_stock_id'])
									->where('extra_id', $extra_id)
									->findCount()->getData())
								{
									$pjOrderExtraModel->reset()->setAttributes(array(
										'order_id' => $_POST['order_id'],
										'order_stock_id' => $_POST['order_stock_id'],
										'extra_id' => $extra_id,
										'extra_item_id' => $stack[2],
										'price' => $stack[1]
									))->insert();
								} else {
									$pjOrderExtraModel->reset()
										->where('order_id', $_POST['order_id'])
										->where('order_stock_id', $_POST['order_stock_id'])
										->where('extra_id', $extra_id)
										->limit(1)
										->modifyAll(array(
											'extra_item_id' => $stack[2],
											'price' => $stack[1]
										)
									);
									$exist_id[] = $extra_id;
								}
								break;
						}
					}
					
					$pjOrderExtraModel->reset();
					if (!empty($empty_id))
					{
						$pjOrderExtraModel
							->where('order_stock_id', $_POST['order_stock_id'])
							->whereIn('extra_id', $empty_id);
							
						if (!empty($exist_id))
						{
							$pjOrderExtraModel->whereNotIn('extra_id', $exist_id);
						}
						$pjOrderExtraModel->eraseAll();
					}
				} else {
					pjOrderExtraModel::factory()->where('order_stock_id', $_POST['order_stock_id'])->eraseAll();
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			$os_arr = pjOrderStockModel::factory()->find($_GET['order_stock_id'])->getData();
			$oe_arr = pjOrderExtraModel::factory()->where('t1.order_stock_id', $_GET['order_stock_id'])->findAll()->getDataPair('extra_id', 'extra_item_id');
			$stock_arr = pjStockModel::factory()->find($os_arr['stock_id'])->getData();
			
			$stock_arr['attrs'] = pjStockAttributeModel::factory()
				->where('t1.stock_id', $os_arr['stock_id'])
				->orderBy('t1.attribute_id ASC')
				->findAll()
				->getDataPair('attribute_parent_id', 'attribute_id');
			
			$pjExtraItemModel = pjExtraItemModel::factory();
				
			$extra_arr = pjExtraModel::factory()
				->select('t1.*, t2.content AS name, t3.content AS title')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.locale='".$this->getLocaleId()."' AND t3.field='extra_title'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
				->orderBy('`title` ASC, `name` ASC')
				->findAll()
				->getData();

			foreach ($extra_arr as $k => $extra)
			{
				$extra_arr[$k]['extra_items'] = $pjExtraItemModel
					->reset()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='extra_name'", 'left outer')
					->where('t1.extra_id', $extra['id'])
					->orderBy('t1.price ASC')
					->findAll()
					->getData();
			}
				
			$attr_arr = array();
			// Do not change col_name, direction
			$a_arr = pjAttributeModel::factory()
				->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.product_id', $os_arr['product_id'])
				->orderBy('t1.order_group ASC, `order_item` ASC')
				->findAll()
				->getData();

			foreach ($a_arr as $attr)
			{
				if ((int) $attr['parent_id'] === 0)
				{
					$attr_arr[$attr['id']] = $attr;
				} else {
					if (!isset($attr_arr[$attr['parent_id']]['child']))
					{
						$attr_arr[$attr['parent_id']]['child'] = array();
					}
					$attr_arr[$attr['parent_id']]['child'][] = $attr;
				}
			}
			
			$this
				->set('os_arr', $os_arr)
				->set('oe_arr', $oe_arr)
				->set('attr_arr', $attr_arr)
				->set('stock_arr', $stock_arr)
				->set('extra_arr', $extra_arr)
			;
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if (in_array($this->getRoleId(), array(1,2,3)))  //change using this role id
		{
			$pjOrderModel = pjOrderModel::factory();
			if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			{
				$pjOrderModel->where('t1.id', $_REQUEST['id']);
			} elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
				$pjOrderModel->where('t1.uuid', $_GET['uuid']);
			}
			$arr = $pjOrderModel
				->select(sprintf("t1.*,
						AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
						AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
						AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
						AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
						AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
						t2.content AS `b_country`, t3.content AS `s_country`, t4.email AS `admin_email`,
						t6.content AS `confirm_subject_client`, t7.content AS `confirm_tokens_client`, t8.content AS `payment_subject_client`, t9.content AS `payment_tokens_client`,
						t5.email as client_email, t5.client_name, t5.phone as client_phone, t5.url as client_url, AES_DECRYPT(t5.password, '%1\$s') AS `password`", PJ_SALT))
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
					->join('pjUser', 't4.id=1', 'left outer')
					->join('pjClient', 't5.id=t1.client_id', 'left outer')
					->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='confirm_subject_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='confirm_tokens_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_client'", $this->getForeignId()), 'left outer')
					->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
				->limit(1)
				->findAll()
				->getData();
				
			if (empty($arr))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR08");
			}
			$arr = $arr[0];
			
			if (isset($_POST['update_form']))
			{
				if (0 != $pjOrderModel->reset()->where('t1.uuid', $_POST['uuid'])->where('t1.id !=', $_POST['id'])->findCount()->getData())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR02");
				}
				
				$data = array();
				if (isset($_POST['same_as']))
				{
					$data['s_name'] = $_POST['b_name'];
					$data['s_country_id'] = $_POST['b_country_id'];
					$data['s_state'] = $_POST['b_state'];
					$data['s_city'] = $_POST['b_zip'];
					$data['s_zip'] = $_POST['b_address_1'];
					$data['s_address_1'] = $_POST['b_address_1'];
					$data['s_address_2'] = $_POST['b_address_2'];
				} else {
					$data['same_as'] = array(0);
				}
				
				if ($arr['status'] != 'completed' && $_POST['status'] == 'completed')
				{
					$data['processed_on'] = ':NOW()';
				}
				
				$pjInvoiceModel = pjInvoiceModel::factory();
				$_arr = $pjInvoiceModel->where('t1.order_id', $arr['uuid'])->limit(1)->findAll()->getData();
				$_arr = $_arr[0];
				$pjInvoiceModel->reset()->set('id', $_arr['id'])->modify(array('order_id'=>$_POST['uuid']));
				
				$pjOrderModel->reset()->set('id', $_POST['id'])->modify(array_merge($_POST, $data));
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AOR05");
			} else {
				$arr['products'] = pjAppController::pjActionGetProductsString($arr['id'], $arr['locale_id']);
				$arr['has_digital'] = pjAppController::pjActionCheckDigital($arr['id']);
				$stack = pjAppController::pjActionGetOrderStock($arr['id'], $arr['locale_id']);
				
				$tokens = pjAppController::getTokens($arr, $this->option_arr);

				$to = $arr['client_email'];
				$from = $arr['admin_email'];
				$confirm_subject = str_replace($tokens['search'], $tokens['replace'], $arr['confirm_subject_client']);
				$confirm_body = str_replace($tokens['search'], $tokens['replace'], $arr['confirm_tokens_client']);
				$payment_subject = str_replace($tokens['search'], $tokens['replace'], $arr['payment_subject_client']);
				$payment_body = str_replace($tokens['search'], $tokens['replace'], $arr['payment_tokens_client']);
				
				$this
					->set('os_arr', $stack['os_arr'])
					->set('extra_arr', $stack['extra_arr'])
					->set('attr_arr', $stack['attr_arr'])
				;
				
				$this
					->set('arr', $arr)
					->set('country_arr', pjCountryModel::factory()
						->select('t1.*, t2.content AS name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`name` ASC')
						->findAll()
						->getData()
					)
					->set('client_arr', pjClientModel::factory()->orderBy('t1.client_name ASC')->findAll()->getData())
					->set('address_arr', pjAddressModel::factory()
						->select('t1.*, t2.content AS country_name')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.client_id', $arr['client_id'])
						->orderBy('t1.address_1 ASC')->findAll()->getData()
					)
					->set('tax_arr', pjTaxModel::factory()
						->select('t1.*, t2.content AS location')
						->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`location` ASC')
						->findAll()
						->getData()
					)
					->set('from', $from)
					->set('to', $to)
					->set('confirm_subject', $confirm_subject)
					->set('confirm_body', $confirm_body)
					->set('payment_subject', $payment_subject)
					->set('payment_body', $payment_body)
					->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/')
					->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
					->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
					->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tiny_mce_4.1.1/')
					->appendJs('pjAdminOrders.js')
					->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
				;
			}
			
			$this->set('role', $this->getRoleId());//get role id for their views and left menu
		} else {
			$this->set('status', 2);
		}
	}
	
public function pjActionSendShipping()
{
		$this->checkLogin();
		
		if (in_array($this->getRoleId(), array(1,2,3)) && $this->isLoged())
		{
			$orderId = $_POST['orderId'];
			$client_email = $_POST['clientMail'];
			$track_id = $_POST['trackId'];
			
			$pjOrderModel = new pjOrderModel();
			$query = $pjOrderModel
					->select("uuid, locale_id, shipping, s_name, s_state, s_city, s_zip, s_address_1, s_address_2")
					->where('id', $orderId)
					->findAll()
					->getData();

			$uuid = $query[0]['uuid'];
			$locale = $query[0]['locale_id'];
			$shipping_cost = $query[0]['shipping'];
			$s_name = $query[0]['s_name'];
			$s_state = $query[0]['s_state'];
			$s_city = $query[0]['s_city'];
			$s_address_1 = $query[0]['s_address_1'];
			$s_address_2 = $query[0]['s_address_2'];
			$s_zip = $query[0]['s_zip'];
			
			$signature = "<p style='text-align:center'>Exportech Solution Ecommerce Team<br>".
							"4418 N.W 74th Avenue.<br>Miami, Florida<br>33166<br>".
							"<br><img src='https://www.exportechsolution.com/wp-content/uploads/2016/05/small_logo.png' alt=''width='201' height='54'>".
							"</p>";
							
			if((int)$locale == 3){
				$subject = 'Exportech Solution - Confirmación de Envio';
				$body = "<p>Estimado <strong>" . $s_name . "</strong><br><br>" .
 						"Su orden <strong>".$uuid."</strong> ya fue procesada.<br>" .
 						"Los detalles del envio son:</p><br>" .
 						"<h3>Detalles de Envio</h3>" .
 						"<p style='padding-left:30px'>" .
 						"<strong>Codigo de Rastreo: </strong>" . $track_id . "<br>" .
 						"<strong>Costo de Envío:</strong> $" . $shipping_cost . "<br>" .
 						"<strong>Nombre de Cliente: </strong>" . $s_name . "<br>" .
 						"<strong>Dirección de Envío 1: </strong>" . $s_address_1 . "<br>" .
 						"<strong>Dirección de Envío 2: </strong>" . $s_address_2 . "<br>" .
 						"<strong>Ciudad de Envío: </strong>" . $s_city . "<br>" .
 						"<strong>Estado de Envío: </strong>" . $s_country . "<br>" .
 						"<strong>Código Postal: </strong>" . $s_zip . "<br>" .
 						"<strong>País de Envío: </strong> United States</p><br><br>" . $signature;
			}else{
				$subject = 'Exportech Solution - Shipping Confirmation';
				$body = "<p>Dear <strong>" . $s_name . "</strong><br><br>" .
 						"Your order <strong>" . $uuid . "</strong> has been processed.<br>" .
 						"Your shipping details are:</p><br>" .
 						"<h3>Shipping Details</h3>" .
 						"<p style='padding-left:30px'>" .
 						"<strong>Tracking Code: </strong>" .$track_id . "<br>" .
 						"<strong>Shipping Cost:</strong> $" . $shipping_cost . "<br>" .
 						"<strong>Customer's Shipping Name: </strong>" . $s_name . "<br>" .
 						"<strong>Shipping Address 1: </strong>" . $s_address_1 . "<br>" .
 						"<strong>Shipping Address 2: </strong>" . $s_address_2 ."<br>" .
 						"<strong>Shipping City: </strong>" . $s_city . "<br>" .
 						"<strong>Shipping State: </strong>" . $s_country . "<br>" .
 						"<strong>Shipping Zip Code: </strong>" . $s_zip . "<br>" .
 						"<strong>Shipping Country: </strong> United States</p><br><br>" . $signature;	
			}


			$pjEmail = new pjEmail();
			$pjEmail->setContentType('text/html');
			if ($this->option_arr['o_send_email'] == 'smtp')
			{
				$pjEmail
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass']);
			}
			
			$r = $pjEmail
                ->setTo($client_email)
                ->setFrom("ecommerce@exportechsolution.com")
                ->setSubject($subject)
                ->send($body);

            if ($r)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AU13");
			}else{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AU12");	
			}
		}else
		{
			$this->set('status', 2);
		}
	}

	public function pjActionGetStocks()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				# Find out what qty is in current shopping cart for each stock
				$order_arr = array();
					
				$pjStockModel = pjStockModel::factory();
				$pjStockAttributeModel = pjStockAttributeModel::factory();
				$pjAttributeModel = pjAttributeModel::factory();
					
				$stock_arr = $pjStockModel
					->join('pjProduct', 't1.product_id=t2.id', 'left')
					->where('t1.product_id', $_GET['id'])
					->where("(t1.qty > 0 OR t2.is_digital='1')")
					->findAll()->getData();
				
				$stocks = $stock_ids = $qty = $price = array();
				foreach ($stock_arr as $k => $stock)
				{
					$_qty = $stock['qty'];
					if (isset($order_arr[$stock['id']]))
					{
						$_qty -= $order_arr[$stock['id']];
						if ($_qty < 1)
						{
							continue;
						}
					}
					$stock_ids[] = $stock['id'];
					$stocks[] = $pjStockAttributeModel
						->reset()
						->where('t1.stock_id', $stock['id'])
						->where("t1.attribute_id IN (SELECT TA.id FROM `".$pjAttributeModel->getTable()."` AS `TA` WHERE `TA`.product_id='". $_GET['id']."')")
						->orderBy('t1.attribute_id ASC')
						->findAll()
						->getDataPair('attribute_parent_id', 'attribute_id');
						
					$qty[] = $_qty;
					$price[] = $stock['price'];
				}
	
				# -- Fix for empty values in stocks
				$attr_arr = $pjAttributeModel
					->where('t1.product_id', $_GET['id'])
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->findAll()
					->getDataPair('id', 'parent_id');
							
				foreach ($stocks as $k => $stock)
				{
					foreach ($stock as $_k => $_v)
					{
						if ((int) $_v === 0)
						{
							$stokkk = $stock;
							pjUtil::reArrange($stocks, $qty, $price, $stokkk, $attr_arr, $_k, $k);
						}
					}
				}
				# -- End fix
					
				# Attributes --
				$attr_arr = array();
				// Do not change col_name, direction
				$a_arr = $pjAttributeModel
					->reset()
					->select('t1.id, t1.product_id, t1.parent_id, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('t1.product_id', $_GET['id'])
					->where(sprintf("(CONCAT_WS('_', t1.id, t1.parent_id) IN (
							SELECT CONCAT_WS('_', TSA.attribute_id, TSA.attribute_parent_id)
							FROM `%s` AS `TSA`
							INNER JOIN `%s` AS `TS` ON TS.id = TSA.stock_id AND TS.qty > 0
							WHERE TSA.product_id = t1.product_id
						) OR t1.parent_id IS NULL OR t1.parent_id = '0')", $pjStockAttributeModel->getTable(), $pjStockModel->getTable()))
					->orderBy('t1.parent_id ASC, `name` ASC')
					->findAll()
					->getData();
	
				foreach ($a_arr as $attr)
				{
					if ((int) $attr['parent_id'] === 0)
					{
						$attr_arr[$attr['id']] = $attr;
					} else {
						if (!isset($attr_arr[$attr['parent_id']]['child']))
						{
							$attr_arr[$attr['parent_id']]['child'] = array();
						}
						$attr_arr[$attr['parent_id']]['child'][] = $attr;
					}
				}
				$attributes = array_values($attr_arr);
				# Attributes --
	
				# Fix for no-stock
				if (isset($stocks[0]) && empty($stocks[0]))
				{
					$stocks = array();
				}
				pjAppController::jsonResponse(compact('stocks', 'qty', 'price', 'stock_ids', 'attributes'));
			}
		}
		exit;
	}
}
?>
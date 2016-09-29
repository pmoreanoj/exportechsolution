<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	public $defaultForm = 'SCart_Form';
	
	public $defaultCaptcha = 'SCart_Captcha';
	
	public $defaultUser = 'SCart_Client';
	
	public $defaultVoucher = 'SCart_Voucher';
	
	public $defaultCookie = 'SCart_Cookie';
	
	public $defaultTax = 'SCart_Tax';
	
	public $defaultLocale = 'SCart_LocaleId';
	
	public $defaultHash = 'SCart_Hash';
	
	public $defaultLangMenu = 'SCart_LangMenu';
	
	public $defaultCategoryMenu = 'SCart_CategoryMenu';
	
	public $cart = NULL;

	public function __construct()
	{
		$this->setLayout('pjActionFront');
		
		if (!isset($_SESSION[$this->defaultHash]))
		{
			if ($this->isLoged())
			{
				$_SESSION[$this->defaultHash] = md5(PJ_SALT . $this->getUserId());
			} else {
				$_SESSION[$this->defaultHash] = md5(uniqid(rand(), true));
			}
		}
		
		$this->setModel('Cart', pjCartModel::factory());
		$this->cart = new pjShoppingCart($this->getModel('Cart'), $_SESSION[$this->defaultHash]);
		$this->set('cart_arr', $this->cart->getAll());
		
		self::allowCORS();
	}
	
	public function afterFilter()
	{
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionRegister',
				'pjActionProfile', 'pjActionFavs', 'pjActionProducts', 'pjActionProduct',
				'pjActionCart', 'pjActionCheckout', 'pjActionPreview', 'pjActionGetPaymentForm')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			$this->set('locale_arr', $locale_arr);
		}
		$this->set('hidden_ids_arr', pjProductModel::factory()->where('t1.status', 2)->findAll()->getDataPair('id', 'id'));
	}
	
	public function beforeFilter()
	{
		$this->setModel('Option', pjOptionModel::factory());
		$pjOptionModel = $this->getModel('Option');
		$this->option_arr = $pjOptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();
		
		if(isset($_GET['locale']) && $_GET['locale'] == 'ES')
		{
			$this->pjActionSetLocale('3');
		}
		elseif(isset($_GET['locale']) && $_GET['locale'] == 'EN')
		{
			$this->pjActionSetLocale('4');
		}
		elseif(isset($_COOKIE['pll_language']))
		{
			if($_COOKIE['pll_language'] == 'es')
			{
				$this->pjActionSetLocale('3');
			}
			else
			{
				$this->pjActionSetLocale('4');
			}
		}
		elseif(isset($_GET['locale']) && (int) $_GET['locale'] > 0)
		{
			$this->pjActionSetLocale($_GET['locale']);
		}
	
		if ($this->pjActionGetLocale() === FALSE)
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
    			
		if (!in_array($_GET['action'], array('pjActionLoadCss')))
		{
			$this->loadSetFields(true);
		}
	}

	public function beforeRender()
	{
		$this->set('price_arr', $this->pjActionGetPrice());
	}
	
	protected function pjActionGetPrice()
	{
		if ($this->cart->isEmpty())
		{
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$data = $stock_id = $stocks = $product_id = array();
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			if (isset($cart_item['stock_id']) && (int) $cart_item['stock_id'] > 0)
			{
				$stock_id[] = $cart_item['stock_id'];
			}
			$product_id[] = $cart_item['product_id'];
		}
		if (empty($stock_id))
		{
			return array('status' => 'ERR', 'code' => 105, 'text' => 'Empty cart.');
		}
		$stocks = pjStockModel::factory()
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getDataPair('id');

		if (empty($stocks))
		{
			return array('status' => 'ERR', 'code' => 106, 'text' => 'Stocks in cart not found into the database.');
		}
		
		$pjExtraItemModel = pjExtraItemModel::factory();
		$extra_arr = pjExtraModel::factory()->whereIn('t1.product_id', $product_id)->findAll()->getDataPair('id', 'price');
		foreach ($extra_arr as $e_id => $e_price)
		{
			$extra_arr[$e_id] = array(
					'price' => $e_price,
					'extra_items' => $pjExtraItemModel->reset()
					->join('pjExtra', "t2.id=t1.extra_id AND t2.type='multi'", 'inner')
					->where('t1.extra_id', $e_id)->findAll()->getDataPair('id', 'price')
			);
		}
		
		$calc_price = pjAppController::pjActionCalcPrices($product_id, $extra_arr, $cart_arr, $stocks, @$_SESSION[$this->defaultVoucher], $this->option_arr, isset($_SESSION[$this->defaultTax]) ? $_SESSION[$this->defaultTax] : null,  'front');
		if($calc_price == false)
		{
			return array('status' => 'ERR', 'code' => 108, 'text' => __('system_118', true));
		}
		
		$data['price'] = $calc_price['price'];
		$data['discount'] = $calc_price['discount'];
		$data['insurance'] = $calc_price['insurance'];
		$data['shipping'] = $calc_price['shipping'];
		$data['tax'] = $calc_price['tax'];
		$data['total'] = $calc_price['total'];
		$data['total'] = $data['total'] > 0 ? $data['total'] : 0;
		
		return array('status' => 'OK', 'code' => 200, 'text' => 'Success', 'data' => $data);
	}
	
	protected function pjActionGetCart()
	{
		# Find out what qty is in current shopping cart for each stock
		$order_arr = $product_id = $stock_id = array();
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			if (!isset($order_arr[$cart_item['stock_id']]))
			{
				$order_arr[$cart_item['stock_id']] = 0;
			}
			$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
		
			$product_id[] = $cart_item['product_id'];
			if (!empty($cart_item['stock_id']))
			{
				$stock_id[] = $cart_item['stock_id'];
			}
		}
		
		$arr = pjProductModel::factory()
			->select(sprintf("t1.*, t2.content AS name,
				(SELECT GROUP_CONCAT(`category_id`) FROM `%1\$s` WHERE `product_id` = `t1`.`id` LIMIT 1) AS `category_ids`",
				pjProductCategoryModel::factory()->getTable()
			))
			->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='".$this->getLocaleId()."' AND t2.field='name'", 'left outer')
			->join('pjStock', 't3.product_id=t1.id', 'inner')
			->whereIn('t1.id', $product_id)
			->groupBy('t1.id')
			->findAll()
			->toArray('category_ids', ',')
			->getData();

		$tax_arr = pjTaxModel::factory()
			->select('t1.*, t2.content AS location')
			->join('pjMultiLang', "t2.model='pjTax' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`location` ASC')
			->findAll()
			->getData();
			
		$_stock_arr = pjStockModel::factory()
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getData();
		$stock_arr = array();
		foreach ($_stock_arr as $stock)
		{
			$stock_arr[$stock['id']] = $stock;
		}
		
		$image_arr = pjStockModel::factory()
			->select('t1.id, t2.small_path')
			->join('pjGallery', 't2.id=t1.image_id', 'left outer')
			->whereIn('t1.id', $stock_id)
			->findAll()
			->getDataPair('id', 'small_path');
		foreach($image_arr as $id => $img)
		{
			if(empty($img))
			{
				$gallery_arr = pjGalleryModel::factory()
					->select('t1.*')
					->where("`foreign_id` = (SELECT TS.`product_id` FROM `".pjStockModel::factory()->getTable()."` AS TS WHERE TS.id='$id')")
					->limit(1)
					->findAll()
					->getData();
				if(!empty($gallery_arr))
				{
					$image_arr[$id] = $gallery_arr[0]['small_path'];
				}
			}
		}

		$extra_arr = pjAppController::pjActionGetExtrasList($product_id, $this->getLocaleId());
		$attr_arr = pjAppController::pjActionGetAttr($product_id, $this->getLocaleId());
				
		return compact('arr', 'extra_arr', 'order_arr', 'attr_arr', 'stock_arr', 'tax_arr', 'image_arr');
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
		
		$pjCaptcha = new pjCaptcha(PJ_WEB_PATH . 'obj/Lato-Bol.ttf', $this->defaultCaptcha, 6);
		$pjCaptcha
			->setImage(PJ_IMG_PATH . 'button.png')
			->init(@$_GET['rand']);
		exit;
	}
	
	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			echo isset($_SESSION[$this->defaultCaptcha]) && isset($_GET['captcha'])
				&& pjCaptcha::validate($_GET['captcha'], $_SESSION[$this->defaultCaptcha])
				? 'true' : 'false';
		}
		exit;
	}
		
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		
		if (!isset($_POST['x_invoice_num']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjOrderModel = pjOrderModel::factory();

		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['x_invoice_num'])
			->limit(1)
			->findAll()
			->getData();
		if (empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$invoice_arr = $invoice_arr[0];
		$order_arr = $pjOrderModel
			->select(sprintf("t1.*,
				AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
				AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
				AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
				AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
				AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
				t2.content AS b_country, t3.content AS s_country, t4.email AS `admin_email`, t4.phone AS `admin_phone`,
				t6.content AS `payment_subject_client`, t7.content AS `payment_tokens_client`, t8.content AS `payment_subject_admin`,
				t9.content AS `payment_tokens_admin`, t10.content AS `payment_sms_admin`,
				t5.email, t5.client_name, t5.phone, t5.url, AES_DECRYPT(t5.password, '%1\$s') AS `password`", PJ_SALT))
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
			->join('pjUser', 't4.id=1', 'left outer')
			->join('pjClient', 't5.id=t1.client_id', 'left outer')
			->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='payment_subject_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t10.model='pjOption' AND t10.foreign_id='%u' AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", $this->getForeignId()), 'left outer')
			->where('t1.uuid', $invoice_arr['order_id'])
			->limit(1)
			->findAll()
			->getData();
		
		if (empty($order_arr))
		{
			$this->log('Order not found');
			exit;
		}
		$order_arr = $order_arr[0];
		
		$params = array(
			'transkey' => $this->option_arr['o_authorize_key'],
			'x_login' => $this->option_arr['o_authorize_mid'],
			'md5_setting' => $this->option_arr['o_authorize_hash'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		
		$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$pjOrderModel
				->set('id', $order_arr['id'])
				->modify(array(
					'status' => 'completed',
					'processed_on' => ':NOW()'
				));
				
			$pjInvoiceModel
				->reset()
				->set('id', $invoice_arr['id'])
				->modify(array('status' => 'paid', 'modified' => ':NOW()'));
			
			$order_arr['has_digital'] = pjAppController::pjActionCheckDigital($order_arr['id']);
			pjFront::pjActionConfirmSend($this->option_arr, $order_arr, 'payment');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Order not confirmed. ' . $response['response_reason_text']);
		}
		exit;
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		if (!isset($_POST['custom']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjOrderModel = pjOrderModel::factory();
		
		$invoice_arr = $pjInvoiceModel
			->where('t1.uuid', $_POST['custom'])
			->limit(1)
			->findAll()
			->getData();

		if (empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$invoice_arr = $invoice_arr[0];
		
		$order_arr = $pjOrderModel
			->select(sprintf("t1.*,
				AES_DECRYPT(t1.cc_type, '%1\$s') AS `cc_type`,
				AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
				AES_DECRYPT(t1.cc_exp_month, '%1\$s') AS `cc_exp_month`,
				AES_DECRYPT(t1.cc_exp_year, '%1\$s') AS `cc_exp_year`,
				AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`,
				t2.content AS b_country, t3.content AS s_country, t4.email AS `admin_email`, t4.phone AS `admin_phone`,
				t6.content AS `payment_subject_client`, t7.content AS `payment_tokens_client`, t8.content AS `payment_subject_admin`,
				t9.content AS `payment_tokens_admin`, t10.content AS `payment_sms_admin`,
				t5.email, t5.client_name, t5.phone, t5.url, AES_DECRYPT(t5.password, '%1\$s') AS `password`", PJ_SALT))
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.b_country_id AND t2.locale=t1.locale_id AND t2.field='name'", 'left outer')
			->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.s_country_id AND t3.locale=t1.locale_id AND t3.field='name'", 'left outer')
			->join('pjUser', 't4.id=1', 'left outer')
			->join('pjClient', 't5.id=t1.client_id', 'left outer')
			->join('pjMultiLang', sprintf("t6.model='pjOption' AND t6.foreign_id='%u' AND t6.locale=t1.locale_id AND t6.field='payment_subject_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t7.model='pjOption' AND t7.foreign_id='%u' AND t7.locale=t1.locale_id AND t7.field='payment_tokens_client'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t8.model='pjOption' AND t8.foreign_id='%u' AND t8.locale=t1.locale_id AND t8.field='payment_subject_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t9.model='pjOption' AND t9.foreign_id='%u' AND t9.locale=t1.locale_id AND t9.field='payment_tokens_admin'", $this->getForeignId()), 'left outer')
			->join('pjMultiLang', sprintf("t10.model='pjOption' AND t10.foreign_id='%u' AND t10.locale=t1.locale_id AND t10.field='payment_sms_admin'", $this->getForeignId()), 'left outer')
			->where('t1.uuid', $invoice_arr['order_id'])
			->limit(1)
			->findAll()
			->getData();
		if (empty($order_arr))
		{
			$this->log('Order not found');
			exit;
		}
		$order_arr = $order_arr[0];
		
		$params = array(
			'txn_id' => @$invoice_arr['txn_id'],
			'paypal_address' => @$this->option_arr['o_paypal_address'],
			'deposit' => @$invoice_arr['total'],
			'currency' => @$invoice_arr['currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjOrderModel->reset()->set('id', $order_arr['id'])->modify(array(
				'status' => 'pending',
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			
			$pjInvoiceModel
				->reset()
				->set('id', $invoice_arr['id'])
				->modify(array('status' => 'paid', 'modified' => ':NOW()'));

			$order_arr['has_digital'] = pjAppController::pjActionCheckDigital($order_arr['id']);
			pjFront::pjActionConfirmSend($this->option_arr, $order_arr, 'payment');
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		exit;
	}
	
	protected static function pjActionConfirmSend($option_arr, $order_arr, $type)
	{
		if (!in_array($type, array('confirm', 'payment')))
		{
			return false;
		}
		$pjEmail = new pjEmail();
		$pjEmail->setContentType('text/html');
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$pjEmail
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$order_arr['products'] = pjAppController::pjActionGetProductsString($order_arr['id'], $order_arr['locale_id']);
		$tokens = pjAppController::getTokens($order_arr, $option_arr);

		switch ($type)
		{
			case 'confirm':
				//client
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_tokens_client']);

				$pos = strrpos($message, "bank");
				if ($pos !== false) {
				 $rep = "<br><strong>Bank Information: <br></strong><p style='padding-left:30px'>Citibank<br /> Owner of account: Siaproci-INH Inc<br />" .
				         "Account number: 067004764<br /> Routing number: 3290291435<br /></p> <br />*Please after making the payment, send the transfer" . 
				         " information with the order id from our shop to accounting@exportechsolution.com and edministration@exportechsolution.com<br />"; 
				 $pos = $pos + 4;
				 $message = substr_replace($message, $rep, $pos, 0);
				}

				$pjEmail
					->setTo($order_arr['email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				//admin
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['confirm_tokens_admin']);
				$pjEmail
					->setTo($order_arr['admin_email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				
				# SMS
				if (pjObject::getPlugin('pjSms') !== NULL && isset($order_arr['admin_phone']) && !empty($order_arr['admin_phone']))
				{
					$dispatcher = new pjDispatcher();
					$controller = $dispatcher->createController(array('controller' => 'pjFront'));
					$controller->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => array(
						'number' => $order_arr['admin_phone'],
						'text' => str_replace($tokens['search'], $tokens['replace'], @$order_arr['confirm_sms_admin']),
						'key' => md5($option_arr['private_key'] . PJ_SALT),
						'type' => 'unicode'
					)), array('return'));
				}
				break;
			case 'payment':
				//client
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_subject_client']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_tokens_client']);
				$pjEmail
					->setTo($order_arr['email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				//admin
				$subject = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_subject_admin']);
				$message = str_replace($tokens['search'], $tokens['replace'], $order_arr['payment_tokens_admin']);
				$pjEmail
					->setTo($order_arr['admin_email'])
					->setFrom($order_arr['admin_email'])
					->setSubject($subject)
					->send($message);
				
				# SMS
				if (pjObject::getPlugin('pjSms') !== NULL && isset($order_arr['admin_phone']) && !empty($order_arr['admin_phone']))
				{
					$dispatcher = new pjDispatcher();
					$controller = $dispatcher->createController(array('controller' => 'pjFront'));
					$controller->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => array(
						'number' => $order_arr['admin_phone'],
						'text' => str_replace($tokens['search'], $tokens['replace'], @$order_arr['payment_sms_admin']),
						'key' => md5($option_arr['private_key'] . PJ_SALT),
						'type' => 'unicode'
					)), array('return'));
				}
				break;
		}
	}
	
	public function pjActionDigitalDownload()
	{
		$this->setLayout('pjActionEmpty');
		
		if (!isset($_GET['uuid']) || empty($_GET['uuid']) || !isset($_GET['hash']) || empty($_GET['hash']) || md5($_GET['uuid'] . PJ_SALT) != $_GET['hash'])
		{
			$this->set('status', 1);
			return;
		}
		
		$order = pjOrderModel::factory()->where('t1.uuid', $_GET['uuid'])->limit(1)->findAll()->getData();
		if (empty($order))
		{
			$this->set('status', 2);
			return;
		}
		
		$order = $order[0];
		if ($order['status'] != 'completed')
		{
			$this->set('status', 3);
			return;
		}
		
		$os_arr = pjOrderStockModel::factory()
			->select('t3.digital_file, t3.digital_name, t3.digital_expire, t2.processed_on,
				DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) AS `expire_at`,
				IF(DATE_ADD(t2.processed_on, INTERVAL t3.digital_expire HOUR_SECOND) < NOW(), 1, 0) AS `is_expired`')
			->join('pjOrder', 't2.id=t1.order_id', 'inner')
			->join('pjProduct', "t3.id=t1.product_id AND t3.is_digital='1'", 'inner')
			->where('t1.order_id', $order['id'])
			->findAll()
			->getData();

		if (empty($os_arr))
		{
			$this->set('status', 4);
			return;
		}
		
		$digitals = $expired = array();
		foreach ($os_arr as $item)
		{
			if ((int) $item['is_expired'] === 0 || $item['digital_expire'] == '00:00:00')
			{
				$digitals[] = $item;
			} else {
				$expired[] = $item;
			}
		}
		
		if (empty($digitals))
		{
			$this->set('status', 5);
			return;
		}
		
		$zip = new pjZipStream();
		foreach ($digitals as $file)
		{
			if (empty($file['digital_file']) || !is_file($file['digital_file']))
			{
				continue;
			}
			$handle = @fopen($file['digital_file'], "rb");
			if ($handle)
			{
				$zip->addLargeFile($file['digital_file'], $file['digital_name']);
				fclose($handle);
			}
		}
		$zip->finalize();
		$zip->sendZip(sprintf("%s.zip", $order['uuid']));
		exit;
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
				$cart_arr = $this->get('cart_arr');
				foreach ($cart_arr as $cart_item)
				{
					if (!isset($order_arr[$cart_item['stock_id']]))
					{
						$order_arr[$cart_item['stock_id']] = 0;
					}
					$order_arr[$cart_item['stock_id']] += $cart_item['qty'];
				}
				
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
					->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->pjActionGetLocale()."'", 'left outer')
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

	public function pjActionSendToFriend()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['url']) || empty($_POST['url']) ||
				!isset($_POST['your_email']) || empty($_POST['your_email']) || !pjValidation::pjActionEmail($_POST['your_email']) ||
				!isset($_POST['your_name']) || empty($_POST['your_name']) ||
				!isset($_POST['friend_email']) || empty($_POST['friend_email']) || !pjValidation::pjActionEmail($_POST['friend_email']) ||
				!isset($_POST['friend_name']) || empty($_POST['friend_name']) ||
				!isset($_POST['captcha']) || empty($_POST['captcha'])
				|| !isset($_SESSION[$this->defaultCaptcha])
				|| !pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha])
			)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('system_100', true)));
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
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$lang_message = $pjMultiLangModel
				->reset()
				->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'send_to_tokens')
				->limit(0, 1)
				->findAll()
				->getData();
			$lang_subject = $pjMultiLangModel
				->reset()
				->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'send_to_subject')
				->limit(0, 1)
				->findAll()
				->getData();
			
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace(array('{FriendName}', '{FriendEmail}', '{YourName}', '{YourEmail}', '{URL}'), array(pjSanitize::html($_POST['friend_name']), pjSanitize::html($_POST['friend_email']), pjSanitize::html($_POST['your_name']), pjSanitize::html($_POST['your_email']), pjSanitize::html($_POST['url'])), $lang_message[0]['content']);
			
				$result = $pjEmail
					->setContentType('text/html')
					->setTo($_POST['friend_email'])
					->setFrom($_POST['your_email'])
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
				if (!$result)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('system_101', true)));
				}
			}
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('system_200', true)));
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-type: text/javascript");

		if(isset($_GET['locale']) && $_GET['locale'] > 0)
		{
			$this->loadSetFields(true);
			$_SESSION[$this->defaultLangMenu] = 'hide';
		}else{
			$_SESSION[$this->defaultLangMenu] = 'show';
		}
		if(isset($_GET['category_id']) && $_GET['category_id'] > 0)
		{
			$_SESSION[$this->defaultCategoryMenu] = 'hide';
		}else{
			$_SESSION[$this->defaultCategoryMenu] = 'show';
		}
	}
	
	public function pjActionLoadCss()
	{
		$layout = isset($_GET['layout']) && in_array($_GET['layout'], $this->getLayoutRange()) ?
			(int) $_GET['layout'] : (int) $this->option_arr['o_layout'];
		$theme = isset($_GET['theme']) ? $_GET['theme'] : $this->option_arr['o_theme'];
		if((int) $theme > 0)
		{
			$theme = 'theme' . $theme;
		}
		$arr = array(
				array('file' => 'ShoppingCart'.$layout.'.css', 'path' => PJ_CSS_PATH),
				array('file' => 'jquery.fancybox.css', 'path' => PJ_LIBS_PATH . 'pjQ/fancybox/'),
				array('file' => $theme.'.css', 'path' => PJ_CSS_PATH)
			);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb'))
			{
				$string = stream_get_contents($stream);
				fclose($stream);
			}
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../fonts/', "url('","pjWrapper"),
					array(
						PJ_INSTALL_URL . PJ_LIBS_PATH . 'pjQ/bootstrap/fonts/',
						"url('" . PJ_INSTALL_URL . PJ_LIBS_PATH . "pjQ/fancybox/img/",
						"pjWrapperShoppingCart_" . $theme),
					$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLogout()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if ($this->isLoged())
			{
				$_SESSION[$this->defaultUser] = NULL;
				unset($_SESSION[$this->defaultUser]);
				
				$_SESSION[$this->defaultHash] = NULL;
				unset($_SESSION[$this->defaultHash]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 201, 'text' => __('system_201', true)));
		}
		exit;
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				if($_GET['locale_id'] == 'ES')
				{
					$this->pjActionSetLocale('3');	
				}else{
					$this->pjActionSetLocale('4');
				}
				$this->loadSetFields(true);
			}
		}
		exit;
	}
	
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}

	public function pjActionShowShipping()
	{
		$cart_arr = $this->get('cart_arr');
		foreach ($cart_arr as $cart_item)
		{
			$item = unserialize($cart_item['key_data']);
			if ((int) $item['is_digital'] === 0)
			{
				return true;
				break;
			}
		}
		
		return false;
	}
	
	protected function pjActionSaveToAddressBook($client_id, $data, $prefix='b_')
	{
		return pjAddressModel::factory()->setAttributes(array(
			'client_id' => $client_id,
			'country_id' => @$data[$prefix.'country_id'],
			'state' => @$data[$prefix.'state'],
			'city' => @$data[$prefix.'city'],
			'zip' => @$data[$prefix.'zip'],
			'address_1' => @$data[$prefix.'address_1'],
			'address_2' => @$data[$prefix.'address_2'],
			'name' => @$data[$prefix.'name']
		))->insert()->getInsertId();
	}
	
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>
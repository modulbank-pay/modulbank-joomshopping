<?php
defined('_JEXEC') or die('Restricted access');
if (!class_exists("ModulbankHelper")) {
	include_once __DIR__ . '/modulbanklib/ModulbankHelper.php';
}

if (!class_exists("ModulbankReceipt")) {
	include_once __DIR__ . '/modulbanklib/ModulbankReceipt.php';
}

class pm_modulbank extends PaymentRoot
{

	public function __construct()
	{
		JLog::addLogger(
			array(
				'logger'   => 'callback',
				'callback' => array($this, 'callbackLog'),
			),
			JLog::ALL,
			array('pm_modulbank')
		);
	}

	public function showPaymentForm($params, $pmconfigs)
	{
		include dirname(__FILE__) . "/paymentform.php";
	}

	//function call in admin
	public function showAdminFormParams($params)
	{
		if ($params == "") {
			$params = [];
		}

		$data                 = [];
		$data['vat_catalog']  = array(array('id' => '0', 'name' => 'Брать из настроек товара'));
		$data['vat_delivery'] = array(array('id' => '0', 'name' => 'Брать из настроек доставки'));
		$data['vat_list']     = array(
			array('id' => 'none', 'name' => 'Без НДС'),
			array('id' => 'vat0', 'name' => 'НДС по ставке 0%'),
			array('id' => 'vat10', 'name' => 'НДС чека по ставке 10%'),
			array('id' => 'vat20', 'name' => 'НДС чека по ставке 20%'),
			array('id' => 'vat110', 'name' => 'НДС чека по расчетной ставке 10%'),
			array('id' => 'vat120', 'name' => 'НДС чека по расчетной ставке 20%'),
		);

		$data['sno_list'] = array(
			array('id' => 'osn', 'name' => 'Общая СН'),
			array('id' => 'usn_income', 'name' => 'Упрощенная СН (доходы)'),
			array('id' => 'usn_income_outcome', 'name' => 'Упрощенная СН (доходы минус расходы)'),
			array('id' => 'envd', 'name' => 'Единый налог на вмененный доход'),
			array('id' => 'esn', 'name' => 'Единый сельскохозяйственный налог'),
			array('id' => 'patent', 'name' => 'Патентная СН'),
		);

		$data['payment_method_list'] = array(
			array('id' => 'full_prepayment', 'name' => 'Предоплата 100%'),
			array('id' => 'prepayment', 'name' => 'Предоплата'),
			array('id' => 'advance', 'name' => 'Аванс'),
			array('id' => 'full_payment', 'name' => 'Полный расчет'),
			array('id' => 'partial_payment', 'name' => 'Частичный расчет и кредит'),
			array('id' => 'credit', 'name' => 'Передача в кредит'),
			array('id' => 'credit_payment', 'name' => 'Оплата кредита'),
		);

		$data['payment_object_list'] = array(
			array('id' => 'commodity', 'name' => 'Товар'),
			array('id' => 'excise', 'name' => 'Подакцизный товар'),
			array('id' => 'job', 'name' => 'Работа'),
			array('id' => 'service', 'name' => 'Услуга'),
			array('id' => 'gambling_bet', 'name' => 'Ставка азартной игры'),
			array('id' => 'gambling_prize', 'name' => 'Выигрыш азартной игры'),
			array('id' => 'lottery', 'name' => 'Лотерейный билет'),
			array('id' => 'lottery_prize', 'name' => 'Выигрыш лотереи'),
			array('id' => 'intellectual_activity', 'name' => 'Предоставление результатов интеллектуальной деятельности'),
			array('id' => 'payment', 'name' => 'Платеж'),
			array('id' => 'agent_commission', 'name' => 'Агентское вознаграждение'),
			array('id' => 'composite', 'name' => 'Составной предмет расчета'),
			array('id' => 'another', 'name' => 'Другое'),
		);

		$settings = array(
			'merchant'                   => '',
			'secret_key'                 => '',
			'test_secret_key'            => '',
			'mode'                       => 'test',
			'success_url'                => JURI::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_modulbank',
			'fail_url'                   => JURI::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_modulbank',
			'back_url'                   => JURI::root() . 'index.php?option=com_jshopping&controller=checkout&task=step5',
			'sno'                        => 'usn_income_outcome',
			'product_vat'                => 'none',
			'delivery_vat'               => 'none',
			'payment_method'             => 'full_prepayment',
			'payment_object'             => 'commodity',
			'payment_object_delivery'    => 'service',
			'logging'                    => 0,
			'transaction_end_status'     => 6, //Paid
			'transaction_pending_status' => 1, //Pending
			'transaction_refund_status'  => 4, //Refunded
			'log_size_limit'             => 10,
		);
		foreach ($settings as $key => $value) {
			if (!isset($params[$key])) {
				$params[$key] = $value;
			}

		}
		$orders = JModelLegacy::getInstance('orders', 'JshoppingModel'); //admin model
		include dirname(__FILE__) . "/adminparamsform.php";
	}

	public function checkTransaction($pmconfigs, $order, $act)
	{
		//http://localhost.ru/joomsh/index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_modulbank&no_lang=1
		$jshopConfig   = JSFactory::getConfig();
		$db            = JFactory::getDBO();
		$state         = JRequest::getVar('state');
		$transactionId = JRequest::getVar('transaction_id');
		$amount        = JRequest::getVar('amount');
		$post          = JRequest::get('post');
		$this->log($post, 'callback', $pmconfigs);

		if ($this->checkSign($pmconfigs)) {
			$db->setQuery("
				REPLACE INTO #__modulbank_transactions
				(order_id, amount, transaction) VALUES
				({$order->order_id}," . $db->quote($amount) . ", " . $db->quote($transactionId) . ")");
			$db->query();
			if ($state === 'COMPLETE') {
				$status   = $pmconfigs['transaction_end_status'];
				$checkout = JModelLegacy::getInstance('checkout', 'jshop');
				if ($status && !$order->order_created) {
					$order->order_created = 1;
					$order->order_status  = $status;
					$order->store();
					$checkout->sendOrderEmail($order->order_id);
					$order->changeProductQTYinStock("-");
					$checkout->changeStatusOrder($order->order_id, $status, 0);
				}

				if ($status && $order->order_status != $status) {
					$checkout->changeStatusOrder($order->order_id, $status, 1);
				}
			}
		} else {
			$this->error("Wrong signature");
		}
		die();

	}

	public function showEndForm($pmconfigs, $order)
	{
		$jshopConfig = JSFactory::getConfig();
		$amount      = number_format(round($order->order_total, 2), 2, '.', '');
		$receipt     = new ModulbankReceipt($pmconfigs['sno'], $pmconfigs['payment_method'], $amount);
		$cart        = JSFactory::getModel('cart', 'jshop');

		if (method_exists($cart, 'init')) {
			$cart->init('cart', 1);
		} else {
			$cart->load('cart');
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jshopping_taxes");
		$taxes = $db->loadObjectList('tax_id');

		foreach ($cart->products as $product) {
			if ($pmconfigs['product_vat']) {
				$nds = $pmconfigs['product_vat'];
			} else {
				switch ($taxes[$product['tax_id']]->tax_value) {
					case 20:$nds = 'vat20';
						break;
					case 10:$nds = 'vat10';
						break;
					default:$nds = 'none';
						break;
				}
			}
			$receipt->addItem($product['product_name'], $product['price'], $nds, $pmconfigs['payment_object'], $product['quantity']);
		}

		$shipping        = false;
		$shippingModel   = JSFactory::getTable('shippingMethod', 'jshop');
		$shippingMethods = $shippingModel->getAllShippingMethodsCountry($order->d_country, $order->payment_method_id);
		foreach ($shippingMethods as $tmp) {
			if ($tmp->shipping_id == $order->shipping_method_id) {
				$shipping = $tmp;
			}
		}

		if ($order->shipping_method_id && $shipping) {
			if ($pmconfigs['delivery_vat']) {
				$nds = $pmconfigs['delivery_vat'];
			} else {
				switch ($taxes[$shipping->shipping_tax_id]->tax_value) {
					case 20:$nds = 'vat20';
						break;
					case 10:$nds = 'vat10';
						break;
					default:$nds = 'none';
						break;
				}
			}
			$receipt->addItem($shipping->name, $shipping->shipping_stand_price, $nds, $pmconfigs['payment_object_delivery']);
		}

		$callbackUrl = JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_modulbank&no_lang=1&order_id=" . $order->order_id;

		$url     = "https://pay.modulbank.ru/pay";
		$sysinfo = [
			'language' => 'PHP ' . phpversion(),
			'plugin'   => $this->getVersion(),
			'cms'      => $this->getCmsVersion(),
		];
		$data = [
			'merchant'        => $pmconfigs['merchant'],
			'amount'          => $amount,
			'order_id'        => $order->order_id,
			'testing'         => $pmconfigs['mode'] == 'test' ? 1 : 0,
			'description'     => 'Оплата заказа №' . $order->order_number,
			'success_url'     => $pmconfigs['success_url'],
			'fail_url'        => $pmconfigs['fail_url'],
			'cancel_url'      => $pmconfigs['back_url'],
			'callback_url'    => $callbackUrl,
			'client_name'     => $order->f_name . ' ' . $order->l_name,
			'client_email'    => $order->email,
			'receipt_contact' => $order->email,
			'receipt_items'   => $receipt->getJson(),
			'unix_timestamp'  => time(),
			'sysinfo'         => json_encode($sysinfo),
			'salt'            => ModulbankHelper::getSalt(),
		];
		$key               = $pmconfigs['mode'] == 'test' ? $pmconfigs['test_secret_key'] : $pmconfigs['secret_key'];
		$data['signature'] = ModulbankHelper::calcSignature($key, $data);

		$this->log($data, 'paymentForm', $pmconfigs);
		$html = '
				Сейчас вы будете перемещены на страницу оплаты, если этого не произошло, нажмите кнопку оплатить.
				<form  name="paymentform" id="paymentform" method="post" action="' . $url . '">';
		foreach ($data as $key => $value) {
			$html .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
		}
		$html .= '<input type="submit" name="" value="Оплатить">
				</form>';

		$order->order_created = 1;
		$order->order_status  = $pmconfigs['transaction_pending_status'];
		$order->store();
		$checkout = JModelLegacy::getInstance('checkout', 'jshop');
		$checkout->sendOrderEmail($order->order_id);
		?>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>
		<body>
		<?php echo $html; ?>
		<script type="text/javascript">document.getElementById('paymentform').submit();</script>
		</body>
		</html>
<?php

		die();
	}

	private function error($msg)
	{
		$this->log($msg, 'error');
		throw new Exception($msg, 1);

	}

	private function getVersion()
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT manifest_cache FROM #__extensions WHERE element='pkg_joomshopping_modulbank'");
		$json = $db->loadResult();
		$data = json_decode($json);
		return $data->version;
	}

	private function getCmsVersion()
	{
		$jversion    = new JVersion();
		$jshopConfig = JSFactory::getConfig();
		$data        = JApplicationHelper::parseXMLInstallFile($jshopConfig->admin_path . "jshopping.xml");
		return 'Joomla ' . $jversion->getShortVersion() . ' JShopping ' . $data['version'];
	}

	public function getUrlParams($pmconfigs)
	{
		$params              = array();
		$params['order_id']  = JRequest::getInt("order_id");
		$params['hash']      = "";
		$params['checkHash'] = 0;
		return $params;
	}

	public function checkSign($config)
	{
		$key       = $config['mode'] == 'test' ? $config['test_secret_key'] : $config['secret_key'];
		$post      = JRequest::get('post');
		$signature = ModulbankHelper::calcSignature($key, $post);
		return strcasecmp($signature, JRequest::getVar('signature')) == 0;
	}

	public function getModulbankTransactionStatus($config, $order_id)
	{
		$key         = $config['mode'] == 'test' ? $config['test_secret_key'] : $config['secret_key'];
		$transaction = JFactory::getSession()->get('modulbank_transaction_id');
		$merchant    = $config['merchant'];

		$this->log([$merchant, $transaction], 'getModulbankTransactionStatus', $config);
		$result = ModulbankHelper::getTransactionStatus(
			$config['merchant'],
			$transaction,
			$key
		);
		$this->log($result, 'getModulbankTransactionStatus_response', $config);
		$result = json_decode($result);

		$msg = "<b>Статус оплаты:</b> Ожидаем поступления средств";
		if (isset($result->status) && $result->status == "ok") {

			switch ($result->transaction->state) {
				case 'PROCESSING':$msg = "<b>Статус оплаты:</b> В процессе";
					break;
				case 'WAITING_FOR_3DS':$msg = "<b>Статус оплаты:</b> Ожидает 3DS";
					break;
				case 'FAILED':$msg = "<b>Статус оплаты:</b> При оплате возникла ошибка";
					break;
				case 'COMPLETE':$msg = "<b>Статус оплаты:</b> Оплата прошла успешно";
					break;
				default:$msg = "<b>Статус оплаты:</b> Ожидаем поступления средств";
			}
		}
		return $msg;
	}

	public function refund($config, $order_id)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT transaction, amount FROM #__modulbank_transactions WHERE order_id=$order_id");
		$transaction = $db->loadObject();
		$key         = $config['mode'] == 'test' ? $config['test_secret_key'] : $config['secret_key'];
		if ($transaction) {
			$this->log([
				$config['merchant'],
				$transaction->amount,
				$transaction->transaction], 'refund', $config);

			$result = ModulbankHelper::refund(
				$config['merchant'],
				$transaction->amount,
				$transaction->transaction,
				$key
			);
			$this->log($result, 'refund_response', $config);
		}
	}

	public function log($data, $category, $config)
	{
		if ($config['logging']) {
			$context = [
				'data' => $data,
				'size' => $config['log_size_limit'],
			];
			JLog::add($category, JLog::INFO, 'pm_modulbank', null, $context);
		}
	}

	public function callbackLog($entry)
	{
		$logName = JFactory::getConfig()->get('log_path') . '/modulbank.log';
		ModulbankHelper::log($logName, $entry->context['data'], $entry->message, $entry->context['size']);
	}
}

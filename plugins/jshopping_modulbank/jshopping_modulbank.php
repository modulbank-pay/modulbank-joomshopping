<?php

defined('_JEXEC') or die;

class plgJshoppingorderjshopping_modulbank extends JPlugin
{
	public function __construct(&$subject, $config)
	{

		parent::__construct($subject, $config);

	}

	public function onBeforeDisplayCheckoutFinish(&$text, &$order_id){
		$order = JSFactory::getTable('order', 'jshop');
		$order->load($order_id);
		$pm_method = $order->getPayment();
		$paymentsysdata = $pm_method->getPaymentSystemData();
		$payment_system = $paymentsysdata->paymentSystem;
		if ($payment_system && method_exists($payment_system, "getModulbankTransactionStatus")){
			$pmconfigs = $pm_method->getConfigs();
			$text .= "<div class='modulbank_transaction_thx'>Спасибо за заказ!</div><div class='modulbank_transaction_state'>".$payment_system->getModulbankTransactionStatus($pmconfigs, $order_id)."</div>";
		}
	}

	public function onAfterChangeOrderStatus($order_id, $status, $message)
	{
		$order = JTable::getInstance('order', 'jshop');
		$order->load($order_id);
		$this->onUpdateStatus($order);
		return true;
	}
	public function onAfterChangeOrderStatusAdmin($order_id, $order_status, $status_id, $notify, $comments, $include, $view_order)
	{
		$order = JTable::getInstance('order', 'jshop');
		$order->load($order_id);
		$this->onUpdateStatus($order);
		return true;
	}



	private function onUpdateStatus($order)
	{
		$pm_method = $order->getPayment();
		$paymentsysdata = $pm_method->getPaymentSystemData();
		$payment_system = $paymentsysdata->paymentSystem;
		if ($payment_system && get_class($payment_system) == 'pm_modulbank'){
			$pmconfigs = $pm_method->getConfigs();
			if ($pmconfigs['transaction_refund_status'] == $order->order_status) {
				$payment_system->refund($pmconfigs, $order->order_id);
			}
			if ($pmconfigs['preauth'] && $pmconfigs['transaction_capture_status'] == $order->order_status) {
				$payment_system->capture($pmconfigs, $order->order_id);
			}
		}
		return true;

	}

}

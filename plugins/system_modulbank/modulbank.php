<?php
defined('_JEXEC') or die;

if(!class_exists("ModulbankHelper")) {
	include_once JPATH_ROOT.'/components/com_jshopping/payments/pm_modulbank/modulbanklib/ModulbankHelper.php';
}

class plgsystemModulbank extends JPlugin
{

	protected $app;

	public function __construct(&$subject, $config)
	{

		parent::__construct($subject, $config);

	}
	public function onAfterInitialise()
	{

		if ($this->app->isClient('site')){
			$id = $this->app->input->get('transaction_id', '', 'string');
			if ($id) {
				JFactory::getSession()->set('modulbank_transaction_id', $id);
			}
			return;
		}
		$user = JFactory::getUser();
		if ($user->authorise('core.manage','com_jshopping')) {
			$log = $this->app->input->get('download_modulbank_logs', 0, 'int');
			if($log) {
				ModulbankHelper::sendPackedLogs(JFactory::getConfig()->get('log_path'));
			}
		}
	}

}

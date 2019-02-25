<?php
defined('_JEXEC') or die;
class pkg_joomshopping_modulbankInstallerScript
{
	public function postflight($type, $parent, $result)
	{
		if ($type == 'install') {
			$db = JFactory::getDBO();
			$db->setQuery("
				CREATE TABLE IF NOT EXISTS `#__modulbank_transactions` (
						order_id		INT(11)  NOT NULL,
						amount			DECIMAL(13,2) NOT NULL,
						transaction		VARCHAR(32) NULL,
						PRIMARY KEY (order_id)
				);
			");
			$db->query();
			$db->setQuery('UPDATE #__extensions set enabled=1 where `type`="plugin" and (
				(element="jshopping_modulbank" and folder="jshoppingorder") or
				(element="modulbank" and folder="system")
				)');
			$db->query();

			JFolder::move(dirname(__FILE__) . '/pm_modulbank', JPATH_ROOT . '/components/com_jshopping/payments/pm_modulbank');
			$db->setQuery('insert into #__jshopping_payment_method (payment_code, payment_class,  payment_publish,  payment_type, price,  price_type,show_descr_in_email,`name_ru-RU`,`name_en-GB`) values("modulbank","pm_modulbank",0,2,0.00,0,0,"Модульбанк Интернет-эквайринг","Модульбанк Интернет-эквайринг")');
			$db->query();
			$id = $db->insertid();
			echo "<a href='index.php?option=com_jshopping&controller=payments&task=edit&payment_id=" . $id . "'>Перейти к настройке</a>";
		}
		if ($type == 'update') {
			$db = JFactory::getDBO();
			$db->setQuery('select payment_code from #__jshopping_payment_method WHERE payment_code="modulbank"');
			if (!$db->loadResult()) {
				$db->setQuery('insert into #__jshopping_payment_method (payment_code, payment_class,  payment_publish,  payment_type, price,  price_type,show_descr_in_email,`name_ru-RU`,`name_en-GB`) values("modulbank","pm_modulbank",0,2,0.00,0,0,"Модульбанк Интернет-эквайринг","Модульбанк Интернет-эквайринг")');
				$db->query();
			}
			JFolder::delete(JPATH_ROOT . '/components/com_jshopping/payments/pm_modulbank');
			JFolder::move(dirname(__FILE__) . '/pm_modulbank', JPATH_ROOT . '/components/com_jshopping/payments/pm_modulbank');
		}

		return true;
	}

	public function uninstall($x)
	{
		$db = JFactory::getDBO();
		$db->setQuery('DELETE from  #__jshopping_payment_method where  payment_class="pm_modulbank"');
		$db->query();
		$db->setQuery('DROP TABLE IF EXISTS `#__modulbank_transactions`;');
		$db->query();
		JFolder::delete(JPATH_ROOT . '/components/com_jshopping/payments/pm_modulbank');
	}

}

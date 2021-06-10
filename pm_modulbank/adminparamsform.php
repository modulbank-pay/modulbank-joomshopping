<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
 <tr>
   <td style="width:250px;" class="key">
     Мерчант
   </td>
   <td>
<input type = "text" class = "inputbox" name = "pm_params[merchant]" size="45" value = "<?php echo $params['merchant']?>" />
   </td>
 </tr>
  <tr>
   <td style="width:250px;" class="key">
     Секретный ключ
   </td>
   <td>
<input type = "text" class = "inputbox" name = "pm_params[secret_key]" size="45" value = "<?php echo $params['secret_key']?>" />
   </td>
 </tr>
 <tr>
   <td  class="key">
     Тестовый секретный ключ
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[test_secret_key]" size="45" value = "<?php echo $params['test_secret_key']?>" />
   </td>
 </tr>
  <tr>
   <td  class="key">
     Адрес для перехода после успешной оплаты
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[success_url]" size="45" value = "<?php echo $params['success_url']?>" />
   </td>
 </tr>
 <tr>
   <td  class="key">
     Адрес для перехода после ошибки при оплате
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[fail_url]" size="45" value = "<?php echo $params['fail_url']?>" />
   </td>
 </tr>
 <tr>
   <td  class="key">
     Адрес для перехода в случае нажатия кнопки «Вернуться в магазин»
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[back_url]" size="45" value = "<?php echo $params['back_url']?>" />
   </td>
 </tr>
 <tr>
   <td  class="key">
     Режим
   </td>
   <td>
    <?php
    $options = array(
      array('id' => 'test', 'name' => 'Тестовый'),
      array('id' => 'prod', 'name' => 'Рабочий'),
    );
    echo JHTML::_('select.genericlist', $options, 'pm_params[mode]', 'class = "inputbox" size = "1"', 'id', 'name', $params['mode'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     НДС на товары
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', array_merge($data['vat_catalog'],$data['vat_list']), 'pm_params[product_vat]', 'class = "inputbox" size = "1"', 'id', 'name', $params['product_vat'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     НДС на доставку
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', array_merge($data['vat_delivery'],$data['vat_list']), 'pm_params[delivery_vat]', 'class = "inputbox" size = "1"', 'id', 'name', $params['delivery_vat'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Система налогообложения
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', $data['sno_list'], 'pm_params[sno]', 'class = "inputbox" size = "1"', 'id', 'name', $params['sno'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Метод платежа
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', $data['payment_method_list'], 'pm_params[payment_method]', 'class = "inputbox" size = "1"', 'id', 'name', $params['payment_method'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Предмет расчёта
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', $data['payment_object_list'], 'pm_params[payment_object]', 'class = "inputbox" size = "1"', 'id', 'name', $params['payment_object'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Предмет расчёта на доставку
   </td>
   <td>
    <?php
    echo JHTML::_('select.genericlist', $data['payment_object_list'], 'pm_params[payment_object_delivery]', 'class = "inputbox" size = "1"', 'id', 'name', $params['payment_object_delivery'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Статус оформленного заказа
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status'] );
     echo " ".JHTML::tooltip("Статус заказа после его оформления.");
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Статус успешной оплаты
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );
     echo " ".JHTML::tooltip("Выберите статус заказа, который будет установлен, если транзакция прошла успешно.");
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Статус возврата
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_refund_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_refund_status'] );
     echo " ".JHTML::tooltip("Будет осуществлён возврат средств покупателю при смене статуса заказа на указанный.");
     ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     Предавторизация
   </td>
   <td>
    <?php
    $options = array(
      array('id' => '0', 'name' => 'Нет'),
      array('id' => '1', 'name' => 'Да'),
    );
    echo JHTML::_('select.genericlist', $options, 'pm_params[preauth]', 'class = "inputbox" size = "1"', 'id', 'name', $params['preauth'] );
    ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     Статус для подтверждения оплаты
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_capture_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_capture_status'] );
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">Отображать определённые способы оплаты</td>
   <td><input id="modulbank_pm_checkbox" type="checkbox" name="pm_params[pm_checkbox]" value="1" <?php if ( $params['pm_checkbox'] == 1): ?>
     checked
   <?php endif ?>><?echo " ".JHTML::tooltip("Для отображения отдельных методов оплаты установите галочку и выберите интересующие из списка.");?></td>
 </tr>
<tr id="show_payment_methods_block" style="display:none">
   <td class="key">Отображаемые варианты оплаты</td>
   <td><?php
    $options = array(
      array('id' => 'card', 'name' => 'Картой'),
      array('id' => 'sbp', 'name' => 'Система Быстрых Платежей'),
      array('id' => 'googlepay', 'name' => 'GooglePay'),
      array('id' => 'applepay', 'name' => 'ApplePay'),
    );
    foreach($options as $option) {
      $checked = ($params[$option['id']] == 1)?'checked':'';
      ?>
      <input type="checkbox" name="pm_params[<?php echo $option['id']?>]" value="1" <?php echo $checked;?>>&nbsp;<?php echo $option['name']?><br>
      <?php
    }
    ?></td>
 </tr>
 <tr>
   <td  class="key">
     Логирование
   </td>
   <td>
    <?php
    $options = array(
      array('id' => '0', 'name' => 'Нет'),
      array('id' => '1', 'name' => 'Да'),
    );
    echo JHTML::_('select.genericlist', $options, 'pm_params[logging]', 'class = "inputbox" size = "1"', 'id', 'name', $params['logging'] );
    ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     Ограничение размеров лога (Mb)
   </td>
   <td>
    <input type = "text" class = "inputbox" name = "pm_params[log_size_limit]" size="45" value = "<?php echo $params['log_size_limit']?>" />
    <br>
    <a href="<?php echo JRoute::_('index.php?download_modulbank_logs=1')?>">Скачать логи</a>
   </td>
 </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>
<script>
  jQuery(document).ready(function(){
    var checkbox = jQuery('#modulbank_pm_checkbox');
    var block = jQuery('#show_payment_methods_block');
    if (checkbox.attr('checked')) {
      block.show();
    }
    checkbox.change(function(){
      if (this.checked) {
        block.show();
      } else {
        block.hide();
      }
    });
  });
</script>
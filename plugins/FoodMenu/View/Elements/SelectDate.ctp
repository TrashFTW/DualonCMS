<div id="foodMenuHeader" style="padding: 10px; width:100%;">
<?php
 echo '<h1 style="align:left;">'.__('Bill of Fare').'</h1>';
 echo '<p>';
 echo $this->Form->create('selectdate', array('url' => array('plugin' => 'FoodMenu', 'controller' => 'View', 'action' => 'selectDate')));
 if(isset($url) && $url != '') {
 	 echo $this->Form->hidden('refererurl', array('value' => $url));
 }

 echo $this->Html->link((__('today')), '#', array('onClick' => 'setToday()', 'id' => 'today'));
 echo '   <input id="datepicker" value="' . (__('yyyy-mm-dd')) . '" onchange="document.forms[\'selectdateSelectDateForm\'].submit()" type="text" size="10"/>   ';     
 echo $this->Html->link((__('tomorrow')), '#', array('onClick' => 'setTomorrow()', 'id' => 'tomorrow'));
 echo '</p>';
 echo $this->Html->scriptBlock('$(function() {$( "#datepicker" ).datepicker();});',array('inline'=>'true'));
 echo $this->Form->end((__('Select date')));
 ?>          
</div>
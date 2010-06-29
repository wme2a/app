<div class="wsUsers form">
<?php echo $this->Form->create('WsUser');?>
	<fieldset>
 		<legend><?php __('Add Ws User'); ?></legend>
	<?php
		echo $this->Form->input('user_label');
		echo $this->Form->input('api_key');
		echo $this->Form->input('privileges');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ws Users', true), array('action' => 'index'));?></li>
	</ul>
</div>
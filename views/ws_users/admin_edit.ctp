<div class="wsUsers form">
<?php echo $this->Form->create('WsUser');?>
	<fieldset>
 		<legend><?php __('Admin Edit Ws User'); ?></legend>
	<?php
		echo $this->Form->input('id');
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

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('WsUser.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('WsUser.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Ws Users', true), array('action' => 'index'));?></li>
	</ul>
</div>
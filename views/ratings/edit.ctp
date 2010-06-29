<div class="ratings form">
<?php echo $this->Form->create('Rating');?>
	<fieldset>
 		<legend><?php __('Edit Rating'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('photo_id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('value');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Rating.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Rating.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Ratings', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Photos', true), array('controller' => 'photos', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Photo', true), array('controller' => 'photos', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
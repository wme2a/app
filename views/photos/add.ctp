<div class="photos form">
<?php echo $this->Form->create('Photo',array('type' => 'file'));?>
	<fieldset>
 		<legend><?php __('Add Photo'); ?></legend>
	<?php
		echo $this->Form->file('Photo.img');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>

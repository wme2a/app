<div class="wsUsers view">
<h2><?php  __('Ws User');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $wsUser['WsUser']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User Label'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $wsUser['WsUser']['user_label']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Api Key'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $wsUser['WsUser']['api_key']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Privileges'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $wsUser['WsUser']['privileges']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ws User', true), array('action' => 'edit', $wsUser['WsUser']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Ws User', true), array('action' => 'delete', $wsUser['WsUser']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $wsUser['WsUser']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Ws Users', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ws User', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
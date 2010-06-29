<div class="photos index">
	<h2><?php __('Photos');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('width');?></th>
			<th><?php echo $this->Paginator->sort('height');?></th>
			<th><?php echo $this->Paginator->sort('geo_lat');?></th>
			<th><?php echo $this->Paginator->sort('geo_long');?></th>
			<th><?php echo $this->Paginator->sort('aperture');?></th>
			<th><?php echo $this->Paginator->sort('exposuretime');?></th>
			<th><?php echo $this->Paginator->sort('focallength');?></th>
			<th><?php echo $this->Paginator->sort('views');?></th>
			<th><?php echo $this->Paginator->sort('original_filename');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('user_name');?></th>
			<th><?php echo $this->Paginator->sort('upload_complete');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($photos as $photo):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $photo['Photo']['id']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['created']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['title']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['description']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['width']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['height']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['geo_lat']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['geo_long']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['aperture']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['exposuretime']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['focallength']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['views']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['original_filename']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($photo['User']['id'], array('controller' => 'users', 'action' => 'view', $photo['User']['id'])); ?>
		</td>
		<td><?php echo $photo['Photo']['user_name']; ?>&nbsp;</td>
		<td><?php echo $photo['Photo']['upload_complete']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $photo['Photo']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $photo['Photo']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $photo['Photo']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $photo['Photo']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Photo', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Comments', true), array('controller' => 'comments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Comment', true), array('controller' => 'comments', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Ratings', true), array('controller' => 'ratings', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Rating', true), array('controller' => 'ratings', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tags', true), array('controller' => 'tags', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tag', true), array('controller' => 'tags', 'action' => 'add')); ?> </li>
	</ul>
</div>
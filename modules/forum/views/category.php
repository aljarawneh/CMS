<ul class="breadcrumbs">
	<li><a href="<?php echo $this->router->path(array('module' => 'forum')); ?>"><?php echo __('Forum'); ?></a></li>
	<li><a href="<?php echo $this->router->path(array('module' => 'forum')); ?>#board-<?php echo $category['board_id']; ?>"><?php echo $category['board']; ?></a></li>
	<li><strong><?php echo $category['title']; ?></strong></li>
</ul>
<?php opentable(__('Forum')); ?>
		<table class="forum">
			<thead>
				<tr>
					<th class="col-6 align-left"><?php echo __('Thread title'); ?></th>
					<th class="col-2"><?php echo __('Author'); ?></th>
					<th class="col-1"><?php echo __('Replies'); ?></th>
					<th class="col-3"><?php echo __('Last entry'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($threads): ?>
				<?php foreach ($threads as $thread): ?>
				<tr>
					<td>
						<a href="<?php echo $this->router->path(array('module' => 'forum', 'controller' => 'thread', $thread['id'])); ?>" class="text-title"><?php echo $thread['title']; ?></a>
					</td>
					<td class="align-center"><?php echo HELP::profileLink($thread['username'], $thread['user_id']); ?></td>
					<td class="align-center"><?php echo $thread['entries']; ?></td>
					<td>-</td>
				</tr>
				<?php endforeach; ?>
				<?php else: ?>
				<tr>
					<td colspan="4"><?php echo __('In this category has not been created any thread'); ?>.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
<?php closetable(); ?>
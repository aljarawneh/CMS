<?php opentable(__('Forum')); ?>
		<?php if ($boards = $board->fetchAll()): ?>
		<?php foreach ($boards as $board): ?>
		<table class="forum" id="board-<?php echo $board['id']; ?>">
			<thead>
				<tr>
					<th class="col-8 align-left"><?php echo $board['title']; ?></th>
					<th class="col-1"><?php echo __('Threads'); ?></th>
					<th class="col-1"><?php echo __('Entries'); ?></th>
					<th class="col-3"><?php echo __('Last entry'); ?></th>
				</tr>
			</thead>
			<?php if ($categories = $category->fetchByID($board['id'])): ?>
			<tbody>
				<?php foreach ($categories as $category): ?>
				<tr>
					<td>
						<a href="<?php echo $this->router->path(array('module' => 'forum', 'controller' => 'category', $category['id'])); ?>" class="text-title"><?php echo $category['title']; ?></a>
						<p><?php echo $category['description']; ?></p>
					</td>
					<td class="align-center"><?php echo $category['threads']; ?></td>
					<td class="align-center"><?php echo $category['entries']; ?></td>
					<td>-</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<?php endif; ?>
		</table>
		<?php endforeach; ?>
		<?php endif; ?>
<?php closetable(); ?>
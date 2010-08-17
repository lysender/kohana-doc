<!-- Article tree - from index to the current page -->
<?php if ( ! empty($sidebar_info['topic_hierarchy'])): ?>
<ul id="topic_hierarchy">
<?php foreach ($sidebar_info['topic_hierarchy'] as $node): ?>
	<li><?php echo HTML::anchor($node['link'], $node['title']) ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>

<!-- Related topics -->
<?php if( ! empty($sidebar_info['topic_group'])): ?>
<ul id="topic_group">
<?php foreach ($sidebar_info['topic_group'] as $node): ?>
	<li<?php echo ($node['currently_viewed']) ? ' class="currently-viewed"' : '' ?>><?php echo HTML::anchor($node['link'], $node['title']) ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>

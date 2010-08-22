<!-- Topics -->
<?php if (isset($topics) AND !empty($topics)): ?>
<div class="doc-topics">
	<h2>Topics</h2>
	<ul>
	<?php foreach ($topics as $key => $node): ?>
		<li><?php echo HTML::anchor($node['link'], $node['title']) ?></li>
	<?php endforeach ?>
	</ul>
</div>
<?php endif ?>

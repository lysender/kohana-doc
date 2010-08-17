<!-- Topics -->
<?php if (isset($topics) AND !empty($topics)): ?>
<div class="doc-topics">
	<h2>Topics</h2>
	<ul>
	<?php foreach ($topics as $link => $title): ?>
		<li><?php echo HTML::anchor($link, $title) ?></li>
	<?php endforeach ?>
	</ul>
</div>
<?php endif ?>

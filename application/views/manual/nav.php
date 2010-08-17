<!-- Basic navigation -->
<?php if (isset($basic_nav) && !empty($basic_nav)): ?>
<div class="basic-nav">
<?php foreach ($basic_nav as $key => $node): ?>
	<span class="<?php echo $key ?>"><?php echo HTML::anchor($basic_nav[$key]['link'], $basic_nav[$key]['title']) ?></span>
<?php endforeach ?>
</div>
<?php endif ?>

<!-- Basic navigation -->
<?php if (isset($basic_nav) && !empty($basic_nav)): ?>
<div class="basic-nav span-17 last">
	<div class="prev span-8">
	<?php if (!empty($basic_nav['prev'])): ?>
		<?php echo HTML::anchor($basic_nav['prev']['link'], $basic_nav['prev']['title']) ?>
	<?php endif ?>
	</div>
	<div class="next span-8 last">
	<?php if (!empty($basic_nav['next'])): ?>
		<?php echo HTML::anchor($basic_nav['next']['link'], $basic_nav['next']['title']) ?>
	<?php endif ?>
	</div>
</div>
<?php endif ?>

<!-- Basic navigation -->
<?php if (isset($basic_nav) && !empty($basic_nav)): ?>
<div class="basic-nav span-17 last clear">
	<div class="prev span-5">
	<?php if (!empty($basic_nav['prev'])): ?>
		<?php echo HTML::anchor($basic_nav['prev']['link'], $basic_nav['prev']['title']) ?>
	<?php endif ?>
	</div>

	<div class="parent span-7 center">
	<?php echo HTML::anchor($basic_nav['parent']['link'], $basic_nav['parent']['title']) ?><br />
	<?php echo HTML::anchor($basic_nav['grand_parent']['link'], $basic_nav['grand_parent']['title']) ?>
	</div>

	<div class="next span-5 last">
	<?php if (!empty($basic_nav['next'])): ?>
		<?php echo HTML::anchor($basic_nav['next']['link'], $basic_nav['next']['title']) ?>
	<?php endif ?>
	</div>
</div>
<?php endif ?>

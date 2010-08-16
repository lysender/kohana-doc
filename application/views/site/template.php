<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $title ?> | Kohana Documentation Project</title>
	
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="all" />
	
<?php if (isset($description) && $description): ?>
<meta name="description" content="<?php echo $description ?>" />
<?php endif ?>
	
<?php if (isset($keywords) && $keywords): ?>
<meta name="keywords" content="<?php echo $keywords ?>" />
<?php endif ?>

<link rel="shortcut icon" href="/favicon.ico?v=<?php echo APP_VERSION ?>" />

<!-- basic styles -->
<?php foreach ($styles as $style => $media)
	echo HTML::style($style.'?v='.APP_VERSION, array('media' => $media)), "\n" ?>
	
<!--[if IE]>
<?php echo HTML::style('media/css/ie.css?v='.APP_VERSION, array('media' => 'screen, projection')) ?>
<![endif]-->

<script type="text/javascript">
//<![CDATA[
	var base_url = '<?php echo URL::site('/') ?>';
//]]>
</script>
	
<?php if (!IN_PRODUCTION && Kohana::$profiling): ?>
<!-- Profiler Styles -->
<style type="text/css">
	<?php include Kohana::find_file('views', 'profiler/style', 'css') ?>
</style>
<?php endif ?>
</head>

<body>
<div id="header">
	<div class="container"><?php echo $header ?></div>
</div>

<div id="manual" class="container">
	<div id="content" class="span-17 suffix-1">
		<div class="basic-nav"><?php echo $basic_nav ?></div>
		<div class="doc-content"><?php echo $content ?></div>
		<div class="doc-topics"><?php echo $topics ?></div>
		<div class="basic-nav"><?php echo $basic_nav ?></div>
	</div>
	<div id="sidebar" class="span-6 last"><?php echo $sidebar ?></div>
</div>

<div id="footer">
	<div class="container"><?php echo $footer ?></div>
</div>

<!-- basic scripts -->
<?php foreach ($scripts as $script)
	echo HTML::script($script.'?v='.APP_VERSION), "\n" ?>

<script type="text/javascript">
//<![CDATA[
	<?php
		if (isset($head_scripts) && $head_scripts) {
			echo $head_scripts."\n";
		}
	?>
	$(function(){
		<?php
			if (isset($head_readyscripts) && $head_readyscripts) {
				echo $head_readyscripts."\n";
			}
		?>
	});
//]]>
</script>
</body>
</html>

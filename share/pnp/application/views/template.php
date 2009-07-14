<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="refresh" content="<?php echo $this->config->conf['refresh'] ?>"; URL="<?php echo $_SERVER['REQUEST_URI'] ?>">
		<title><?php if (isset($this->title)) echo html::specialchars($this->title) ?></title>
		<?php echo html::stylesheet('media/css/common.css') ?>
		<?php echo html::stylesheet('media/css/autocomplete.css') ?>
		<?php echo html::stylesheet('media/css/ui-'.$this->config->conf['ui-theme'].'/jquery-ui.css') ?>
		<?php echo html::link('media/images/favicon.ico','icon','image/ico') ?>
		<?php echo html::script('media/js/jquery-min.js')?>
		<?php echo html::script('media/js/jquery-ui.min.js')?>
		<?php echo html::script('media/js/jquery.autocomplete.min.js')?>
		<?php
			if (!empty($css_header)) {
				echo $css_header;
			}
		?>
	</head>

<body>
<?php if (!empty($graph)) {
     echo $graph;
} ?>
<?php if (!empty($debug)) {
     echo $debug;
} ?>
</body>
</html>

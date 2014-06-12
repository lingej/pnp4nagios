<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(isset ( $_SERVER['REQUEST_URI'])):?> 
<meta http-equiv="refresh" content="60">
<?php endif ?>
<title><?php echo $error ?></title>
<?php echo html::stylesheet('media/css/common.css') ?>
<?php echo html::stylesheet('media/css/ui-'.Kohana::config('core.theme').'/jquery-ui.css') ?>
<?php echo html::link('media/images/favicon.ico','icon','image/ico') ?>
<?php echo html::script('media/js/jquery-min.js')?>
<?php echo html::script('media/js/jquery-ui.min.js')?>
<style type="text/css">
<?php #include Kohana::find_file('views', 'kohana_errors', FALSE, 'css') ?>
</style>
</head>
<body>
<div class="pagebody">
<table class="body">
<tr valign="top"><td>
<div class="left ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo "PNP4Nagios Version ".PNP_VERSION ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" style="width: 640px">
<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all">

<h3>Please check the documentation for information about the following error.</h3>
<p><?php echo html::specialchars($message) ?></p>
<?php if ( ! empty($line) AND ! empty($file)): ?>
<h3>file [line]:</h3>
<p><?php echo Kohana::lang('core.error_file_line', $file, $line) ?></p>
<?php endif ?>
<?php if ( ! empty($trace)): ?>
<h3><?php echo Kohana::lang('core.stack_trace') ?></h3>
<?php echo $trace ?>
<?php endif ?>
<p>
<a href="javascript:history.back()"><?php echo Kohana::lang('common.back') ?></a>

</div>
</div>
</td><td>
<div class="right">

<div class="ui-widget">

<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>

<div class="p4 ui-widget-content ui-corner-bottom" >
<?php
echo "<a title=\"".Kohana::lang('common.back')."\" href=\"javascript:history.back()\"><img class=\"icon\" src=\"".url::base()."media/images/back.png\"></a>\n";
echo "<a title=\"".Kohana::lang('common.title-home-link')."\" href=\"".url::base(TRUE)."graph\"><img class=\"icon\" src=\"".url::base()."media/images/home.png\"></a>\n";
echo "<a title=\"".Kohana::lang('common.title-docs-link')."\" href=\"".url::base(TRUE)."docs\"><img class=\"icon\" src=\"".url::base()."media/images/docs.png\"></a>\n";
?>
</div>

</div>

</div>
</td></tr>
<tr valign="top"><td colspan="2">
<div class="left">
<div class="cb p4 ui-widget-content ui-corner-all">
<?php echo pnp::print_version(); ?>
</div>
</div>
</div>
</td></tr></table>
</div>

</body>
</html>

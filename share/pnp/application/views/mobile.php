<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes" />
<?php echo html::link('media/images/home.png', 'apple-touch-icon', "") ?>
<?php echo html::stylesheet('media/css/jquery.mobile.min.css') ?>
<?php echo html::stylesheet('media/css/mobile.css') ?>
<?php echo html::script('media/js/jquery-min.js')?>
<?php echo html::script('media/js/jquery.mobile.min.js')?>
</head> 
<body> 

<div data-role="page" data-theme="b" data-add-back-btn="true">
<div data-role="header">
<h1>PNP4Nagios</h1>
<a href="<?php echo url::base(TRUE)?>/mobile" data-icon="home" class="ui-btn-right">Home</a>
</div><!-- /header -->
<?php if (!empty($home)) {
     echo $home;
} ?>
<?php if (!empty($about)) {
     echo $about;
} ?>
<?php if (!empty($overview)) {
     echo $overview;
} ?>
<?php if (!empty($host)) {
     echo $host;
} ?>
<?php if (!empty($graph)) {
     echo $graph;
} ?>
<?php if (!empty($search)) {
     echo $search;
} ?>
<?php if (!empty($query)) {
     echo $query;
} ?>
<?php if (!empty($pages)){ 
   	echo $pages;
} ?>
<?php if (!empty($special)){ 
   	echo $special;
} ?>
<div data-role="footer">
</div><!-- /footer -->
</div><!-- /page -->
</body>
</html>

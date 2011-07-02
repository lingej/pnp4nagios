<?php
if($this->is_authorized == FALSE){
?>
<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="e">
<li><strong>Alert:&nbsp;</strong><?php echo Kohana::lang('error.not_authorized')?></li>
</ul>
</div><!-- /content -->
<?php
return;
}
?>

<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
<?php
foreach($services as $key=>$service){
	if($key == 0)
		printf("<li data-role=\"list-divider\">%s</li>\n", $service['hostname'] );
		
 	 printf("<li><a href=\"".url::base(TRUE)."mobile/graph/%s/%s\" data-transition=\"pop\"><img src=\"".url::base(TRUE)."image?host=%s&srv=%s&h=80&w=80&view=1\">%s</a></li>",
		urlencode($service['hostname']),
	 	urlencode($service['name']),
	 	urlencode($service['hostname']),
	 	urlencode($service['name']),
	 	$service['servicedesc']);
}
?>
</ul>
</div><!-- /content -->

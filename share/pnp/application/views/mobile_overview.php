<div data-role="content">
<ul data-filter="true" data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
<?php 
$l = '';
foreach($hosts as $host){
	if( substr($host['name'], 0, 1) != $l ){
		printf("<li data-role=\"list-divider\">%s</li>\n", strtoupper(substr($host['name'], 0, 1)) );
	}
    printf("<li><a href=\"".url::base(TRUE)."mobile/host/%s\" data-transition=\"pop\">%s</a></li>", $host['name'], $host['name']);
	$l = substr($host['name'], 0, 1);
}
?>
</ul>
</div><!-- /content -->

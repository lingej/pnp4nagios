<div data-role="content">
<ul data-role="listview" data-filter="true" data-inset="true" data-theme="c" data-dividertheme="a">
<?php
foreach($templates as $template){
  	printf("<li><a href=\"".url::base(TRUE)."mobile/special/%s\" data-transition=\"pop\">%s</a></li>", $template, $template);
}
?>
</ul>
</div><!-- /content -->

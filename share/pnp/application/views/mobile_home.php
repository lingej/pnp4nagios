<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
<li><a href="<?php echo url::base(TRUE)?>mobile/overview" data-transition="pop">All Hosts</a></li>
<li><a href="<?php echo url::base(TRUE)?>mobile/search" data-transition="pop">Search Hosts</a></li>
<?php
if($this->data->getFirstPage() && $this->isAuthorizedFor('pages') ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/pages\" data-transition=\"pop\">Pages</a></li>"; 
}

if($this->data->getFirstSpecialTemplate() ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/special\" data-transition=\"pop\">Special Templates</a></li>"; 
}
?>
<li><a href="<?php echo url::base(TRUE)?>mobile/about" data-transition="pop">About</a></li>
<li><a href="<?php echo url::base(TRUE)?>mobile/goto/classic" data-ajax="false" data-transition="pop">Classic UI</a></li>
</ul>
</div><!-- /content -->

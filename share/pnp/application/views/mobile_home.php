<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
<li><a href="<?php echo url::base(TRUE)?>mobile/overview" data-transition="slide">All Hosts</a></li>
<li><a href="<?php echo url::base(TRUE)?>mobile/search" data-transition="slide">Search Hosts</a></li>
<?php
if($this->data->getFirstPage() && $this->isAuthorizedFor('pages') ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/pages\" data-transition=\"slide\">Pages</a></li>"; 
}

if($this->data->getFirstSpecialTemplate() ){
    echo "<li><a href=\"".url::base(TRUE)."mobile/special\" data-transition=\"slide\">Special Templates</a></li>"; 
}
?>
<li><a href="<?php echo url::base(TRUE)?>mobile/about" data-transition="slide">About</a></li>
</ul>
</div><!-- /content -->

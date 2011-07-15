<?php if( $this->isAuthorizedFor('host_search') ){ ?>
<!-- Search Box Start -->
<div data-role="content">

<div data-role="fieldcontain">
<form action="search" method="post"> 
    <input type="search" name="term" id="search" value="" />
	<button type="submit"><?php echo Kohana::lang('common.mobile-submit')?></button>
</form>
</div>

</div>
<!-- Search Box End -->
<?php } ?>

<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="a">
<?php
foreach($this->result as $host){
    printf("<li><a href=\"".url::base(TRUE)."mobile/host/%s\" data-transition=\"pop\">%s</a></li>", $host, $host);
}
?>
</ul>
</div><!-- /content -->

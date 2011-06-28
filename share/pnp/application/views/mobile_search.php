<?php if( $this->isAuthorizedFor('host_search') ){ ?>
<!-- Search Box Start -->
<div data-role="content">

<div data-role="fieldcontain">
<form action="search" method="post"> 
    <input type="search" name="term" id="search" value="" />
	<button type="submit">Submit</button>
</form>
</div>

</div>
<!-- Search Box End -->
<?php } ?>

<div data-role="content">
<ul data-role="listview" data-inset="false" data-theme="c" data-dividertheme="a">
<?php
foreach($this->result as $host){
    printf('<li><a href="/pnp4nagios/mobile/host/%s" data-transition="slide">%s</a></li>', $host, $host);
}

?>
</ul>
</div><!-- /content -->

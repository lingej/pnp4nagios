<?php if (!empty($services) && $this->isAuthorizedFor('service_links') ) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.service-box-header') ?> 
 </div>
<div class="p4 ui-widget-content ui-corner-bottom">
<?php
foreach($services as $service){
	$path = pnp::addToUri( array('host' => $host, 'srv' => $service['name']) );
	echo pnp::add_to_basket_icon($host,
                $service['name']);

	echo "<a href=\"".$path."\" class=\"multi".$service['is_multi']. " " . $service['state'].
		 "\" title=\"".$service['servicedesc']. 
		 "\">";
	echo pnp::shorten($service['servicedesc']).
		 "</a><br>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

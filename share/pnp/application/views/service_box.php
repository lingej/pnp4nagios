<?php if (!empty($services) && $this->isAuthorizedFor('service_links') ) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.service-box-header') ?> 
 </div>

<?php
    $filter = $this->session->get('sfilter');
?>

<div class="p4 ui-widget-content">
	<?php
		echo "<input type=\"text\" name=\"service-filter\" id=\"service-filter\" value=\"".$filter."\" class=\"textbox\" />"	
	?>
</div>

<div class="p4 ui-widget-content ui-corner-bottom" id="services">
<?php
foreach($services as $service){
	echo "<span id=\"service-".$service['servicedesc']."\">\n";
	$path = pnp::addToUri( array('host' => $host, 'srv' => $service['name']) );
	echo pnp::add_to_basket_icon($host,
                $service['name']);

	echo "<a href=\"".$path."\" class=\"multi".$service['is_multi']. " " . $service['state'].
		 "\" title=\"".$service['servicedesc']. 
		 "\">";
	echo pnp::shorten($service['servicedesc']).
		 "</a><br>\n";
	echo "</span>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

<?php if (!empty($services)) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 Services
 </div>
<div class="p4 ui-widget-content ui-corner-bottom">
<?php
foreach($services as $service){
	echo "<a href=\"".$this->uri->string()."?host=".$host."&srv=".$service['name']."\" class=\"multi".$service['is_multi']."\">".pnp::shorten($service['servicedesc'])."</a><br>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

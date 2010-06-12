<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.multisite-box-header') ?>
 </div>
 <div class="p4 ui-widget-content ui-corner-bottom">
<?php 
if(isset($host)){
    echo "<strong>Host: </strong><a href=".pnp::multisite_link($base_url,$site,$host).">".html::specialchars(pnp::shorten($host))."</a><br>\n";
}
if(isset($service) && $service != "Host Perfdata"){
    echo "<strong>Service: </strong><a href=".pnp::multisite_link($base_url,$site,$host, $service).">".html::specialchars(pnp::shorten($service))."</a>\n"; 
}
?>
 </div>
</div>
<p>


<?php 
echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo Kohana::lang('common.timerange-box-header')."\n"; 
echo "</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";
$start = $this->session->get('start','');
$end   = $this->session->get('end','');
$path  = pnp::addToUri(array('start' => $start,'end' => $end));
if($start && $end){
	echo "<a class=\"multi0\" href=\"".$path."\">".Kohana::lang('common.timerange-selector-link')."</a><br>\n"; 
}
if($start && !$end){
	echo "<a class=\"multi0\" href=\"".$path."\">".Kohana::lang('common.timerange-selector-link')."</a><br>\n"; 
}

$path  = pnp::addToUri(array('view' => '', 'start' => '', 'end' => ''));
echo "<a class=\"multi0\" href=\"".$path."\">".Kohana::lang('common.timerange-selector-overview')."</a><br>\n"; 

foreach($this->config->views as $key=>$view){
	$path = pnp::addToUri(array('view' => $key, 'start' => '', 'end' => ''));
	echo "<a class=\"multi0\" href=\"".$path."\">".$view['title']."</a><br>\n"; 
}
echo "</div>\n";
echo "</div><p>\n";
?>

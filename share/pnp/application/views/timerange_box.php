<?php 
echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo Kohana::lang('common.timerange-box-header')."\n"; 
echo "</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";

foreach($this->config->views as $key=>$view){
	echo "<a class=\"multi0\" href=\"".$this->url."&view=".$key."\">".$view['title']."</a><br>\n"; 
}
echo "</div>\n";
echo "</div><p>\n";
?>

<?php 
echo "<div class=\"ui-widget\">\n";
echo "<div class=\"ui-widget-header ui-corner-top\">\n";
echo "Timeranges"; 
echo "</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";

foreach($this->config->views as $key=>$view){
	echo "<a href=\"".$this->url."&view=".$key."\">".$view['title']."</a><br>\n"; 
}
echo "</div>\n";
echo "</div><p>\n";
?>

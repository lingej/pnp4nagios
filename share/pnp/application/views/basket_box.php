<?php 
$basket = $this->session->get('basket');

echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo "Basket Box</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";
echo "<div class=basket_actions>\n";
echo "<span id=\"basket_action_add\"><a id=\"".$this->host."::".$this->service."\">add</a></span>\n";
echo "::<span id=\"basket_action_delall\"><a id=\"basket_action_delall\">del all</a></span>\n";
echo "</div>\n";
echo '<div id="basket_items">items';
if(is_array($basket)){
	foreach($basket as $key=>$item){
		echo "<span id=\"$item\">$item<a id=\"$item\" onClick=\"return false;\">::del</a></span>\n";
	}
}
echo "</div>\n";
echo "</div>\n";
?>

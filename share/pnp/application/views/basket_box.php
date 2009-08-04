<?php 
$basket = $this->session->get('basket');

echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo "Basket Box</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";
echo '<div id="basket_items">';
if(sizeof($basket) > 0 ){
	foreach($basket as $key=>$item){
		echo "<span id=\"basket_action_remove\"><a title=\"Remove Item\" id=\"".$item."\"><img width=12px height=12px src=\"media/images/remove.png\"></a>".$item."</span><br>\n";
	}
}
if(sizeof($basket) > 0 ){
	echo "<div><a href=\"".url::base()."page/basket\">show basket</a></div>\n";
}else{
	echo "<div>Basket is empty</div>\n";
}
echo "</div>\n";
echo "</div><br>\n";
?>
<div id="basket_box"></div>

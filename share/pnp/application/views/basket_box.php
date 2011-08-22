<?php 
$basket = $this->session->get('basket');

echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo Kohana::lang('common.basket-box-header')."</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";
echo "<div id=\"basket_items\">\n";
if(is_array($basket) && sizeof($basket) > 0 ){
	foreach($basket as $key=>$item){
		echo "<li class=\"ui-state-default basket_action_remove\" id=\"".
                     $item."\"><a title=\"".Kohana::lang('common.basket-remove', $item)."\"".
                     "id=\"".$item.
                     "\"><img width=12px height=12px src=\"".url::base().
                     "media/images/remove.png\"></a>".
                     pnp::shorten($item)."</li>\n";
	}
}
if(is_array($basket) && sizeof($basket) > 0 ){
    echo "<div align=\"center\" class=\"p2\">\n";
    echo "<button id=\"basket-show\">".Kohana::lang('common.basket-show')."</button>\n";
    echo "<button id=\"basket-clear\">".Kohana::lang('common.basket-clear')."</button>\n";
    echo "</div>\n";
    #echo "<div><a class=\"multi0\" href=\"".url::base(TRUE)."page/basket\">".Kohana::lang('common.basket-show')."</a></div>\n";
}else{
	echo "<div>".Kohana::lang('common.basket-empty')."</div>\n";
}
echo "</div>\n";
echo "</div>\n";
echo "</div><br>\n";
?>
<div id="basket_box"></div>

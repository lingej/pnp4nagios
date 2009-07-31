<?php 
$seen = $this->session->get('seen');
if(sizeof($seen) > 0){

	echo "<div class=\"ui-widget\">\n";
 	echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
	echo "Seen Items</div>\n";
 	echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";
	echo "<a href=\"#\" onClick=\"addFormField(); return false;\">add</a><br>\n";
	foreach($seen as $key=>$item){
		echo "<span id=\"row".$key."\">".$item['host']."::".$item['srv']."</span><a href=# onClick=\"removeItem('row".$key."'); return false;\">rm</a><br>";
	}
	echo "</div>\n";
}
?>

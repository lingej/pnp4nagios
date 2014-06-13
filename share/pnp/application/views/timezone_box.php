<?php 

// Maintain dropdown selection on reload
function urlSelected($query_val) {
	if (strpos($_SERVER['REQUEST_URI'], "timezone=".urlencode($query_val))) {
		return "selected"; 
	}
	return "";
}

// Reload page for selection
echo "<script>\n";
echo "function updateUrl() {\n";
    echo "index = document.getElementById(\"timezones\").selectedIndex;";
    echo "options = document.getElementById(\"timezones\").options;";
    echo "url = options[index].value;";
    echo "if (url != \"\") {";
        echo "window.location.href= url;";
    echo "}";
echo "}";
echo "</script>\n";

// Box headings
echo "<div class=\"ui-widget\">\n";
echo "<div class=\"p2 ui-widget-header ui-corner-top\">\n";
echo Kohana::lang('common.timezone-box-header')."\n"; 
echo "</div>\n";
echo "<div class=\"p4 ui-widget-content ui-corner-bottom\">\n";

// Dropdown
echo "<select id=\"timezones\" onchange=\"updateUrl()\">\n";

	echo "<option value=\"\" selected>Select a timezone</option>\n";

foreach($this->config->zones as $key=>$zone) {
	$path = pnp::addToUri(array('timezone' => $zone['tz']));
	$selected_val = urlSelected($zone['tz']);
	echo "<option value=\"".$path."\" ".$selected_val.">".$zone['title']."</option>\n";
}

echo "</select>";

echo "</div>\n";
echo "</div><p>\n";


?>
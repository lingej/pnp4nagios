<!-- Graph Content Start-->
<?php
if (!empty($timerange_select)) {
	echo $timerange_select;
}
if (!empty($widget_graph)) {
	echo $widget_graph;
}
echo "<div class=\"ui-widget\" style=\"min-width:".$graph_width."px\">\n";
$count = 0;
foreach($this->data->STRUCT as $key=>$value){ 
    if($value['LEVEL'] == 0){
		echo "<strong>Special Template</strong><p>\n";
		echo "<strong>".$value['TIMERANGE']['title']. "</strong> " .$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "\n";
		$count = 0;
	}

    echo "<div class=\"ui-widget-header ui-corner-top\">";
    echo "<table border=0 width=100%><tr>\n";
    echo "<td width=100% align=left>";
	echo Kohana::lang('common.datasource',$value['ds_name'])."</td>\n";
	echo "<td align=right>";
	echo nagios::SummaryLink($value['MACRO']['DISP_HOSTNAME'],
		$value['TIMERANGE']['start'],
		$value['TIMERANGE']['end'])."</td>\n";
	echo "<td align=right>";
	echo nagios::AvailLink($value['MACRO']['DISP_HOSTNAME'],
		$value['MACRO']['DISP_SERVICEDESC'],
		$value['TIMERANGE']['start'],
		$value['TIMERANGE']['end'])."</td>\n";
	echo "<td align=right>";
	echo pnp::add_to_basket_icon($value['MACRO']['HOSTNAME'],
		$value['MACRO']['SERVICEDESC'])."</td>\n";
	echo "<td align=right>";
	echo pnp::zoom_icon($value['MACRO']['HOSTNAME'],
		$value['MACRO']['SERVICEDESC'],
		$value['TIMERANGE']['start'],
		$value['TIMERANGE']['end'],
		$value['SOURCE'],
		$value['VIEW'])."</td>\n";
    echo "</tr></table>\n";
    echo "</div>\n";
    echo "<div class=\"p4 gh ui-widget-content ui-corner-bottom\">\n";
    echo "<a href=\"".url::base(TRUE)."graph?host=" 
		.$value['MACRO']['HOSTNAME'] . "&srv=" 
		.$value['MACRO']['SERVICEDESC'] ."\" title=\""
		.Kohana::lang('common.host',$value['MACRO']['DISP_HOSTNAME']) . " "
		.Kohana::lang('common.service',$value['MACRO']['DISP_SERVICEDESC']) . " " 
		.Kohana::lang('common.datasource',$value['ds_name']) . " " 
		."\">\n";
	echo "<img src=\"".url::base(TRUE)."image_special?tpl=" . $this->tpl . "&view=" . $value['VIEW'] . "&source=" . $value['SOURCE'] . "&start=" . $value['TIMERANGE']['start'] ."&end=" . $value['TIMERANGE']['end'] . "\"></a>\n";
    echo "</div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

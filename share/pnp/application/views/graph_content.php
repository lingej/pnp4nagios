<!-- Graph Content Start-->
<?php
if (!empty($timerange_select)) {
	echo $timerange_select;
}
echo "<div class=\"gw ui-widget\">\n";
$count = 0;
foreach($this->data->STRUCT as $key=>$value){ 
    if($value['LEVEL'] == 0){
		echo "<h3>".$value['TIMERANGE']['title']. " " .$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "</h3>\n";
		$count = 0;
	}
	if($value['VERSION'] != "valid" && $count == 0){
		$count++;
		echo "<div class=\"ui-widget\">\n";
		echo "<div class=\"ui-state-error ui-corner-all\">\n";
		echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left;\"></span>".$value['VERSION']."</p>\n";
		echo "</div>\n";
		echo "</div><br>\n";
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
    echo "<a href=\"".url::base()."graph?host=" 
		.$value['MACRO']['HOSTNAME'] . "&srv=" 
		.$value['MACRO']['SERVICEDESC'] ."\" title=\""
		.Kohana::lang('common.host',$value['MACRO']['DISP_HOSTNAME']) . " "
		.Kohana::lang('common.service',$value['MACRO']['DISP_SERVICEDESC']) . " " 
		.Kohana::lang('common.datasource',$value['ds_name']) . " " 
		."\">\n";
	echo "<img src=\"".url::base()."image?host=" . $value['MACRO']['HOSTNAME'] . "&srv=" . $value['MACRO']['SERVICEDESC'] . "&view=" . $value['VIEW'] . "&source=" . $value['SOURCE'] . "&start=" . $value['TIMERANGE']['start'] ."&end=" . $value['TIMERANGE']['end'] . "\"></a>\n";
    echo "</div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

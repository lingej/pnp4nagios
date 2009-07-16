<!-- Graph Content Start-->
<?php
echo "<div class=\"gw ui-widget\">\n";
$count = 0;
foreach($this->data->STRUCT as $key=>$value){ 
    if($value['LEVEL'] == 0){
		echo "<h3>".$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "</h3>\n";
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
    echo "<td align=left>".Kohana::lang('common.datasource',$value['ds_name'])."</td><td align=right>\n";
	echo html::image('media/images/PDF_16.png');
	echo html::image('media/images/XML_16.png');
    echo "</td></tr></table>\n";
    echo "</div>\n";
    echo "<div class=\"p4 gh ui-widget-content ui-corner-bottom\">\n";
    echo "<a href=\"graph?host=" . $value['MACRO']['HOSTNAME'] . "&srv=".$value['MACRO']['SERVICEDESC'] ."\">\n";
	echo "<img src=\"image?host=" . $value['MACRO']['HOSTNAME'] . "&srv=" . $value['MACRO']['SERVICEDESC'] . "&view=" . $value['VIEW'] . "&source=" . $value['SOURCE'] . "&start=" . $value['TIMERANGE']['start'] ."&end=" . $value['TIMERANGE']['end'] . "\"></a>\n";
    echo "</div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

<!-- Graph Content Start-->
<?php
if (!empty($timerange_select)) {
	echo $timerange_select;
}
if (!empty($widget_graph)) {
	echo $widget_graph;
}
echo "<div class=\"ui-widget\" style=\"min-width:".$graph_width."px\">\n";

if($this->is_authorized == FALSE){
    echo "<div style=\"padding: 0pt 0.7em;\" class=\"ui-state-error ui-corner-all\">\n"; 
    echo "<p><span style=\"float: left; margin-right: 0.3em;\" class=\"ui-icon ui-icon-alert\"></span>\n"; 
    echo "<strong>Alert:&nbsp;</strong>".Kohana::lang('error.not_authorized')."</p>\n";
    echo "</div></div>\n";
    return;
}

if($this->data->ERROR != NULL){
    echo "<div style=\"padding: 0pt 0.7em;\" class=\"ui-state-error ui-corner-all\">\n"; 
    echo "<p><span style=\"float: left; margin-right: 0.3em;\" class=\"ui-icon ui-icon-alert\"></span>\n"; 
    echo "<strong>Alert:&nbsp;</strong>".$this->data->ERROR."</p>\n";
    echo "</div></div>\n";
    return;
}
$count = 0;
foreach($this->data->STRUCT as $key=>$value){ 
   	if($value['LEVEL'] == 0){
		echo "<strong>Host: </strong>".$value['MACRO']['DISP_HOSTNAME']. " <strong>Service: </strong>".$value['MACRO']['DISP_SERVICEDESC']."<p>\n";
		echo "<strong>".$value['TIMERANGE']['title']. "</strong> " .$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "\n";
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
	echo pnp::add_to_basket_icon(
                $value['MACRO']['HOSTNAME'],
		$value['MACRO']['SERVICEDESC'],
                $value['SOURCE']
             );
        echo "</td>\n";
	echo "<td align=right>";
	echo pnp::zoom_icon($value['MACRO']['HOSTNAME'],
		$value['MACRO']['SERVICEDESC'],
		$value['TIMERANGE']['start'],
		$value['TIMERANGE']['end'],
		$value['SOURCE'],
		$value['VIEW'],
		$value['GRAPH_WIDTH'],
		$value['GRAPH_HEIGHT'])."</td>\n";
	echo "</tr></table>\n";
	echo "</div>\n";
   	echo "<div class=\"p4 gh ui-widget-content ui-corner-bottom\">\n";
	echo "<div style=\"position:relative;\">\n";
        $path = pnp::addToUri( array(
                                'host'   => $value['MACRO']['HOSTNAME'],
                                'srv'    => $value['MACRO']['SERVICEDESC'],
                               ), FALSE
                             );
   	echo "<a href=\"".url::base(TRUE)."graph".$path 
		."\" title=\""
		.Kohana::lang('common.host',$value['MACRO']['DISP_HOSTNAME']) . " "
		.Kohana::lang('common.service',$value['MACRO']['DISP_SERVICEDESC']) . " " 
		.Kohana::lang('common.datasource',$value['ds_name']) . " " 
		."\">\n";
	echo "<div start=".$value['TIMERANGE']['start']." end=".$value['TIMERANGE']['end']." style=\"width:".$value['GRAPH_WIDTH']."px; height:".$value['GRAPH_HEIGHT']."px; position:absolute; top:33px\" class=\"graph\" id=\"".$this->url."\" ></div>";
        $path = pnp::addToUri( array(
                                'host'   => $value['MACRO']['HOSTNAME'],
                                'srv'    => $value['MACRO']['SERVICEDESC'],
                                'view'   => $value['VIEW'],
                                'source' => $value['SOURCE'],
                                'start'  => $value['TIMERANGE']['start'],
                                'end'    => $value['TIMERANGE']['end']
                               ), FALSE
                             );
        echo "<img class=\"graph\" src=\"".url::base(TRUE)."image" . $path . "\"></a>\n";
        echo "</div>\n";
   	echo "</div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

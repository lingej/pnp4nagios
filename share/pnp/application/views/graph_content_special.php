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
if($this->data->MACRO['TITLE'])
    echo "<strong>".$this->data->MACRO['TITLE']."</strong><p>\n";
if($this->data->MACRO['COMMENT'])
    echo $this->data->MACRO['COMMENT']."<p>\n";

foreach($this->data->STRUCT as $key=>$value){ 
    if($value['LEVEL'] == 0 ){
		echo "<strong>".$value['TIMERANGE']['title']. "</strong> " .$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "\n";
		$count = 0;
	}

    echo "<div class=\"ui-widget-header ui-corner-top\">";
    echo "<table border=0 width=100%><tr>\n";
    echo "<td width=100% align=left>";
	echo Kohana::lang('common.datasource',$value['ds_name'])."</td>\n";
	echo "<td align=right>";
    echo "</tr></table>\n";
    echo "</div>\n";
    echo "<div class=\"p4 gh ui-widget-content ui-corner-bottom\">\n";
    echo "<a href=\"".url::base(TRUE)."special?tpl=" . $this->tpl . "\">\n";
	echo "<img src=\"".url::base(TRUE)."image_special?tpl=" . $this->tpl . "&view=" . $value['VIEW'] . "&source=" . $value['SOURCE'] . "&start=" . $value['TIMERANGE']['start'] ."&end=" . $value['TIMERANGE']['end'] . "\">\n";
    echo "</a></div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

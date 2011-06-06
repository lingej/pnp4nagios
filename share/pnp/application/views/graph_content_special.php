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
    echo pnp::zoom_icon_special($this->tpl,
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
    $path = pnp::addToUri( array('tpl' => $this->tpl, 'view' => NULL ) );
    echo "<a href=\"". $path . "\">\n";
    echo "<div start=".$value['TIMERANGE']['start']." end=".$value['TIMERANGE']['end']." style=\"width:".$value['GRAPH_WIDTH']."px; height:".$value['GRAPH_HEIGHT']."px; position:absolute; top:33px;\" class=\"graph\" id=\"".$this->url."\"></div>\n";
    $path = pnp::addToUri( array('tpl' => $this->tpl,
				'view' => $value['VIEW'],
				'source' => $value['SOURCE'], 
				'start' => $value['TIMERANGE']['start'], 
				'end' => $value['TIMERANGE']['end']), FALSE 
			);
    echo "<img class=\"graph\" src=\"".url::base(TRUE)."image" . $path . "\">\n";
    echo "</div>\n";
    echo "</a></div><p>\n";
}
echo "</div>\n";
?>
<!-- Graph Content End-->

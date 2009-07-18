<html><head>
</head><body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table>
<div id='zoomBox' style='position:absolute; overflow:none; left:0px; top:0px; width:0px; height:0px; visibility:visible; background:red; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity:0.5; opacity:0.5'></div>
<div id='zoomSensitiveZone' style='position:absolute; overflow:none; left:0px; top:0px; width:0px; height:0px; visibility:visible; cursor:crosshair; background:blue; filter:alpha(opacity=0); -moz-opacity:0; -khtml-opacity:0;opacity:0' oncontextmenu='return false'></div>
<STYLE MEDIA="print">
  div#zoomBox, div#zoomSensitiveZone {display: none}
  #why {position: static; width: auto}
</STYLE>
<tr><td><img id="zoomGraphImage" src="image?source=<?php echo $source?>&host=<?php echo $host?>&srv=<?php echo $srv?>&start=<?php echo $start?>&end=<?php echo $end?>&graph_height=<?php echo $graph_height?>&graph_width=<?php echo $graph_width?>&title_font_size=10">
</tr></td><tr><td>TIMERANGE</tr></td></table>
<?php include("media/js/zoom.js")?>
</body></html>


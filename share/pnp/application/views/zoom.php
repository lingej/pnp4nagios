<html>
<head>
<?php echo html::stylesheet('media/css/common.css') ?>
<?php echo html::stylesheet('media/css/ui-'.$this->theme.'/jquery-ui.css') ?>
</head>
<body>
<div class="pagebody">
<div class="ui-widget">
<div class="ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.zoom-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom">
<h3> <?php echo $this->data->TIMERANGE['f_start']?> --- <?php echo $this->data->TIMERANGE['f_end']?> </h3>
<div id='zoomBox' style='position:absolute; overflow:none; left:0px; top:0px; width:0px; height:0px; visibility:visible; background:red; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity:0.5; opacity:0.5'></div>
<div id='zoomSensitiveZone' style='position:absolute; overflow:none; left:0px; top:0px; width:0px; height:0px; visibility:visible; cursor:crosshair; background:blue; filter:alpha(opacity=0); -moz-opacity:0; -khtml-opacity:0;opacity:0' oncontextmenu='return false'></div>
<STYLE MEDIA="print">
  div#zoomBox, div#zoomSensitiveZone {display: none}
  #why {position: static; width: auto}
</STYLE>
<?php if(!empty($tpl)){ ?>
<img id="zoomGraphImage" src="image?source=<?php echo $source?>&tpl=<?php echo $tpl?>&view=<?php echo $view?>&start=<?php echo $start?>&end=<?php echo $end?>&graph_height=<?php echo $graph_height?>&graph_width=<?php echo $graph_width?>&title_font_size=10">
<?php }else{ ?>
<img id="zoomGraphImage" src="image?source=<?php echo $source?>&host=<?php echo $host?>&srv=<?php echo $srv?>&view=<?php echo $view?>&start=<?php echo $start?>&end=<?php echo $end?>&graph_height=<?php echo $graph_height?>&graph_width=<?php echo $graph_width?>&title_font_size=10">
<?php }
include("media/js/zoom.js") ?>
</div>
</div>
</body>
</html>


<html>
<head>
<?php echo html::stylesheet('media/css/common.css') ?>
<?php echo html::stylesheet('media/css/imgareaselect-default.css') ?>
<?php echo html::stylesheet('media/css/ui-'.$this->theme.'/jquery-ui.css') ?>
<?php echo html::link('media/images/favicon.ico','icon','image/ico') ?>
<?php echo html::script('media/js/jquery-min.js')?>
<?php echo html::script('media/js/jquery.imgareaselect.min.js')?>
<?php echo html::script('media/js/jquery-ui.min.js')?>

<script type="text/javascript">
jQuery.noConflict();
    jQuery(window).load(
        function() {
    
        jQuery('div.graph').each(function(){
            var img_width = jQuery(this).next('img').width();
            var rrd_width = parseInt(jQuery(this).css('width'));
            var left = img_width - rrd_width - <?php echo $this->config->conf['right_zoom_offset'] ?>;
            jQuery(this).css('left', left);
            jQuery(this).css('cursor', 'e-resize');
            jQuery(this).attr('title', 'Click to zoom in');
        });
    
	jQuery('img.goto').css('visibility', 'visible');
        jQuery('div.graph').imgAreaSelect({ handles: false, autoHide: true,
            fadeSpeed: 500, onSelectEnd: redirect, minHeight: '<?php echo $this->config->conf['zgraph_height'] ?>' });
    
        function redirect(img, selection) {
            if (!selection.width || !selection.height)
                    return;

            var graph_width = parseInt(jQuery(img).css('width'));
            var source = <?php echo $this->source?>;
            var link   = jQuery(img).attr('id');
            var ostart = Math.abs(jQuery(img).attr('start'));
            var oend   = Math.abs(jQuery(img).attr('end'));
            var delta  = (oend - ostart);
            if( delta < 600 )
                delta = 600;
            var sec_per_px = parseInt( delta / graph_width);
            var start = ostart + Math.ceil( selection.x1 * sec_per_px );  
            var end   = ostart + ( selection.x2 * sec_per_px );  
            window.location = link + '&source=' + source + '&start=' + start + '&end=' + end ; 
        }
    });
</script>
</head>
<body>
<div class="pagebody">
<div class="ui-widget">
<div class="ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.zoom-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom">
<h3> <?php echo $this->data->TIMERANGE['f_start']?> --- <?php echo $this->data->TIMERANGE['f_end']?> </h3>
<div style="position:relative;">
<?php 
echo "<div start=$start end=$end style=\"width:".$graph_width."px; height:".$graph_height."px; position:absolute; top:33px\" class=\"graph\" id=\"".$this->url."\" ></div>";
if(!empty($tpl)){
    echo "<img class=\"graph\" src=\"image?source=$source"
	."&tpl=$tpl"
        ."&view=$view"
	."&start=$start"
	."&end=$end"
	."&graph_height=$graph_height"
	."&graph_width=$graph_width\">";
}else{
   $srv = urlencode($srv);
    echo "<img src=\"image?source=$source"
	."&host=$host"
	."&srv=$srv"
	."&view=$view"
	."&start=$start"
	."&end=$end"
	."&graph_height=$graph_height"
	."&graph_width=$graph_width\">";
} 
$start_down = $this->data->TIMERANGE['start'] - intval(($this->data->TIMERANGE['end'] - $this->data->TIMERANGE['start']) / 2);
$path = pnp::addToUri( array('start' => $start_down));
printf("<a href=\"%s\" title=\"%s\"><img class=\"goto\" src=\"%s\" style=\"visibility: hidden; position:absolute; left:%dpx; bottom:-28px\"></a>\n",
        $path,
        "Move Start to ".date($this->config->conf['date_fmt'],$start_down),
        url::base()."media/images/go-left.png",
        10
);

$start_up   = $this->data->TIMERANGE['start'] + intval(($this->data->TIMERANGE['end'] - $this->data->TIMERANGE['start']) / 2);      
$path = pnp::addToUri( array('start' => $start_up));         
printf("<a href=\"%s\" title=\"%s\"><img class=\"goto\" src=\"%s\" style=\"visibility: hidden; position:absolute; left:%dpx; bottom:-28px\"></a>\n",
        $path,
        "Move Start to ".date($this->config->conf['date_fmt'],$start_up),
        url::base()."media/images/go-right.png",
        60
);

$path = pnp::addToUri( array('end' => time() ));         
printf("<a href=\"%s\" title=\"%s\"><img class=\"goto\" src=\"%s\" style=\"visibility: hidden; position:absolute; right:%dpx; bottom:-28px\"></a>\n",
        $path,
        "Move End to ".date($this->config->conf['date_fmt'],time()),
        url::base()."media/images/go-now.png",
        10
);

$end_up = $this->data->TIMERANGE['end'] + intval(($this->data->TIMERANGE['end'] - $this->data->TIMERANGE['start']) / 2);
$path = pnp::addToUri( array('end' => $end_up));
printf("<a href=\"%s\" title=\"%s\"><img class=\"goto\" src=\"%s\" style=\"visibility: hidden; position:absolute; right:%dpx; bottom:-28px\"></a>\n",
        $path,
        "Move End to ".date($this->config->conf['date_fmt'],$end_up),
        url::base()."media/images/go-right.png",
        60
);
        
$end_down = $this->data->TIMERANGE['end'] - intval(($this->data->TIMERANGE['end'] - $this->data->TIMERANGE['start']) / 2);
$path = pnp::addToUri( array('end' => $end_down));
printf("<a href=\"%s\" title=\"%s\"><img class=\"goto\" src=\"%s\" style=\"visibility: hidden; position:absolute; right:%dpx; bottom:-28px\"></a>\n",
        $path,
        "Move End to ".date($this->config->conf['date_fmt'],$end_down),
        url::base()."media/images/go-left.png",
        110
);
        

?>
</div>
</div>
</div>
</body>
</html>


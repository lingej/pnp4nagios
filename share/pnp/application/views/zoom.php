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
    
        jQuery('div.graph').imgAreaSelect({ handles: false, autoHide: true,
            fadeSpeed: 500, onSelectEnd: redirect, minHeight: '100' });
    
        function redirect(img, selection) {
            if (!selection.width || !selection.height)
                    return;

            var graph_width = parseInt(jQuery(img).css('width'));
            var link   = jQuery(img).attr('id');
            var ostart = Math.abs(jQuery(img).attr('start'));
            var oend   = Math.abs(jQuery(img).attr('end'));
            var delta  = (oend - ostart);
            if( delta < 600 )
                delta = 600;
            var sec_per_px = parseInt( delta / graph_width);
            var start = ostart + Math.ceil( selection.x1 * sec_per_px );  
            var end   = ostart + ( selection.x2 * sec_per_px );  
            window.location = link + '&start=' + start + '&end=' + end ; 
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
    echo "<img src=\"image?source=$source"
	."&host=$host"
	."&srv=$srv"
	."&view=$view"
	."&start=$start"
	."&end=$end"
	."&graph_height=$graph_height"
	."&graph_width=$graph_width\">";
} ?>
</div>
</div>
</div>
</body>
</html>


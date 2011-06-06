<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="<?php echo $this->config->conf['refresh'] ?>"; URL="<?php echo $_SERVER['REQUEST_URI'] ?>">
<title><?php if (isset($this->title)) echo html::specialchars($this->title) ?></title>
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
	jQuery(this).css('cursor', 'crosshair');
	jQuery(this).attr('title', 'Click to zoom in');
    });

    jQuery('div.graph').imgAreaSelect({ handles: false, autoHide: true,
        fadeSpeed: 500, onSelectEnd: redirect, minHeight: '100' });

    function redirect(img, selection) {
    	if (!selection.width || !selection.height)
        	return;

	var graph_width = jQuery(img).parent().find('img').width() - 97;
	var link   = jQuery(img).attr('id');
	var ostart = Math.abs(jQuery(img).attr('start'));
	var oend   = Math.abs(jQuery(img).attr('end'));
	var sec_per_px = Math.ceil(( oend - ostart ) / graph_width);
	var start = ostart + Math.ceil( selection.x1 * sec_per_px );  
	var end   = ostart + ( selection.x2 * sec_per_px );  
        window.location = link + '&start=' + start + '&end=' + end ; 

    }

});
jQuery(document).ready(function(){
    var path = "<?php echo url::base(TRUE)."index.php/"?>";
    jQuery("img").fadeIn(1500);
    jQuery("#basket_action_add a").click(function(){
        var item = (this.id)
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/add",
            data: { item: item },
            success: function(msg){
                jQuery("#basket_items").html(msg);
            }
        });
    });
    jQuery("#basket_action_remove-all a").click(function(){
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/remove-all/",
            success: function(msg){
                jQuery("#basket_items").html(msg);
            }
        });
    });
    jQuery("#basket_action_remove a").live("click", function(){
        var item = (this.id)
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/remove/"+item,
            data: { item: item },
            success: function(msg){
                jQuery("#basket_items").html(msg);
            }
        });
    });
    jQuery("#remove_timerange_session").click(function(){
        jQuery.ajax({
            type: "GET",
            url: path + "ajax/remove/timerange",
            success: function(){
                location.reload();
            }
        });
    });


});

<?php if (!empty($zoom_header)) {
     echo $zoom_header;
} ?>
</script>
</head>
<body>
<?php if (!empty($graph)) {
     echo $graph;
} ?>
<?php if (!empty($debug)) {
     echo $debug;
} ?>
<?php if (!empty($zoom)) {
     echo $zoom;
} ?>
<?php if (!empty($page)) {
     echo $page;
} ?>
<?php if (!empty($docs)) {
     echo $docs;
} ?>
</body>
</html>

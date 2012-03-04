<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="<?php echo $this->config->conf['refresh'] ?>; url=<?php echo $_SERVER['REQUEST_URI'] ?>">
<title><?php if (isset($this->title)) echo html::specialchars($this->title) ?></title>
<?php echo html::stylesheet('media/css/common.css') ?>
<?php echo html::stylesheet('media/css/imgareaselect-default.css') ?>
<?php echo html::stylesheet('media/css/ui-'.$this->theme.'/jquery-ui.css') ?>
<?php echo html::link('media/images/favicon.ico','icon','image/ico') ?>
<?php echo html::script('media/js/jquery-min.js')?>
<?php echo html::script('media/js/jquery.imgareaselect.min.js')?>
<?php echo html::script('media/js/jquery-ui.min.js')?>
<?php echo html::script('media/js/jquery-ui-timepicker-addon.js')?>
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
        fadeSpeed: 500, onSelectEnd: redirect, minHeight: '<?php echo $this->config->conf['graph_height'] ?>' });

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
jQuery(document).ready(function(){
    var path = "<?php echo url::base(TRUE)."/"?>";
    jQuery("img").fadeIn(1500);
    jQuery("#basket_action_add a").live("click", function(){
        var item = (this.id)
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/add",
            data: { item: item },
            success: function(msg){
                jQuery("#basket_items").html(msg);
                window.location.reload() 
            }
        });
    });
    jQuery("#basket-clear").live("click", function(){
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/clear",
            success: function(msg){
                window.location.reload() 
            }
        });
    });
    jQuery("#basket-show").live("click", function(){
                window.location.href = path + 'page/basket' 
    });
    jQuery(".basket_action_remove a").live("click", function(){
        var item = (this.id)
        jQuery.ajax({
            type: "POST",
            url: path + "ajax/basket/remove/",
            data: { item: item },
            success: function(msg){
                jQuery("#basket_items").html(msg);
                window.location.reload() 
            }
        });
    });
    jQuery("#basket_items" ).sortable({
        update: function(event, ui) {
	    var items = jQuery(this).sortable('toArray').toString();
            jQuery.ajax({
                type: "POST",
                url: path + "ajax/basket/sort",
                data: { items: items },
                success: function(msg){
                    window.location.reload() 
                }
            });
        }
    });
    jQuery("#basket_items" ).disableSelection();
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

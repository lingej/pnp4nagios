<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="<?php echo $this->config->conf['refresh'] ?>"; URL="<?php echo $_SERVER['REQUEST_URI'] ?>">
<title><?php if (isset($this->title)) echo html::specialchars($this->title) ?></title>
<?php echo html::stylesheet('media/css/common.css') ?>
<?php echo html::stylesheet('media/css/ui-'.$this->theme.'/jquery-ui.css') ?>
<?php echo html::link('media/images/favicon.ico','icon','image/ico') ?>
<?php echo html::script('media/js/jquery-min.js')?>
<?php echo html::script('media/js/jquery-ui.min.js')?>
<script type="text/javascript">
$(document).ready(function(){
    var path = "<?php echo url::base(TRUE)."index.php/"?>";
    $("img").fadeIn(1500);
    $("#basket_action_add a").click(function(){
        var item = (this.id)
        $.ajax({
            type: "POST",
            url: path + "ajax/basket/add",
            data: { item: item },
            success: function(msg){
                $("#basket_items").html(msg);
            }
        });
    });
    $("#basket_action_remove-all a").click(function(){
        $.ajax({
            type: "POST",
            url: path + "ajax/basket/remove-all/",
            success: function(msg){
                $("#basket_items").html(msg);
            }
        });
    });
    $("#basket_action_remove a").live("click", function(){
        var item = (this.id)
        $.ajax({
            type: "POST",
            url: path + "ajax/basket/remove/"+item,
            data: { item: item },
            success: function(msg){
                $("#basket_items").html(msg);
            }
        });
    });
    $("#remove_timerange_session").click(function(){
        $.ajax({
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

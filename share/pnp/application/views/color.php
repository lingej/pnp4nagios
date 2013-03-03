<div class="pagebody">
<table class="body">
<tr valign="top"><td>
<div class="left ui-widget">
 <div class="p4 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.color-header') ?>
 </div>
<div class="p4 ui-widget-content ui-corner-bottom" style="width: 600px">
	<?php if (!empty($this->scheme)) {
		foreach( $this->scheme as $key => $colors ){
			print "<h3>\"" . $key . "\"</h3><ul class=\"colorscheme\">";  
			foreach($colors as $color){
				print "<li class=\"colorscheme\"><span class=\"colorscheme\" style=\"background-color:".$color."\">" . "</span></li>\n";
			}
			print "</ul>";
		}
		print "<br><br>";
	} ?>
    </div>
    </td><td>
	<div class="right">

	<?php if (!empty($color_box)) {
     		echo $color_box;
	} ?>

	<?php if (!empty($logo_box)) {
     		echo $logo_box;
	} ?>
	</div>
    </td></tr>
	<tr valign="top"><td colspan="2">
    <div class="cb p4 ui-widget-content ui-corner-all">
	<?php echo pnp::print_version(); ?>
    </div>
    </div>
	</td></tr></table>
</div>

<div class="pagebody">
    <table class="body"><tr valign="top"><td colspan="2">
	<?php if (!empty($header)) {
     		echo $header;
	} ?>
	</td></tr>
	<tr valign="top"><td>
    <div class="left ui-widget-content ui-corner-all">
	<?php if (!empty($graph_content)) {
     		echo $graph_content;
	} ?>
    </div>
    </td><td>
	<div class="right ui-widget-content ui-corner-all">
	<?php if (!empty($search_box)) {
     		echo $search_box;
	} ?>

	<?php if (!empty($icon_box)) {
     		echo $icon_box;
	} ?>

	<?php if (!empty($basket_box)) {
     		echo $basket_box;
	} ?>

	<?php if (!empty($status_box)) {
     		echo $status_box;
	} ?>

	<?php if (!empty($multisite_box)) {
     		echo $multisite_box;
	} ?>

	<?php if (!empty($widget_menu)) {
     		echo $widget_menu;
	} ?>

	<?php if (!empty($timerange_box)) {
     		echo $timerange_box;
	} ?>

	<?php if (!empty($timezone_box) && ($this->config->conf['show_timezone'])) {
     		echo $timezone_box;
	} ?>

	<?php if (!empty($service_box)) {
     		echo $service_box;
	} ?>
	<?php if (!empty($logo_box)) {
     		echo $logo_box;
	} ?>
	</div>
    </td></tr>
	<tr valign="top"><td colspan="2">
    <div class="cb p4 ui-widget-content ui-corner-all">
	<?php echo pnp::print_version()?>
    </div>
	</td></tr></table>
</div>

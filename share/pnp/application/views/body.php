<div class="pagebody b1">
	<?php if (!empty($header)) {
     		echo $header;
	} ?>

    <table class="body"><tr><td valign="top">
    <div class="left ui-corner-all">
	<?php if (!empty($graph_content)) {
     		echo $graph_content;
	} ?>
    </div>
    </td><td valign="top">
    <div class="right ui-corner-all">
	<?php if (!empty($search_box)) {
     		echo $search_box;
	} ?>

	<?php if (!empty($icon_box)) {
     		echo $icon_box;
	} ?>

	<?php if (!empty($status_box)) {
     		echo $status_box;
	} ?>
	<?php if (!empty($service_box)) {
     		echo $service_box;
	} ?>
    </div>
    </td></tr></table>
    <div class="left w99 cb ui-corner-all">
	<?php if (!empty($footer)) {
     		echo $footer .  Kohana::lang('core.stats_footer');
	} ?>
	<?php echo Kohana::lang('core.stats_footer') ?>
    </div>
</div>

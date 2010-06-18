<div class="pagebody">
    <table class="body"><tr valign="top"><td>
    <tr valign="top"><td>
    <div class="left ui-widget-content ui-corner-all" style="width: <?php echo $this->graph_width ?>px">
	<?php if (!empty($this->content)) {
     		echo $this->content;
	} ?>
    </div>
    </td><td>
	<div class="right ui-widget-content ui-corner-all">

	<?php if (!empty($docs_box)) {
     		echo $docs_box;
	} ?>

	<?php if (!empty($widget_menu)) {
     		echo $widget_menu;
	} ?>

	<?php if (!empty($logo_box)) {
     		echo $logo_box;
	} ?>
	</div>
    </td></tr>
	<tr valign="top"><td colspan="2">
    <div class="cb p4 ui-widget-content ui-corner-all">
	<?php echo Kohana::lang('core.stats_footer') ?>
    </div>
	</td></tr></table>
</div>

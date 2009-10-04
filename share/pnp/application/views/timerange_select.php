<script type="text/javascript">
$(function() {
    $("#button").click(function() {
        $("#toggle-timerange").toggle("blind",500); 
        return false;
    });
	$("#dpstart").datepicker({ showOn: 'button', buttonImage: 'media/images/calendar.gif', buttonImageOnly: true, constrainInput: false });
	$("#dpend").datepicker({ showOn: 'button', buttonImage: 'media/images/calendar.gif', buttonImageOnly: true, constrainInput: false });
});
</script>

<div id="toggle-timerange" class="ui-widget" style="Display: none;">
    <div class="p4 ui-widget-header ui-corner-top"><?php echo Kohana::lang('common.timerange-selector-title') ?></div>
    <div class="p4 ui-widget-content ui-corner-bottom">
    <form method="GET" action="<?php echo url::base() ?>graph">
        <fieldset>
            <legend><?php echo Kohana::lang('common.timerange-selector-legend') ?></legend>
			<input type="hidden" name="host" value="<?php echo $this->host ?>">
			<input type="hidden" name="srv" value="<?php echo $this->service ?>">
            <label for=start"> Start: </label><input id="dpstart" type="text" size="16" maxlength="40" name="start" value="<?php echo $this->start?>">
			<label for=end"> End: </label><input id="dpend" type="text" size="16" maxlength="40" name="end" value="<?php echo $this->end?>">
			<input type="submit" id="submit" class="ui-button ui-state-default ui-corner-all" value="<?php echo Kohana::lang('common.timerange-selector-submit-button') ?>"></input>
        </fieldset>
    </form><p>
    </div>
</div>

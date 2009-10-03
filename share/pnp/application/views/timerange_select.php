<script type="text/javascript">
$(function() {
    $("#button").click(function() {
        $("#toggle-timerange").toggle("blind",500); 
        return false;
    });
	$("#dpstart").datepicker({ constrainInput: false });
	$("#dpend").datepicker({ constrainInput: false });
});
</script>

<div id="toggle-timerange" class="ui-widget" style="Display: none;">
    <div class="p4 ui-widget-header ui-corner-top">Select custom timerange</div>
    <div class="ui-widget-content ui-corner-bottom">
    <form method="GET" action="<?php echo url::base() ?>graph">
        <fieldset>
            <legend>Define a Custom Timerange</legend>
			<input type="hidden" name="host" value="<?php echo $this->host ?>">
			<input type="hidden" name="srv" value="<?php echo $this->service ?>">
            <label for=start">Start: </label><input id="dpstart" type="text" size="10" maxlength="40" name="start" value="<?php echo $this->start?>">
            <label for=end">End: </label><input id="dpend" type="text" size="10" maxlength="40" name="end" value="<?php echo $this->end?>">
            <input type="submit" value=" Absenden ">
        </fieldset>
    </form><p>
    </div>
</div>

<!-- Icon Box Start -->
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<script type="text/javascript">
	$(function() {
		var currentTime = new Date()
		var hours = (currentTime.getHours() * 60 * 60)
		var minutes = (currentTime.getMinutes() * 60)
		var sec = (hours + minutes)
		$("#datepicker").datepicker({showOn: 'button', buttonImage: 'media/images/calendar.png', dateFormat: '@', buttonImageOnly: true, onSelect: function(dateText, inst) { window.location.href = 'graph<?php echo $this->url ?>&end=' + (dateText / 1000 + sec) }});
	});
	</script>

<input type="hidden" id="datepicker">
<?php
if($this->config->conf['use_fpdf'] == 1){
	echo "<a title=\"PDF View\" href=\"pdf".$this->url."&view=".$this->view."\"><img src=\"media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['show_xml_icon'] == 1){
	echo "<a title=\"XML View\" href=\"xml".$this->url."\"><img src=\"media/images/XML_32.png\"></a>\n";
}?>
</div>
</div><p>
<!-- Icon Box End -->


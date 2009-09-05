<!-- Icon Box Start -->
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<?php
if($position == "graph"){
echo "<script type=\"text/javascript\">
	$(function() {
		var currentTime = new Date()
		var hours = (currentTime.getHours() * 60 * 60)
		var minutes = (currentTime.getMinutes() * 60)
		var sec = (hours + minutes)
		$(\"#datepicker\").datepicker({showOn: 'button', buttonImage: '".url::base()."media/images/calendar.png', dateFormat: '@', buttonImageOnly: true, onSelect: function(dateText, inst) { window.location.href = '".url::base()."graph".$this->url."&end=' + (dateText / 1000 + sec) }});
	});
	</script>
<input type=\"hidden\" id=\"datepicker\">\n";
}
if($this->config->conf['use_fpdf'] == 1 && $position == "graph"){
	echo "<a title=\"PDF View\" href=\"".url::base()."pdf".$this->url."&view=".$this->view."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['use_fpdf'] == 1 && $position == "basket"){
	echo "<a title=\"PDF View\" href=\"".url::base()."pdf/basket?view=".$this->view."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['use_fpdf'] == 1 && $position == "page"){
	echo "<a title=\"PDF View\" href=\"".url::base()."pdf/page/".$this->page."?view=".$this->view."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['show_xml_icon'] == 1 && $position == "graph"){
	echo "<a title=\"XML View\" href=\"".url::base()."xml".$this->url."\"><img src=\"".url::base()."media/images/XML_32.png\"></a>\n";
}
if($this->data->getFirstPage()){
	echo "<a title=\"Goto Pages\" href=\"".url::base()."page\"><img src=\"".url::base()."media/images/pages.png\"></a>\n";
}
?>
</div>
</div><p>
<!-- Icon Box End -->

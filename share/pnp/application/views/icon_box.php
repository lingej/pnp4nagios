<!-- Icon Box Start -->
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<?php
$qsa  = pnp::addToUri(array('start' => $this->start,'end' => $this->end, 'view' => $this->view), False);
if($this->config->conf['use_calendar']){
	echo "<a title=\"".Kohana::lang('common.title-calendar-link')."\" href=\"#\" id=\"button\"><img src=\"".url::base()."media/images/calendar.png\"></a>"; 
}
if($this->config->conf['use_fpdf'] == 1 && $position == "graph"){
	echo "<a title=\"".Kohana::lang('common.title-pdf-link')."\" href=\"".url::base()."pdf".$qsa."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['use_fpdf'] == 1 && $position == "basket"){
	echo "<a title=\"".Kohana::lang('common.title-pdf-link')."\" href=\"".url::base()."pdf/basket/".$qsa."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['use_fpdf'] == 1 && $position == "page"){
	echo "<a title=\"".Kohana::lang('common.title-pdf-link')."\" href=\"".url::base()."pdf/page/".$this->page.$qsa."\"><img src=\"".url::base()."media/images/PDF_32.png\"></a>\n";
}
if($this->config->conf['show_xml_icon'] == 1 && $position == "graph"){
	$qsa  = pnp::addToUri(array(), False);
	echo "<a title=\"".Kohana::lang('common.title-xml-link')."\" href=\"".url::base()."xml".$qsa."\"><img src=\"".url::base()."media/images/XML_32.png\"></a>\n";
}
if($this->data->getFirstPage() && $this->isAuthorizedFor('pages') ){
	echo "<a title=\"".Kohana::lang('common.title-pages-link')."\" href=\"".url::base()."page\"><img src=\"".url::base()."media/images/pages.png\"></a>\n";
}
echo "<a title=\"".Kohana::lang('common.title-statistics-link')."\" href=\"".url::base()."graph?host=.pnp-internal&srv=runtime\"><img src=\"".url::base()."media/images/stats.png\"></a>\n";
?>
</div>
</div><p>
<!-- Icon Box End -->

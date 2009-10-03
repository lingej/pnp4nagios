<!-- Icon Box Start -->
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<?php
if($position == "graph"){
	echo "<a href=\"#\" id=\"button\"><img src=\"".url::base()."media/images/calendar.png\"></a>"; 
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

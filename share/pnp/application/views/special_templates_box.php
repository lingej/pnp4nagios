<?php if (!empty($this->templates) && $this->isAuthorizedFor('service_links') ) { ?>
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.special-templates-box-header') ?> 
</div>
<div class="p4 ui-widget-content ui-corner-bottom">
<?php
foreach($this->templates as $template){
	$path = pnp::addToUri( array('tpl' => $template) );
	echo "<a href=\"".$path."\" class=\"multi0\">".
		 pnp::shorten($template).
		 "</a><br>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

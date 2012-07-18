<?php if (!empty($this->templates) && $this->isAuthorizedFor('service_links') ) { ?>
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.special-templates-box-header') ?> 
</div>

    <div class="p4 ui-widget-content">
        <input type="text" name="special-filter" id="special-filter" class="textbox" />
    </div>

<div class="p4 ui-widget-content ui-corner-bottom" id="special-templates">
<?php
foreach($this->templates as $template){
	echo "<span id=\"special-".$template."\">";
	$path = pnp::addToUri( array('tpl' => $template) );
	echo "<a href=\"".$path."\" class=\"multi0\">".
		 pnp::shorten($template).
		 "</a><br>\n";
	echo "</span>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

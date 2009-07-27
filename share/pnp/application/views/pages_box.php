<?php if (!empty($pages)) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.pages-box-header') ?>
 </div>
<div class="p4 ui-widget-content ui-corner-bottom">
<?php
foreach($pages as $page){
	$this->data->getPageDetails($page);
	echo "<a href=\"".$this->uri->string()."?page=".$page."\">".pnp::shorten($this->data->PAGE_DEF['page_name'])."</a><br>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

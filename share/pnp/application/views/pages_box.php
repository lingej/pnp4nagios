<?php if (!empty($pages) && $this->isAuthorizedFor('pages') ) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.pages-box-header') ?>
 </div>
<div class="p4 ui-widget-content ui-corner-bottom">
<?php
foreach($pages as $page){
	$this->data->getPageDetails($page);
	echo "<a class=\"multi0\" href=\"".url::base(TRUE)."page?page=".$page."\" title=\"".$this->data->PAGE_DEF['page_name']."\">".pnp::shorten($this->data->PAGE_DEF['page_name'])."</a><br>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

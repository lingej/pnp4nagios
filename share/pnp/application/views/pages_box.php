<?php if (!empty($pages) && $this->isAuthorizedFor('pages') ) { ?>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.pages-box-header') ?>
 </div>

<?php
	$filter = $this->session->get('pfilter');
?>

	 <div class="p4 ui-widget-content">
		<?php
			echo "<input type=\"text\" name=\"page-filter\" id=\"page-filter\" value=\"".$filter."\" class=\"textbox\" />"
		?>
    </div>

<div class="p4 ui-widget-content ui-corner-bottom" id="pages">
<?php
foreach($pages as $page){
	echo "<span id=\"page-".$page."\">";
	$this->data->getPageDetails($page);
	echo "<a class=\"multi0\" href=\"".url::base(TRUE)."page?page=".$page."\" title=\"".$this->data->PAGE_DEF['page_name']."\">".pnp::shorten($this->data->PAGE_DEF['page_name'])."</a><br>\n";
	echo "</span>\n";
}
?>
</div>
</div>
<p>
<?php } ?>

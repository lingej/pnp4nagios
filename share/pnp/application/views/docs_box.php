<!-- Docs Menu Start -->
<div class="ui-widget">
<div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.icon-box-header') ?>
</div>
<div class="p4 ui-widget-content ui-corner-bottom" >
<?php 
echo "<a title=\"".Kohana::lang('common.title-home-link')."\" href=\"".url::base(TRUE)."graph\"><img class=\"icon\" src=\"".url::base()."media/images/home.png\"></a>\n";
echo "<a title=\"".Kohana::lang('common.title-docs-link')."\" href=\"".url::base(TRUE)."docs\"><img class=\"icon\" src=\"".url::base()."media/images/docs.png\"></a>\n";
foreach ( $this->doc_language as $lang ){
    echo "<a href=\"".url::base(TRUE)."docs/view/".$lang."/start\"><img class=\"icon\" src=\"".url::base()."media/images/".$lang.".png\"></a> \n";
}
?>
</div>
</div>
<p>
<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.docs-box-header') ?>
 </div>
 <div class="p4 ui-widget-content ui-corner-bottom" >
 <ul><li class="level1"><a href="start"><strong><?php echo Kohana::lang('common.docs-home')?></strong></a></li></ul>
 <?php echo $this->toc ?>
 </div>
</div>
<p>
<!-- Docs Menu End -->

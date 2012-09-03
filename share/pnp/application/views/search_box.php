<?php if( $this->isAuthorizedFor('host_search') ){ ?>
<!-- Search Box Start -->
<script type="text/javascript">
jQuery(function() {
    jQuery("#query").autocomplete({
        source: "<?php echo url::base('true')?>/index.php/ajax/search",
        select: function(event, ui) { window.location = "<?php echo url::base('true')?>" + "graph?host=" + ui.item.value  }
    });
});
</script>

<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
 <?php echo Kohana::lang('common.search-box-header') ?>
 </div>
 <div class="p4 ui-widget-content ui-corner-bottom">
   <input type="text" name="host" id="query" class="textbox" />
 </div>
</div>
<p>
<!-- Search Box End -->
<?php } ?>

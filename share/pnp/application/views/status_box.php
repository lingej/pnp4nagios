<div class="ui-widget">
 <div class="p2 ui-widget-header ui-corner-top">
<?php echo Kohana::lang('common.status-box-header') ?>
 </div>
 <div class="p4 ui-widget-content ui-corner-bottom">
<?php if(isset($host)) echo "<strong>Host:&nbsp;</strong>".
	html::anchor('graph'.
	"?host=".$lhost,
	html::specialchars(pnp::shorten($host))."<br>");?>
<?php if(isset($service)) echo "<strong>Service:&nbsp;</strong>" .
	html::anchor('graph'.
	"?host=".$lhost.
	"&srv=".$lservice,
	html::specialchars(pnp::shorten($service))."<br>");?>
<?php if(isset($timet)) echo "<strong>Last Check:&nbsp;</strong>$timet<br>"?>
 </div>
</div>
<p>


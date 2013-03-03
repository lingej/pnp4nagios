<?php 
if($this->is_authorized === FALSE){
    print "<div class=\"pagebody b1\"><h2>Your are not authorized to view this site</h2></div>";
    return; 
}    
?>
<div class="pagebody b1">

<script type="text/javascript">
	jQuery(function() {
		jQuery("#tabs").tabs();
	});
</script>
<table class="body"><tr><td valign="top">
<div class="gw left ui-corner-all">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Data Structure</a></li>
		<li><a href="#tabs-2">RRD Datasource</a></li>
		<li><a href="#tabs-3">Nagios Macros</a></li>
		<li><a href="#tabs-4">PHP Session </a></li>
	</ul>
	<div id="tabs-1">
	<h3>$this->data->STRUCT</h3>
	<pre>
<?php print_r($this->data->STRUCT);?>
	</pre>
	</div>
	<div id="tabs-2">
	<h3>$this->data->DS</h3>
	<pre>
<?php print_r($this->data->DS);?>
	</pre>
	</div>
	<div id="tabs-3">
	<h3>$this->data->MACRO</h3>
	<pre>
<?php print_r($this->data->MACRO);?>
	</pre>
	</div>
	<div id="tabs-4">
	<h3>$this->session->get()</h3>
	<pre>
<?php print_r($this->session->get());?>
	</pre>
	</div>
</div>
</div>
    </td><td valign="top">
    <div class="right ui-corner-all">
    <?php if (!empty($search_box)) {
            echo $search_box;
    } ?>

    <?php if (!empty($icon_box)) {
            echo $icon_box;
    } ?>

    <?php if (!empty($status_box)) {
            echo $status_box;
    } ?>
    <?php if (!empty($service_box)) {
            echo $service_box;
    } ?>
    </div>
    </td></tr></table>
    <div class="left w99 cb ui-corner-all">
    <?php if (!empty($footer)) {
            echo $footer .  pnp::print_version();
    } ?>
    <?php echo pnp::print_version(); ?>
    </div>


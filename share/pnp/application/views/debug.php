<div class="pagebody b1">

<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
	});
	</script>



<div class="demo">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Data Structure</a></li>
		<li><a href="#tabs-2">RRD Datasource</a></li>
		<li><a href="#tabs-3">Nagios Macros</a></li>
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
</div>

</div>
</div>

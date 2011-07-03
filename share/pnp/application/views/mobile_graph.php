<?php
if($this->is_authorized == FALSE){
?>
<div data-role="content">
<ul data-role="listview" data-inset="true" data-theme="e">
<li><strong>Alert:&nbsp;</strong><?php echo Kohana::lang('error.not_authorized')?></li>
</ul>
</div><!-- /content -->
<?php
return;
}
?>
<div data-role="content" data-inset="true">
<?php
foreach($this->data->STRUCT as $d){
	if($d['LEVEL'] == 0){
		printf("<strong>%s</strong><br>\n", $d['TIMERANGE']['title']);
	}
	printf("%s<br>\n", $d['ds_name']);
	printf("<img style=\"max-width: 100%%\" src=\"".url::base(TRUE)."image?host=%s&srv=%s&view=%s&source=%s\"><br>\n", 
		$d['MACRO']['HOSTNAME'], 
		$d['MACRO']['SERVICEDESC'],
		$d['VIEW'],
		$d['SOURCE']
	);	
	
}
?>
</div>

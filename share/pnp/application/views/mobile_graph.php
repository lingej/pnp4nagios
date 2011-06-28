<pre><? #print_r($this->data->STRUCT); ?></pre>
<div data-role="collapsible-set">
<?php
foreach($this->data->STRUCT as $d){
	if($d['LEVEL'] == 0){
		printf("<strong>%s</strong><br>\n", $d['TIMERANGE']['title']);
	}
	printf("%s<br>\n", $d['ds_name']);
	printf("<img width=100%% src=\"/pnp4nagios/image?host=%s&srv=%s&view=%s&source=%s\"><br>\n", 
		$d['MACRO']['HOSTNAME'], 
		$d['MACRO']['SERVICEDESC'],
		$d['VIEW'],
		$d['SOURCE']
	);	
	
}
?>
</div>

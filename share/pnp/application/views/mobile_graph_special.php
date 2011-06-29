<div data-role="collapsible-set">
<?php

if($this->data->MACRO['TITLE'])
    echo "<strong>".$this->data->MACRO['TITLE']."</strong><p>\n";
if($this->data->MACRO['COMMENT'])
    echo $this->data->MACRO['COMMENT']."<p>\n";

foreach($this->data->STRUCT as $d){
	if($d['LEVEL'] == 0){
		printf("<strong>%s</strong><br>\n", $d['TIMERANGE']['title']);
	}
	printf("%s<br>\n", $d['ds_name']);
	printf("<img width=100%% src=\"".url::base(TRUE)."image?tpl=%s&view=%s&source=%s\"><br>\n", 
		$this->tpl,
		$d['VIEW'],
		$d['SOURCE']
	);	
	
}
?>
</div>

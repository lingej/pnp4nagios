<table><tr><td>
<?php
foreach ( $this->data->STRUCT as $KEY=>$VAL){
	$source = $VAL['SOURCE'];
	echo "<tr><td>\n";
	echo "<img width=\"".$imgwidth."\" src=\"/pnp4nagios/image?host=$host&srv=$srv&view=$view&source=$source\">\n";
	echo "</td></tr>\n";
}
?>
</table>

<table style="background-color:#FFF"><tr><td>
<?php
foreach ( $this->data->DS as $KEY=>$VAL){
	$source = $VAL['DS'];
	echo "<tr><td>\n";
	echo "<img width=\"".$imgwidth."\" src=\"/pnp4nagios/image?host=$host&srv=$srv&view=$view&source=$source\">\n";
	echo "</td></tr>\n";
}
?>
</table>

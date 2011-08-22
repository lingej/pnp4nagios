<table><tr><td>
<?php
foreach ( $this->data->STRUCT as $KEY=>$VAL){
	$source = $VAL['SOURCE'];
	echo "<tr><td>\n";
	echo "<img width=\"".$imgwidth."\" src=\"".url::base(TRUE)."image?host=".urlencode($host)."&srv=".urlencode($srv)."&view=$view&source=$source\">\n";
	echo "</td></tr>\n";
}
?>
</table>

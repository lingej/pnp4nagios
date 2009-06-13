<div class="left b1 w99">
Status Box<br>

<?php if (!empty($hosts)) {
foreach($hosts as $host){
	echo html::anchor('graph?host='.$host['name'], $host['name'], array('class'=>$host['state']))."</p>";
}
}
?>

</div>

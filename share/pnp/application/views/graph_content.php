<!-- Graph Content Start-->
<div class="gw ui-widget">
<?php foreach($this->data->STRUCT as $key=>$value){ ?> 
    <?php if($value['LEVEL'] == 0)
	echo "<h5>".$value['TIMERANGE']['f_start']. " - " . $value['TIMERANGE']['f_end']. "</h5>\n";
    ?>
     <div class="ui-widget-header ui-corner-top">
       <table border=0 width=100%><tr>
       <td align=left><?php echo Kohana::lang('common.datasource',$value['ds_name'])?>
       </td><td align=right>
	<?php echo html::image('media/images/PDF_16.png');?>
	<?php echo html::image('media/images/XML_16.png');?>
       </td></tr></table>
     </div>
    <div class="p4 gh ui-widget-content ui-corner-bottom">
    <img src="image?host=<? echo $value['MACRO']['HOSTNAME']?>&srv=<? echo $value['MACRO']['SERVICEDESC']?>&view=<? echo $value['VIEW']?>&source=<? echo $value['SOURCE']?>">
  </div><p>
<?php } ?>
</div>
<!-- Graph Content End-->


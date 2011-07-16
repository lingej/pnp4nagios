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
$last_view = -1;
foreach($this->data->STRUCT as $d){
    if($d['VIEW'] > $last_view){ # a new header begins
	if($last_view != -1 ){   # close last div 
            print "</div>\n";
        }
        printf("<div class=\"timerange ui-bar-b ui-corner-top\">%s</div>\n", $d['TIMERANGE']['title']  );
        printf("<div class=\"datasource ui-bar-c ui-corner-bottom\">%s\n", $d['ds_name']);
        printf("<div><img style=\"max-width: 100%%\" src=\"".url::base(TRUE)."image?host=%s&srv=%s&view=%s&source=%s\"></div>\n", 
            $d['MACRO']['HOSTNAME'], 
            $d['MACRO']['SERVICEDESC'],
            $d['VIEW'],
            $d['SOURCE']
        );    
	$last_view++;
    }else{
        printf("<div>%s</div>\n", $d['ds_name']);
        printf("<div><img style=\"max-width: 100%%\" src=\"".url::base(TRUE)."image?host=%s&srv=%s&view=%s&source=%s\"></div>\n", 
            $d['MACRO']['HOSTNAME'], 
            $d['MACRO']['SERVICEDESC'],
            $d['VIEW'],
            $d['SOURCE']
        );    
    }
}
?>
</div>
</div>

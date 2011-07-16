<div data-role="content" data-inset="true">
<?php

if($this->data->MACRO['TITLE'])
    echo "<strong>".$this->data->MACRO['TITLE']."</strong><p>\n";
if($this->data->MACRO['COMMENT'])
    echo $this->data->MACRO['COMMENT']."<p>\n";

$last_view = -1;
foreach($this->data->STRUCT as $d){
    if($d['VIEW'] > $last_view){ # a new header begins
        if($last_view != -1 ){   # close last div 
            print "</div>\n";
        }
        printf("<div class=\"timerange ui-bar-b ui-corner-top\">%s</div>\n", $d['TIMERANGE']['title']  );
        printf("<div class=\"datasource ui-bar-c ui-corner-bottom\">%s\n", $d['ds_name']);
        printf("<div><img style=\"max-width: 100%%\" src=\"".url::base(TRUE)."image?tpl=%s&view=%s&source=%s\"></div>\n",
            $this->tpl,
            $d['VIEW'],
            $d['SOURCE']
        );
        $last_view++;
    }else{
        printf("<div>%s</div>\n", $d['ds_name']);
        printf("<div><img style=\"max-width: 100%%\" src=\"".url::base(TRUE)."image?tpl=%s&view=%s&source=%s\"></div>\n",
            $this->tpl,
            $d['VIEW'],
            $d['SOURCE']
        );
    }
}
?>
</div>

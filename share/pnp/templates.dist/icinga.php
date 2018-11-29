<?php
# 
# PNP template for the icinga2's built-in icinga check
# See https://www.icinga.com/docs/icinga2/latest/doc/10-icinga-template-library/#icinga
# Based on a template for check_nagiostats written by Joerg Linge
# Copyright (c) 2017 Yannick Charton
#

$graph = 0; 
$opt[$graph] = '--title "Check Latency" --vertical-label "seconds"';
$ds_name[$graph] = "Check Latency";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(.*)_latency(.*)$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), $label );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Service Stats"';
$ds_name[$graph] = "Service Stats";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(.*)num_services_(.*)$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[2]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Host Stats"';
$ds_name[$graph] = "Host Stats";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(.*)num_hosts_(.*)$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[2]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Check Execution Time" --vertical-label "seconds"';
$ds_name[$graph] = "Execution Time";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(.*)_execution_time(.*)$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Work Queue Items" --vertical-label "items"';
$ds_name[$graph] = "Work Queue Items";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^api_num_(.*)_queue_items$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Work Queue Rate" --vertical-label "items/s"';
$ds_name[$graph] = "Work Queue Rate";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^api_num_(.*)_queue_item_rate$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Check Rate" --vertical-label "checks/s"';
$ds_name[$graph] = "Check Rate";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(.*)_checks$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1]));
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,25) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "IDO MySQL Queries" --vertical-label "queries/s"';
$ds_name[$graph] = "IDO MySQL Queries";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^(idomysqlconnection_ido-mysql_queries_rate)$/', $VAL['NAME'], $matches)){
        $i++;
        $label = "mysql_queries";
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,15) );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
if($i > 0) $graph++; 
$opt[$graph] = '--title "Endpoints"';
$ds_name[$graph] = "Endpoints";
$def[$graph] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^api_num_(.*)_endpoints$/', $VAL['NAME'], $matches)){
        $i++;
        $label = ucfirst(strtolower($matches[1])) . 'ected';
        $colorarea = ($label == 'Connected') ? '#2ECC71' : '#E74C3C';
        $def[$graph] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$graph] .= rrd::area    ("var$KEY", $colorarea, rrd::cut($label,25), 'STACK' );
        $def[$graph] .= rrd::gprint  ("var$KEY", array("LAST"), "%8.0lf");
    }
}

?>

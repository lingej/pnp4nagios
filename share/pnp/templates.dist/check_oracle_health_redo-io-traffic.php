<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# $Id: check_oracle_health_redo-io-traffic.php 523 2008-09-26 17:10:20Z pitchfork $
#

$opt[1] = "--vertical-label \"MB/s\" -X0 --title \"Redo IO $hostname / $servicedesc\" ";


$def[1] =  "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "AREA:var1#F2F2F2:\"\" " ;
$def[1] .= "LINE1:var1#F30000:\"Redo IO\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%3.4lf MB/s LAST \" "; 
$def[1] .= "GPRINT:var1:MAX:\"%3.4lf MB/s MAX \" "; 
$def[1] .= "GPRINT:var1:AVERAGE:\"%3.4lf MB/s AVERAGE \" "; 
?>

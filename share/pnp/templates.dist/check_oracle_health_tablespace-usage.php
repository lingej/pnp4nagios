<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
#

$opt[1] = "--vertical-label \"TBS usage %\" -u102 -l0 --title \"Tablespace usage $servicedesc\" ";
$ds_name[1] = "TBS usage %";

$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "AREA:var1#F2F2F2:\"\" " ;
$def[1] .= "LINE1:var1#FF6600:\"used %\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%3.2lf %% LAST \" "; 
$def[1] .= "GPRINT:var1:MAX:\"%3.2lf %% MAX \" "; 
$def[1] .= "GPRINT:var1:AVERAGE:\"%3.2lf %% AVERAGE \" "; 


$opt[2] = " -X 0 --vertical-label \"TBS usage $UNIT[2]\" --title \"Tablespace usage $servicedesc\" ";
$ds_name[2] = "TBS usage ". $UNIT[2];


$def[2] =  "DEF:var1=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[2] .= "DEF:var2=$RRDFILE[3]:$DS[3]:AVERAGE " ;
$def[2] .= "AREA:var2#F2F2F2:\"\" " ;
$def[2] .= "AREA:var1#C3C3C3:\"\" " ;
$def[2] .= "LINE1:var2#F30000:\"alloc $UNIT[3]\" " ;
$def[2] .= "GPRINT:var2:LAST:\"%6.2lf $UNIT[3] LAST \" "; 
$def[2] .= "GPRINT:var2:MAX:\"%6.2lf $UNIT[3] MAX \" "; 
$def[2] .= "GPRINT:var2:AVERAGE:\"%6.2lf $UNIT[3] AVERAGE \\n\" "; 
$def[2] .= "LINE1:var1#FF6600:\"used $UNIT[2]\" " ;
$def[2] .= "GPRINT:var1:LAST:\"%6.2lf $UNIT[2] LAST \" "; 
$def[2] .= "GPRINT:var1:MAX:\"%6.2lf $UNIT[2] MAX \" "; 
$def[2] .= "GPRINT:var1:AVERAGE:\"%6.2lf $UNIT[2] AVERAGE \\n\" "; 
?>

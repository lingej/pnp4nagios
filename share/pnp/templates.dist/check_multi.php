<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_multi
# 

$opt[1] = "--lower-limit=$MIN[1] --vertical-label num  --title \"Number of Checks\" ";
$ds_name[1] = "Executed Plugins";

$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "AREA:var1#FF660080:\"Number of Checks\" " ;
$def[1] .= "LINE1:var1#00000050:\"\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%3.4lg %s$UNIT[1] LAST \" "; 
$def[1] .= "GPRINT:var1:MAX:\"%3.4lg %s$UNIT[1] MAX \" "; 
$def[1] .= "GPRINT:var1:AVERAGE:\"%3.4lg %s$UNIT[1] AVERAGE \\n\" "; 

$def[1] .=  "DEF:var2=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[1] .= "AREA:var2#FF8C0080:\"Runtime\" " ;
$def[1] .= "LINE1:var2#00000050:\"\" " ;
$def[1] .= "GPRINT:var2:LAST:\"%3.4lg %s$UNIT[1] LAST \" "; 
$def[1] .= "GPRINT:var2:MAX:\"%3.4lg %s$UNIT[1] MAX \" "; 
$def[1] .= "GPRINT:var2:AVERAGE:\"%3.4lg %s$UNIT[1] AVERAGE \" "; 
?>

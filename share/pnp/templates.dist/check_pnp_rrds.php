<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_pnp_rrds.pl
#
$opt[1] = "--slope-mode --watermark \"Created by PNP4Nagios\" --title \"PNP4Nagios XML Statistics\" ";
#
#
$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:MAX " ;
$def[1] .= "DEF:var2=$RRDFILE[2]:$DS[2]:MAX " ;
$def[1] .= "DEF:var3=$RRDFILE[3]:$DS[3]:MAX " ;
$def[1] .= "CDEF:total=var1,var2,-,var3,- " ;
$def[1] .= "AREA:var1#FF8C00:\"Total XML Files \" " ;
$def[1] .= "LINE1:total#000000 " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.0lf last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.0lf avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.0lf max\\n\" ";
$def[1] .= "AREA:var2#FF4500:\"RRD Errors      \":STACK  " ;
$def[1] .= "GPRINT:var2:LAST:\"%6.0lf last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%6.0lf avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%6.0lf max\\n\" ";
$def[1] .= "AREA:var3#FFD700:\"OLD XML Files   \":STACK  " ;
$def[1] .= "GPRINT:var3:LAST:\"%6.0lf last\" " ;
$def[1] .= "GPRINT:var3:AVERAGE:\"%6.0lf avg\" " ;
$def[1] .= "GPRINT:var3:MAX:\"%6.0lf max\\n\" ";
$def[1] .= "LINE1:var1#000000 " ;
?>

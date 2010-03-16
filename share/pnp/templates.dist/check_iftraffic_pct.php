<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_iftraffic.pl (%)
#
$opt[1] = "--vertical-label \"Traffic %\" --title \"Traffic % ($servicedesc)\" ";

$def[1]  = "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[1] .= "LINE1:var1#FF0000:\"in\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%7.2lf %% last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%7.2lf %% avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%7.2lf %% max\\n\" " ;
$def[1] .= "LINE1:var2#00ff00:\"out\" " ;
$def[1] .= "GPRINT:var2:LAST:\"%7.2lf %% last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%7.2lf %% avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%7.2lf %% max\" ";
?>

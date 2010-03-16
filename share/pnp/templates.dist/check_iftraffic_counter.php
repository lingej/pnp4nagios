<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_iftraffic.pl (COUNTER)
#
$opt[1]  = "--vertical-label \"Traffic\" -b 1024 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1]  = "DEF:var1=$RRDFILE[3]:$DS[3]:AVERAGE " ;
$def[1] .= "DEF:var2=$RRDFILE[4]:$DS[4]:AVERAGE " ;
$def[1] .= "LINE1:var1#003300:\"in\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%7.2lf %Sb/s max\\n\" " ;
$def[1] .= "LINE1:var2#00ff00:\"out\" " ;
$def[1] .= "GPRINT:var2:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%7.2lf %Sb/s max\" "
?>

<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_iftraffic.pl (COUNTER)
# $Id: check_iftraffic_counter.php 367 2008-01-23 18:10:31Z pitchfork $
#
#
$opt[1] = "--vertical-label \"Traffic\" -b 1024 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$rrdfile:$DS[3]:AVERAGE " ;
$def[1] .= "DEF:var2=$rrdfile:$DS[4]:AVERAGE " ;
$def[1] .= "LINE1:var1#003300:\"in\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%7.2lf %Sb/s max\\n\" " ;
$def[1] .= "LINE1:var2#00ff00:\"out\" " ;
$def[1] .= "GPRINT:var2:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%7.2lf %Sb/s max\" "
?>

<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_snmp_int.pl (COUNTER)
# $Id: check_snmp_int.php 378 2008-02-04 19:12:20Z pitchfork $
#
#
$opt[1] = " --vertical-label \"Traffic\" -b 1000 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "LINE1:var1#003300:\"in  \" " ;
$def[1] .= "GPRINT:var1:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%7.2lf %Sb/s max\\n\" " ;
$def[1] .= "LINE1:var2#00ff00:\"out \" " ;
$def[1] .= "GPRINT:var2:LAST:\"%7.2lf %Sb/s last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%7.2lf %Sb/s avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%7.2lf %Sb/s max\\n\" ";
if($NAGIOS_TIMET != ""){
    $def[1] .= "VRULE:".$NAGIOS_TIMET."#000000:\"Last Service Check \\n\" ";
}
if($NAGIOS_LASTHOSTDOWN != ""){
    $def[1] .= "VRULE:".$NAGIOS_LASTHOSTDOWN."#FF0000:\"Last Host Down\\n\" ";
}
?>

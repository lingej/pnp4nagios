<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_iftraffic.pl (COUNTER)
# Output based on Bits/s
# 
# $Id: check_snmp_int-bits.php 523 2008-09-26 17:10:20Z pitchfork $
#
#
$opt[1] = " --vertical-label \"Traffic\" -b 1000 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "CDEF:in_bits=var1,8,* ";
$def[1] .= "CDEF:out_bits=var2,8,* ";
$def[1] .= "LINE1:in_bits#003300:\"in  \" " ;
$def[1] .= "GPRINT:in_bits:LAST:\"%7.2lf %Sbit/s last\" " ;
$def[1] .= "GPRINT:in_bits:AVERAGE:\"%7.2lf %Sbit/s avg\" " ;
$def[1] .= "GPRINT:in_bits:MAX:\"%7.2lf %Sbit/s max\\n\" " ;
$def[1] .= "LINE1:out_bits#00ff00:\"out \" " ;
$def[1] .= "GPRINT:out_bits:LAST:\"%7.2lf %Sbit/s last\" " ;
$def[1] .= "GPRINT:out_bits:AVERAGE:\"%7.2lf %Sbit/s avg\" " ;
$def[1] .= "GPRINT:out_bits:MAX:\"%7.2lf %Sbit/s max\\n\" ";
if($NAGIOS_TIMET != ""){
    $def[1] .= "VRULE:".$NAGIOS_TIMET."#000000:\"Last Service Check \\n\" ";
}
if($NAGIOS_LASTHOSTDOWN != ""){
    $def[1] .= "VRULE:".$NAGIOS_LASTHOSTDOWN."#FF0000:\"Last Host Down\\n\" ";
}
?>

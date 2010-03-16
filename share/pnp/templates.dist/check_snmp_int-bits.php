<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_iftraffic.pl (COUNTER)
# Output based on Bits/s
#
#
$opt[1] = " --vertical-label \"Traffic\" -b 1000 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$RRDFILE[2]:$DS[2]:AVERAGE " ;
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
if($this->MACRO['TIMET'] != ""){
    $def[1] .= "VRULE:".$this->MACRO['TIMET']."#000000:\"Last Service Check \\n\" ";
}
?>

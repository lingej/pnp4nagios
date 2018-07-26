<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_snmp_int.pl (COUNTER)
#
$opt[1] = " --vertical-label \"Bytes\" -b 1000 --title \"Interface Traffic for $hostname / $servicedesc\" ";
$def[1] = "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "DEF:var2=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[1] .= "DEF:var3=$RRDFILE[3]:$DS[3]:AVERAGE " ;
$def[1] .= "DEF:var4=$RRDFILE[4]:$DS[4]:AVERAGE " ;
$def[1] .= "DEF:var5=$RRDFILE[5]:$DS[5]:AVERAGE " ;
$def[1] .= "DEF:var6=$RRDFILE[6]:$DS[6]:AVERAGE " ;
$def[1] .= "LINE1:var1#003300:\"in         \" " ;
$def[1] .= "GPRINT:var1:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%7.2lf %SB/s max\\n\" " ;
$def[1] .= "LINE1:var2#00ff00:\"out        \" " ;
$def[1] .= "GPRINT:var2:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var2:MAX:\"%7.2lf %SB/s max\\n\" ";
$def[1] .= "LINE1:var3#dddd00:\"error in   \" " ;
$def[1] .= "GPRINT:var3:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var3:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var3:MAX:\"%7.2lf %SB/s max\\n\" ";
$def[1] .= "LINE1:var4#00dddd:\"discard in \" " ;
$def[1] .= "GPRINT:var4:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var4:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var4:MAX:\"%7.2lf %SB/s max\\n\" ";
$def[1] .= "LINE1:var5#dd00dd:\"error out  \" " ;
$def[1] .= "GPRINT:var5:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var5:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var5:MAX:\"%7.2lf %SB/s max\\n\" ";
$def[1] .= "LINE1:var6#dd0000:\"discard out\" " ;
$def[1] .= "GPRINT:var6:LAST:\"%7.2lf %SB/s last\" " ;
$def[1] .= "GPRINT:var6:AVERAGE:\"%7.2lf %SB/s avg\" " ;
$def[1] .= "GPRINT:var6:MAX:\"%7.2lf %SB/s max\\n\" ";
if($this->MACRO['TIMET'] != ""){
    $def[1] .= "VRULE:".$this->MACRO['TIMET']."#000000:\"Last Service Check \\n\" ";
}
?>

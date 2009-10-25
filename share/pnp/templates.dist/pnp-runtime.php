<?php
#
# Copyright (c) 2006-2009 Joerg Linge (http://www.pnp4nagios.org)
# PNP Runtime Informations 
#
# Runtime 
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"Runtime of process_perfdata.pl\" ";
#
#
#
#$s=$this->STRUCT['TIMERANGE']['end'];
$ds_name[1] = "Runtime";
$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "CDEF:t_var1=var1,14400,TREND ";
if ($WARN[1] != "") {
	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
$def[1] .= "AREA:var1#33cccc:\"Runtime\\t\" " ;
$def[1] .= "LINE1:var1#339999 " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[1] last\\t\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[1] avg\\t\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[1] max\\n\" ";
$def[1] .= "LINE1:t_var1#ff9999:\"4h trend\\t\" " ;
$def[1] .= "GPRINT:t_var1:LAST:\"%6.2lf$UNIT[1] last\\t\" " ;
$def[1] .= "GPRINT:t_var1:AVERAGE:\"%6.2lf$UNIT[1] avg\\t\" " ;
$def[1] .= "GPRINT:t_var1:MAX:\"%6.2lf$UNIT[1] max\\n\" ";


#
# Lines processed and RRD errors 
#
$opt[2] = "--vertical-label \"Counter\" --title \"Number of updates per run\" ";
#
#
#
$ds_name[2] = "Errors and updates per run";
$def[2] =  "DEF:var1=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[2] .= "DEF:var2=$RRDFILE[3]:$DS[3]:AVERAGE " ;
$def[2] .= "DEF:var3=$RRDFILE[4]:$DS[4]:AVERAGE " ;
$def[2] .= "DEF:var4=$RRDFILE[5]:$DS[5]:AVERAGE " ;
$def[2] .= "AREA:var1#6666ff:\"Updates\\t\" " ;
$def[2] .= "LINE1:var1#0000CC " ;
$def[2] .= "GPRINT:var1:LAST:\"%.0lf$UNIT[2] last\\t\" " ;
$def[2] .= "GPRINT:var1:AVERAGE:\"%.0lf$UNIT[2] avg\\t\" " ;
$def[2] .= "GPRINT:var1:MAX:\"%.0lf$UNIT[2] max\\n\" ";
$def[2] .= "LINE1:var2#ff3333:\"Errors\\t\" " ;
$def[2] .= "GPRINT:var2:LAST:\"%.0lf$UNIT[3] last\\t\" " ;
$def[2] .= "GPRINT:var2:AVERAGE:\"%.0lf$UNIT[3] avg\\t\" " ;
$def[2] .= "GPRINT:var2:MAX:\"%.0lf$UNIT[3] max\\n\" ";
$def[2] .= "LINE1:var3#ffff33:\"Invalid\\t\" " ;
$def[2] .= "GPRINT:var3:LAST:\"%.0lf$UNIT[4] last\\t\" " ;
$def[2] .= "GPRINT:var3:AVERAGE:\"%.0lf$UNIT[4] avg\\t\" " ;
$def[2] .= "GPRINT:var3:MAX:\"%.0lf$UNIT[4] max\\n\" ";
$def[2] .= "LINE1:var4#66ff33:\"Skipped\\t\" " ;
$def[2] .= "GPRINT:var4:LAST:\"%.0lf$UNIT[5] last\\t\" " ;
$def[2] .= "GPRINT:var4:AVERAGE:\"%.0lf$UNIT[5] avg\\t\" " ;
$def[2] .= "GPRINT:var4:MAX:\"%.0lf$UNIT[5] max\\n\" ";


?>

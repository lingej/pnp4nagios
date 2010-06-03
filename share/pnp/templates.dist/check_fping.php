<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_fping [Multigraph]
#
# RTA
#
$ds_name[1] = "Round Trip Times";
$opt[1]  =  "--vertical-label \"RTA\"  --title \"Ping times\" ";

$def[1]  =  "DEF:var1=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[1] .=  "CDEF:sp1=var1,100,/,12,* " ;
$def[1] .=  "CDEF:sp2=var1,100,/,30,* " ;
$def[1] .=  "CDEF:sp3=var1,100,/,50,* " ;
$def[1] .=  "CDEF:sp4=var1,100,/,70,* " ;

$def[1] .= "AREA:var1#FF5C00:\"Round Trip Times \" " ;
$def[1] .= "AREA:sp4#FF7C00: " ;
$def[1] .= "AREA:sp3#FF9C00: " ;
$def[1] .= "AREA:sp2#FFBC00: " ;
$def[1] .= "AREA:sp1#FFDC00: " ;

$def[1] .= "GPRINT:var1:LAST:\"%6.2lf $UNIT[2] last \" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf $UNIT[2] max \" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf $UNIT[2] avg \\n\" " ;
$def[1] .= "LINE1:var1#000000:\"\" " ;
if($WARN[2] != ""){
  $def[1] .= "HRULE:".$WARN[2]."#000000:\"Warning ".$WARN[2].$UNIT[2]." \" " ;
}
if($CRIT[2] != ""){
  $def[1] .= "HRULE:".$CRIT[2]."#FF0000:\"Critical ".$CRIT[2].$UNIT[2]." \" " ;
}
#
# Packets Lost
if (array_key_exists(2,$DS)) {
    
    $ds_name[2] = "Packets Lost";
    $opt[2] = "--vertical-label \"Packets lost\" -l0 -u105 --title \"Packets lost\" ";

    $def[2]  =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
    $def[2] .=  "CDEF:sp1=var1,100,/,12,* " ;
    $def[2] .=  "CDEF:sp2=var1,100,/,30,* " ;
    $def[2] .=  "CDEF:sp3=var1,100,/,50,* " ;
    $def[2] .=  "CDEF:sp4=var1,100,/,70,* " ;

    $def[2] .= "AREA:var1#FF5C00:\"Packets lost \" " ;
    $def[2] .= "AREA:sp4#FF7C00: " ;
    $def[2] .= "AREA:sp3#FF9C00: " ;
    $def[2] .= "AREA:sp2#FFBC00: " ;
    $def[2] .= "AREA:sp1#FFDC00: " ;

    $def[2] .= "GPRINT:var1:LAST:\"%6.2lg $UNIT[1] last \" " ;
    $def[2] .= "GPRINT:var1:MAX:\"%6.2lg $UNIT[1] max \" " ;
    $def[2] .= "GPRINT:var1:AVERAGE:\"%6.2lg $UNIT[1] avg \\n\" " ;
    $def[2] .= "LINE1:var1#000000: " ;
    $def[2] .= "HRULE:100#000000:\"\" " ;
    if($WARN[1] != ""){
        $def[2] .= "HRULE:".$WARN[1]."#FFFF00:\"Warning ".$WARN[1].$UNIT[1]." \" " ;
    }
    if($CRIT[1] != ""){
        $def[2] .= "HRULE:".$CRIT[1]."#FF0000:\"Critical ".$CRIT[1].$UNIT[1]." \" " ;
    }
}
?>


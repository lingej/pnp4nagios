<?php
#
# Contributed by Mathias Kettner
# Plugin: check_ntp
#
$range = $CRIT[1];

$opt[1] = "--vertical-label 'offset (s)' -l -$range  -u $range --title '$hostname: NTP time offset' ";

$warn_ms = $WARN[1] * 1000.0;
$crit_ms = $CRIT[1] * 1000.0;

$def[1] = "DEF:offset=$RRDFILE[1]:$DS[1]:MAX "; 
$def[1] .= "CDEF:ms=offset,1000,* ";
$def[1] .= "CDEF:msabs=ms,ABS ";
$def[1] .= "AREA:offset#00ffc6:\"time offset to monitoring server \" "; 
$def[1] .= "LINE1:offset#226600: "; 
$def[1] .= "HRULE:$WARN[1]#ffff00:\"\" ";
$def[1] .= "HRULE:-$WARN[1]#ffff00:\"Warning\\: +/- $warn_ms ms \" ";
$def[1] .= "HRULE:$CRIT[1]#ff0000:\"\" ";       
$def[1] .= "HRULE:-$CRIT[1]#ff0000:\"Critical\\: +/- $crit_ms ms \\n\" ";       
$def[1] .= "GPRINT:ms:LAST:\"current\: %.1lf ms\" ";
$def[1] .= "GPRINT:msabs:MAX:\"max(+/-)\: %.1lf ms \" ";
$def[1] .= "GPRINT:msabs:AVERAGE:\"avg(+/-)\: %.1lf ms\" ";
?>

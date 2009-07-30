<?php
# +------------------------------------------------------------------+
# |           _           _           _       _   __   _____         |
# |        __| |_  ___ __| |__  _ __ | |__   / | /  \ |__ / |        |
# |       / _| ' \/ -_) _| / / | '  \| / /   | || () | |_ \ |        |
# |       \__|_||_\___\__|_\_\_|_|_|_|_\_\   |_(_)__(_)___/_|        |
# |                                            check_mk 1.0.31       |
# |                                                                  |
# | Copyright Mathias Kettner 2009                mk@mathias-kettner |
# +------------------------------------------------------------------+
# 
# This file is part of check_mk 1.0.31.
# The official homepage is at http://mathias-kettner.de/check_mk.
# 
# check_mk is free software;  you can redistribute it and/or modify it
# under the  terms of the  GNU General Public License  as published by
# the Free Software Foundation in version 2.  check_mk is  distributed
# in the hope that it will be useful, but WITHOUT ANY WARRANTY;  with-
# out even the implied warranty of  MERCHANTABILITY  or  FITNESS FOR A
# PARTICULAR PURPOSE. See the  GNU General Public License for more de-
# ails.  You should have  received  a copy of the  GNU  General Public
# License along with GNU Make; see the file  COPYING.  If  not,  write
# to the Free Software Foundation, Inc., 51 Franklin St,  Fifth Floor,
# Boston, MA 02110-1301 USA.

#
# Datensatze:
#    1: rx_bytes
#    2: tx_bytes
#    3: rx_packets
#    4: tx_packets
#    5: rx_errors
#    6: tx_errors
#    7: tx_collisions

$x = explode("_", $servicedesc);
$nic = $x[1];
$opt[1] = "--slope-mode --vertical-label 'Byte/s' -l0 --title \"$hostname / NIC $nic\" ";
#
#
#
$def[1] =  "DEF:rx_bytes=$RRDFILE[1]:$DS[1]:MAX " ;
$def[1] .= "DEF:tx_bytes=$RRDFILE[1]:$DS[2]:MAX " ;
$def[1] .= "CDEF:rx_mb=rx_bytes,8,*,100000000,GT,UNKN,rx_bytes,8,*,IF " ;
$def[1] .= "CDEF:tx_mb=tx_bytes,8,*,100000000,GT,UNKN,tx_bytes,8,*,IF " ; 
$def[1] .= "DEF:rx_errors=$RRDFILE[5]:$DS[5]:MAX " ;
$def[1] .= "DEF:tx_errors=$RRDFILE[6]:$DS[6]:MAX " ;
$def[1] .= "DEF:tx_collisions=$RRDFILE[7]:$DS[7]:MAX " ;
$def[1] .= "CDEF:errors=rx_errors,tx_errors,+ ";
$def[1] .= "CDEF:problems_x=errors,tx_collisions,+ ";
$def[1] .= "CDEF:problems=problems_x,1000000,* "; # Skaliere Probleme hoch, damit man was sieht

$def[1] .= "AREA:problems#ff0000:\"Errors \\t\" " ;
$def[1] .= "GPRINT:problems:LAST:\"%.0lf/s\\n\" " ;
$def[1] .= "LINE:rx_mb#2060a0:\"Receive \\t\" " ;
$def[1] .= "GPRINT:rx_mb:LAST:\"%.1lf %sbit/s\\n\" " ;
$def[1] .= "LINE:tx_mb#60a020:\"Transmit \\t\" " ;
$def[1] .= "GPRINT:tx_mb:LAST:\"%.1lf %sbit/s\\n\" " ;

?>

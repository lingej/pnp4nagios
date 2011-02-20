<?php
#
# Copyright (c) 2009 Gerhard Lausser (gerhard.lausser@consol.de)
# Plugin: check_mysql_health (http://www.consol.com/opensource/nagios/check-mysql-health)
# Release 1.0 2009-03-02
#
# This is a template for the visualisation addon PNP (http://www.pnp4nagios.org)
#

$def[1] = "";
$opt[1] = "";

$defcnt = 1;

$green = "33FF00E0";
$yellow = "FFFF00E0";
$red = "F83838E0";
$now = "FF00FF";

foreach ($DS as $i) {
    $warning = ($WARN[$i] != "") ? $WARN[$i] : "";
    $warnmin = ($WARN_MIN[$i] != "") ? $WARN_MIN[$i] : "";
    $warnmax = ($WARN_MAX[$i] != "") ? $WARN_MAX[$i] : "";
    $critical = ($CRIT[$i] != "") ? $CRIT[$i] : "";
    $critmin = ($CRIT_MIN[$i] != "") ? $CRIT_MIN[$i] : "";
    $critmax = ($CRIT_MAX[$i] != "") ? $CRIT_MAX[$i] : "";
    $minimum = ($MIN[$i] != "") ? $MIN[$i] : "";
    $maximum = ($MAX[$i] != "") ? $MAX[$i] : "";

    if(preg_match('/^connection_time$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Time to connect";
        $opt[$defcnt] = "--vertical-label \"Seconds\" --title \"Time to establish a connection to $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:connectiontime=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:connectiontime#111111 ";
        $def[$defcnt] .= "VDEF:vconnetiontime=connectiontime,LAST " ;
        $def[$defcnt] .= "GPRINT:vconnetiontime:\"is %3.2lf Seconds \" " ;
        $defcnt++;
    }
    if(preg_match('/^uptime$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Uptime";
        $opt[$defcnt] = "--vertical-label \"Seconds\" --title \"Uptime of the database at $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:uptime=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:uptime#111111 ";
        $def[$defcnt] .= "CDEF:uptimed=uptime,86400,/ " ;
        $def[$defcnt] .= "CDEF:uptimew=uptimed,7,/ " ;
        $def[$defcnt] .= "VDEF:vuptime=uptime,LAST " ;
        $def[$defcnt] .= "VDEF:vuptimed=uptimed,LAST " ;
        $def[$defcnt] .= "VDEF:vuptimew=uptimew,LAST " ;
        $def[$defcnt] .= "GPRINT:vuptime:\"%.0lf Seconds \" " ;
        $def[$defcnt] .= "GPRINT:vuptimed:\"%.0lf Days \" " ;
        $def[$defcnt] .= "GPRINT:vuptimew:\"%.0lf Weeks \" " ;
        $defcnt++;
    }
    if(preg_match('/^index_usage_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Index usage";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Index usage $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^index_usage$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:indexusage=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=indexusage,$CRIT_MIN[$ii],LE,indexusage,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,indexusage,0,IF ";
            $def[$defcnt] .= "CDEF:ay=indexusage,$WARN_MIN[$ii],LE,indexusage,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,indexusage,0,IF ";
            $def[$defcnt] .= "CDEF:ag=indexusage,100,LE,indexusage,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,indexusage,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE:indexusage#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vindexusage=indexusage,LAST " ;
            $def[$defcnt] .= "GPRINT:vindexusage:\"Index usage (since epoch) is %3.2lf percent\\n\" " ;
          }
          if(preg_match('/^index_usage_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:indexusagenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:indexusagenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vindexusagenow=indexusagenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vindexusagenow:\"Index usage (current) is %3.2lf percent\\n\" ";
          }
        }
        $defcnt++;
    }
    if(preg_match('/^bufferpool_hitrate_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Innodb buffer pool hitrate";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Innodb buffer pool hitrate on $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^bufferpool_hitrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitrate=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=hitrate,$CRIT_MIN[$ii],LE,hitrate,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ay=hitrate,$WARN_MIN[$ii],LE,hitrate,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ag=hitrate,100,LE,hitrate,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE1.5:hitrate#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vhitrate=hitrate,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitrate:\"Hitratio (since epoch) is %3.2lf percent \\n\" ";
          }
          if(preg_match('/^bufferpool_hitrate_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitratenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:hitratenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vhitratenow=hitratenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitratenow:\"Hitratio (current) is %3.2lf percent \\n\" ";
          }
        }
        $defcnt++;
    }
    if(preg_match('/^bufferpool_free_waits_rate$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Innodb buffer pool waits rate";
        $opt[$defcnt] = "--vertical-label \"Waits/sec\" --title \"Innodb buffer pool waits on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:logwait=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:logwait#111111 ";
        $def[$defcnt] .= "VDEF:vlogwait=logwait,LAST " ;
        $def[$defcnt] .= "GPRINT:vlogwait:\"Rate is %3.2lf Waits / Second \" " ;
        $defcnt++;
    }
    if(preg_match('/^innodb_log_waits_rate$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Innodb log buffer waits rate";
        $opt[$defcnt] = "--vertical-label \"Waits/sec\" --title \"Innodb waits for log buffer $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:logwait=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:logwait#111111 ";
        $def[$defcnt] .= "VDEF:vlogwait=logwait,LAST " ;
        $def[$defcnt] .= "GPRINT:vlogwait:\"Rate is %3.2lf Waits / Second \" " ;
        $defcnt++;
    }
    if(preg_match('/^long_running_procs$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Long running processes";
        $opt[$defcnt] = "--vertical-label \"Processes\" --title \"Long running processes (>60s) on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:longrun=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:longrun#111111 ";
        $def[$defcnt] .= "VDEF:vlongrun=longrun,LAST " ;
        $def[$defcnt] .= "GPRINT:vlongrun:\"%.0lf long running processes \" " ;
        $defcnt++;
    }
    if(preg_match('/^keycache_hitrate_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "MyISAM key cache hitrate";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"MyISAM key cache hitrate on $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^keycache_hitrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitrate=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=hitrate,$CRIT_MIN[$ii],LE,hitrate,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ay=hitrate,$WARN_MIN[$ii],LE,hitrate,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ag=hitrate,100,LE,hitrate,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE1.5:hitrate#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vhitrate=hitrate,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitrate:\"Hitratio (since epoch) is %3.2lf percent \\n\" ";
          }
          if(preg_match('/^keycache_hitrate_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitratenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:hitratenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vhitratenow=hitratenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitratenow:\"Hitratio (current) is %3.2lf percent \\n\" ";
          }
        }
        $defcnt++;
    }
    if(preg_match('/^qcache_hitrate_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Query cache hitrate";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Query cache hitrate on $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = ""; 
        foreach ($DS as $ii) {
          if(preg_match('/^qcache_hitrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitrate=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=hitrate,$CRIT_MIN[$ii],LE,hitrate,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ay=hitrate,$WARN_MIN[$ii],LE,hitrate,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ag=hitrate,100,LE,hitrate,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE1.5:hitrate#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vhitrate=hitrate,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitrate:\"Hitratio (since epoch) is %3.2lf percent \\n\" ";
          }   
          if(preg_match('/^qcache_hitrate_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitratenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:hitratenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vhitratenow=hitratenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitratenow:\"Hitratio (current) is %3.2lf percent \\n\" ";
          }   
        }   
        $defcnt++;
        $ds_name[$defcnt] = "Selects per second";
        $opt[$defcnt] = "--vertical-label \"Selects / sec\" --title \"Selects per second on $hostname\" ";
        $def[$defcnt] = ""; 
        foreach ($DS as $ii) {
          if(preg_match('/^selects_per_sec$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:sps=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "AREA:sps#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vsps=sps,LAST " ;
            $def[$defcnt] .= "GPRINT:vsps:\"%3.2lf Selects per second \\n\" ";
          }   
        }
        $defcnt++;
    }   
    if(preg_match('/^qcache_lowmem_prunes_rate$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Query cache low memory prunes";
        $opt[$defcnt] = "--vertical-label \"Prunes / sec\" --title \"Query cache low mem prunes on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:prunes=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:prunes#111111 ";
        $def[$defcnt] .= "VDEF:vprunes=prunes,LAST " ;
        $def[$defcnt] .= "GPRINT:vprunes:\"Rate is %3.2lf Prunes / Second \" " ;
        $defcnt++;
    }
    if(preg_match('/^slow_queries_rate$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Slow query rate";
        $opt[$defcnt] = "--vertical-label \"Slow queries / sec\" --title \"Slow queries on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:prunes=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:prunes#111111 ";
        $def[$defcnt] .= "VDEF:vprunes=prunes,LAST " ;
        $def[$defcnt] .= "GPRINT:vprunes:\"%3.2lf Slow queries / Second \" " ;
        $defcnt++;
    }
    if(preg_match('/^tablelock_contention_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Table lock contention";
        # set upper limit to 10, because 3 means an already dead database
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Table lock contention on $hostname\" --upper-limit 10 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^tablelock_contention$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:tbllckcont=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ag=tbllckcont,$WARN[$ii],LE,tbllckcont,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,tbllckcont,0,IF ";
            $def[$defcnt] .= "CDEF:ay=tbllckcont,$CRIT[$ii],LE,tbllckcont,$WARN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,tbllckcont,0,IF ";
            $def[$defcnt] .= "CDEF:ar=tbllckcont,100,LE,tbllckcont,$CRIT[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,tbllckcont,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE:tbllckcont#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vtbllckcont=tbllckcont,LAST " ;
            $def[$defcnt] .= "GPRINT:vtbllckcont:\"Lock contention (since epoch) is %3.2lf%%\\n\" " ;
          }
          if(preg_match('/^tablelock_contention_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:tbllckcontnow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:tbllckcontnow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vtbllckcontnow=tbllckcontnow,LAST " ;
            $def[$defcnt] .= "GPRINT:vtbllckcontnow:\"Lock contention (current) is %3.2lf%%\" ";
          }   
        }
        $defcnt++;
    }
    if(preg_match('/^tablecache_fillrate$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Table cache hitrate";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Table cache hitrate on $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^tablecache_hitrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitrate=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=hitrate,$CRIT_MIN[$ii],LE,hitrate,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ay=hitrate,$WARN_MIN[$ii],LE,hitrate,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ag=hitrate,100,LE,hitrate,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE:hitrate#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vhitrate=hitrate,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitrate:\"Hitratio is %3.2lf percent \\n\" ";
          }
          if(preg_match('/^tablecache_fillrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitratenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:hitratenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vhitratenow=hitratenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitratenow:\"%3.2lf%% of the cache is filled \\n\" ";
          }
        }
        $defcnt++;
    }
    if(preg_match('/^pct_tmp_table_on_disk_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Temporary tables created on disk ";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Temporary tables created on disk on $hostname\" --upper-limit 10 --lower-limit 0 ";
        $def[$defcnt] = "";
        foreach ($DS as $ii) {
          if(preg_match('/^pct_tmp_table_on_disk$/', $NAME[$ii])) {

            $def[$defcnt] .= "DEF:tmptbldsk=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ag=tmptbldsk,$WARN[$ii],LE,tmptbldsk,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,tmptbldsk,0,IF ";
            $def[$defcnt] .= "CDEF:ay=tmptbldsk,$CRIT[$ii],LE,tmptbldsk,$WARN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,tmptbldsk,0,IF ";
            $def[$defcnt] .= "CDEF:ar=tmptbldsk,100,LE,tmptbldsk,$CRIT[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,tmptbldsk,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE:tmptbldsk#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vtmptbldsk=tmptbldsk,LAST " ;
            $def[$defcnt] .= "GPRINT:vtmptbldsk:\"%3.2lf percent of temp tables were created on disk (since epoch)\\n\" " ;
          }
          if(preg_match('/^pct_tmp_table_on_disk_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:tmptbldsknow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:tmptbldsknow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vtmptbldsknow=tmptbldsknow,LAST " ;
            $def[$defcnt] .= "GPRINT:vtmptbldsknow:\"%3.2lf percent of temp tables were created on disk (recently)\\n\" " ;
          }   
        }
        $defcnt++;
    }
    if(preg_match('/^thread_cache_hitrate_now$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Thread cache hitrate";
        $opt[$defcnt] = "--vertical-label \"Percent\" --title \"Thread cache hitrate on $hostname\" --upper-limit 100 --lower-limit 0 ";
        $def[$defcnt] = ""; 
        foreach ($DS as $ii) {
          if(preg_match('/^thread_cache_hitrate$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitrate=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "CDEF:ar=hitrate,$CRIT_MIN[$ii],LE,hitrate,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ay=hitrate,$WARN_MIN[$ii],LE,hitrate,$CRIT_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "CDEF:ag=hitrate,100,LE,hitrate,$WARN_MIN[$ii],GT,INF,UNKN,IF,UNKN,IF,ISINF,hitrate,0,IF ";
            $def[$defcnt] .= "AREA:ag#$green: " ;
            $def[$defcnt] .= "AREA:ay#$yellow: " ;
            $def[$defcnt] .= "AREA:ar#$red: " ;
            $def[$defcnt] .= "LINE:hitrate#111111:\" \" ";
            $def[$defcnt] .= "VDEF:vhitrate=hitrate,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitrate:\"Hitratio (since epoch) is %3.2lf percent \\n\" ";
          }   
          if(preg_match('/^thread_cache_hitrate_now$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:hitratenow=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "LINE1.5:hitratenow#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vhitratenow=hitratenow,LAST " ;
            $def[$defcnt] .= "GPRINT:vhitratenow:\"Hitratio (current) is %3.2lf percent \\n\" ";
          }   
        }   
        $defcnt++;
        $ds_name[$defcnt] = "Connects per second";
        $opt[$defcnt] = "--vertical-label \"Conects / sec\" --title \"Connects per second on $hostname\" ";
        $def[$defcnt] = ""; 
        foreach ($DS as $ii) {
          if(preg_match('/^connections_per_sec$/', $NAME[$ii])) {
            $def[$defcnt] .= "DEF:sps=$RRDFILE[$ii]:$DS[$ii]:AVERAGE:reduce=LAST " ;
            $def[$defcnt] .= "AREA:sps#$now:\" \" ";
            $def[$defcnt] .= "VDEF:vsps=sps,LAST " ;
            $def[$defcnt] .= "GPRINT:vsps:\"%3.2lf Connects per second \\n\" ";
          }   
        }
        $defcnt++;
    }   
    if(preg_match('/^threads_connected$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Connection threads";
        $opt[$defcnt] = "--vertical-label \"Threads\" --title \"Connection threads on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:threads=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:threads#111111 ";
        $def[$defcnt] .= "VDEF:vthreads=threads,LAST " ;
        $def[$defcnt] .= "GPRINT:vthreads:\"%.0lf Connection threads \" " ;
        $defcnt++;
    }
    if(preg_match('/^threads_running$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Running threads";
        $opt[$defcnt] = "--vertical-label \"Threads\" --title \"Running threads on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:threads=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:threads#111111 ";
        $def[$defcnt] .= "VDEF:vthreads=threads,LAST " ;
        $def[$defcnt] .= "GPRINT:vthreads:\"%.0lf Running threads \" " ;
        $defcnt++;
    }
    if(preg_match('/^threads_cached$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Cached threads";
        $opt[$defcnt] = "--vertical-label \"Threads\" --title \"Cached threads on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:threads=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:threads#111111 ";
        $def[$defcnt] .= "VDEF:vthreads=threads,LAST " ;
        $def[$defcnt] .= "GPRINT:vthreads:\"%.0lf Cached threads \" " ;
        $defcnt++;
    }
    if(preg_match('/^pct_open_files$/', $NAME[$i])) {
        $ds_name[$defcnt] = "PCT Open Files";
        $opt[$defcnt] = "--vertical-label \"OpenFiles\" --title \"PCT Open Files $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= "DEF:threads=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:threads#111111 ";
        $def[$defcnt] .= "VDEF:vthreads=threads,LAST " ;
        $def[$defcnt] .= "GPRINT:vthreads:\"%.0lf Open Files \" " ;
        $defcnt++;
    }
    if(preg_match('/^threads_created_per_sec$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Created thread per second";
        $opt[$defcnt] = "--vertical-label \"Created threads / sec\" --title \"Created threads per second on $hostname\" ";
        $def[$defcnt] = ""; 
        $def[$defcnt] .= "DEF:sps=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:sps#$now:\" \" ";
        $def[$defcnt] .= "VDEF:vsps=sps,LAST " ;
        $def[$defcnt] .= "GPRINT:vsps:\"%3.2lf Created threads per second \\n\" ";
        }
    if(preg_match('/^connects_aborted_per_sec$/', $NAME[$i])) {
        $ds_name[$defcnt] = "Aborted Connects per second";
        $opt[$defcnt] = "--vertical-label \"Aborted connects / sec\" --title \"Aborted Connects per second on $hostname\" ";
        $def[$defcnt] = ""; 
        $def[$defcnt] .= "DEF:sps=$RRDFILE[$i]:$DS[$i]:AVERAGE:reduce=LAST " ;
        $def[$defcnt] .= "AREA:sps#$now:\" \" ";
        $def[$defcnt] .= "VDEF:vsps=sps,LAST " ;
        $def[$defcnt] .= "GPRINT:vsps:\"%3.2lf Aborted Connects per second \\n\" ";
        }
}
?>


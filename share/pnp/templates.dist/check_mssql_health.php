<?php
#
# Copyright (c) 2009-2011 Gerhard Lausser (gerhard.lausser@consol.de)
# Copyright (c) 2011 Joerg Linge (support@pnp4nagios.org)
# Plugin: check_mysql_health (http://www.consol.com/opensource/nagios/check-mysql-health)
#

$defcnt = 1;

$green = "33FF00E0";
$yellow = "FFFF00E0";
$red = "F83838E0";
$now = "FF00FF";

foreach ($this->DS as $KEY=>$VAL) {
    $warning  = ($VAL['WARN'] != "") ? $VAL['WARN'] : "";
    $warnmin  = ($VAL['WARN_MIN'] != "") ? $VAL['WARN_MIN'] : "";
    $warnmax  = ($VAL['WARN_MAX'] != "") ? $VAL['WARN_MAX'] : "";
    $critical = ($VAL['CRIT'] != "") ? $VAL['CRIT'] : "";
    $critmin  = ($VAL['CRIT_MIN'] != "") ? $VAL['CRIT_MIN'] : "";
    $critmax  = ($VAL['CRIT_MAX'] != "") ? $VAL['CRIT_MAX'] : "";
    $minimum  = ($VAL['MIN'] != "") ? $VAL['MIN'] : "";
    $maximum  = ($VAL['MAX'] != "") ? $VAL['MAX'] : "";

    if(preg_match('/^connection_time$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Time to connect";
        $opt[$defcnt] = "--vertical-label \"Seconds\" --title \"Time to establish a connection to $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("connectiontime",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::area("connectiontime","#111111");
        $def[$defcnt] .= rrd::gprint("connectiontime",array("LAST", "MAX", "AVERAGE"),"%3.2lf Seconds") ;
        $defcnt++;
    }
    if(preg_match('/^cpu_busy$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "CPU Busy Time";
        $opt[$defcnt] = "--vertical-label \"%\" --title \"CPU busy time on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("cpubusy",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::cdef("ag","cpubusy,".$VAL['WARN'].",LE,cpubusy,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,cpubusy,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","cpubusy,".$VAL['CRIT'].",LE,cpubusy,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,cpubusy,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","cpubusy,100,LE,cpubusy,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,cpubusy,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("cpubusy","#111111");
        $def[$defcnt] .= rrd::gprint("cpubusy","LAST","CPU is busy for %3.2lf percent of the time\\n");
        $defcnt++;
    }
    if(preg_match('/^io_busy$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "IO Busy Time";
        $opt[$defcnt] = "--vertical-label \"%\" --title \"IO busy time on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("iobusy",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::cdef("ag","iobusy,".$VAL['WARN'].",LE,iobusy,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,iobusy,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","iobusy,".$VAL['CRIT'].",LE,iobusy,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,iobusy,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","iobusy,100,LE,iobusy,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,iobusy,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("iobusy","#111111");
        $def[$defcnt] .= rrd::gprint("iobusy","LAST","IO is busy for %3.2lf percent of the time\\n");
        $defcnt++;
    }
    if(preg_match('/^full_scans_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Full Table Scans / Sec";
        $opt[$defcnt] = "--vertical-label \"scans / sec\" --title \"Full table scans / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("fullscans",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","fullscans,".$VAL['WARN'].",LE,fullscans,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,fullscans,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","fullscans,".$VAL['CRIT'].",LE,fullscans,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,fullscans,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","fullscans,INF,LE,fullscans,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,fullscans,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("fullscans","#000000", "Full table scans");
        $def[$defcnt] .= rrd::gprint("fullscans",array("MAX", "AVERAGE", "LAST"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^connected_users$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Connected Users";
        $opt[$defcnt] = "--vertical-label \"Users\" --title \"Users connected to $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("users",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","users,".$VAL['WARN'].",LE,users,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,users,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","users,".$VAL['CRIT'].",LE,users,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,users,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","users,INF,LE,users,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,users,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("users","#000000","connected users");
        $def[$defcnt] .= rrd::gprint("users",array("LAST","MAX","AVERAGE"),"%.0lf");
        $defcnt++;
    }

    if(preg_match('/^(.*)_transactions_per_sec/', $VAL['NAME'], $match)) {
        $dsname = $match[1];
        if(empty($opt[1])){
            $opt[1] = "--vertical-label \"Transactions/s\" -l0 --title \"Database transactions / sec\" ";
        }
        if(empty($def[1])){
             $def[1] = "";
        }
        $ds_name[1] = "Transactions Per Seconds ";
        $def[1] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[1] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[1] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.6lf %S" );

    }

    if(preg_match('/^latch_waits_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Latch Waits / Sec";
        $opt[$defcnt] = "--vertical-label \"waits / sec\" --title \"Latch waits / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("waits",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","waits,".$VAL['WARN'].",LE,waits,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,waits,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","waits,".$VAL['CRIT'].",LE,waits,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,waits,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","waits,INF,LE,waits,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,waits,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("waits","#000000", "waits/s");
        $def[$defcnt] .= rrd::gprint("waits",array("MAX","AVERAGE","LAST"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^latch_avg_wait_time$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Latch Wait Time";
        $opt[$defcnt] = "--vertical-label \"msec\" --title \"Latch avg wait time on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("waittime",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","waittime,".$VAL['WARN'].",LE,waittime,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,waittime,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","waittime,".$VAL['CRIT'].",LE,waittime,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,waittime,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","waittime,INF,LE,waittime,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,waittime,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("waittime","#000000");
        $def[$defcnt] .= rrd::gprint("waittime",array("MAX", "AVERAGE", "LAST"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^sql_initcompilations_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Initial Compilations";
        $opt[$defcnt] = "--vertical-label \"initcomps/s\" --title \"Initial compilations / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("comps",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","comps,".$VAL['WARN'].",LE,comps,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","comps,".$VAL['CRIT'].",LE,comps,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","comps,INF,LE,comps,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("comps","#000000","Compilations");
        $def[$defcnt] .= rrd::gprint("comps",array("MAX", "AVERAGE", "LAST"),"%3.2lf/s");
        $defcnt++;
    }
    if(preg_match('/^sql_recompilations_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Re-Compilations";
        $opt[$defcnt] = "--vertical-label \"re-comps/s\" --title \"Re-Compilations / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("comps",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","comps,".$VAL['WARN'].",LE,comps,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","comps,".$VAL['CRIT'].",LE,comps,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","comps,INF,LE,comps,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,comps,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("comps","#000000","Re-Compilations");
        $def[$defcnt] .= rrd::gprint("comps",array("MAX", "AVERAGE", "LAST"),"%3.2lf/s");
        $defcnt++;
    }
    if(preg_match('/^batch_requests_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Batch Requests";
        $opt[$defcnt] = "--vertical-label \"batchreqs/s\" --title \"Batch requests / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("breqs",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","breqs,".$VAL['WARN'].",LE,breqs,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,breqs,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","breqs,".$VAL['CRIT'].",LE,breqs,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,breqs,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","breqs,INF,LE,breqs,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,breqs,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("breqs","#000000","Batch Requests");
        $def[$defcnt] .= rrd::gprint("breqs",array("LAST","AVERAGE","MAX"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^checkpoint_pages_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Checkpoint Pages";
        $opt[$defcnt] = "--vertical-label \"pages/s\" --title \"Flushed pages / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("pages",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","pages,".$VAL['WARN'].",LE,pages,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,pages,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","pages,".$VAL['CRIT'].",LE,pages,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,pages,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","pages,INF,LE,pages,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,pages,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("pages","#000000", "pages flushed");
        $def[$defcnt] .= rrd::gprint("pages",array("LAST","AVERAGE","MAX"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^free_list_stalls_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Free List Stalls";
        $opt[$defcnt] = "--vertical-label \"stalls/s\" --title \"Free list stalls / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("stalls",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","stalls,".$VAL['WARN'].",LE,stalls,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,stalls,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","stalls,".$VAL['CRIT'].",LE,stalls,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,stalls,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","stalls,INF,LE,stalls,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,stalls,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green");
        $def[$defcnt] .= rrd::area("ay","#$yellow");
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("stalls","#000000", "free list stalls");
        $def[$defcnt] .= rrd::gprint("stalls",array("LAST", "AVERAGE","MAX"),"%3.2lf");
        $defcnt++;
    }
    if(preg_match('/^lazy_writes_per_sec$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Lazy Writes";
        $opt[$defcnt] = "--vertical-label \"lazyw/s\" --title \"Lazy writes / sec on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("lazyw",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::cdef("ag","lazyw,".$VAL['WARN'].",LE,lazyw,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,lazyw,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","lazyw,".$VAL['CRIT'].",LE,lazyw,".$VAL['WARN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,lazyw,0,IF");
        $def[$defcnt] .= rrd::cdef("ar","lazyw,INF,LE,lazyw,".$VAL['CRIT'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,lazyw,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red");
        $def[$defcnt] .= rrd::line1("lazyw","#000000");
        $def[$defcnt] .= rrd::gprint("lazyw",array("LAST", "AVERAGE","MAX"),"%3.4lf");
        $defcnt++;
    }
    if(preg_match('/^page_life_expectancy$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Page Life Expectancy";
        $opt[$defcnt] = "--vertical-label \"s\" --title \"Page life expectancy on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("lifeexp",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::cdef("ar","lifeexp,".$VAL['CRIT_MIN'].",LE,lifeexp,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,lifeexp,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","lifeexp,".$VAL['WARN_MIN'].",LE,lifeexp,".$VAL['CRIT_MIN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,lifeexp,0,IF");
        $def[$defcnt] .= rrd::cdef("ag","lifeexp,INF,LE,lifeexp,".$VAL['WARN_MIN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,lifeexp,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("lifeexp","#000000","Page Live");
        $def[$defcnt] .= rrd::gprint("lifeexp",array("LAST", "AVERAGE", "MIN"),"%3.2lf") ;
        $defcnt++;
    }
    if(preg_match('/^total_server_memory$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Total Server memory";
        $opt[$defcnt] = "--vertical-label \"Bytes\" --title \"Total sql server memory on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("mem",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST");
        $def[$defcnt] .= rrd::area("mem","#c3c3c3", "Memory");
        $def[$defcnt] .= rrd::line1("mem","#111111");
        $def[$defcnt] .= rrd::gprint("mem",array("MAX","AVERAGE", "LAST"),"%.1lf %SB") ;
        $defcnt++;
    }
    if(preg_match('/^buffer_cache_hit_ratio$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Buffer Cache Hit Ratio";
        $opt[$defcnt] = "--vertical-label \"%\" --title \"Buffer cache hit ratio on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("bufcahitrat",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::cdef("ar","bufcahitrat,".$VAL['CRIT_MIN'].",LE,bufcahitrat,0,GT,INF,UNKN,IF,UNKN,IF,ISINF,bufcahitrat,0,IF");
        $def[$defcnt] .= rrd::cdef("ay","bufcahitrat,".$VAL['WARN_MIN'].",LE,bufcahitrat,".$VAL['CRIT_MIN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,bufcahitrat,0,IF");
        $def[$defcnt] .= rrd::cdef("ag","bufcahitrat,100,LE,bufcahitrat,".$VAL['WARN_MIN'].",GT,INF,UNKN,IF,UNKN,IF,ISINF,bufcahitrat,0,IF");
        $def[$defcnt] .= rrd::area("ag","#$green") ;
        $def[$defcnt] .= rrd::area("ay","#$yellow") ;
        $def[$defcnt] .= rrd::area("ar","#$red") ;
        $def[$defcnt] .= rrd::line1("bufcahitrat","#000000:");
        $def[$defcnt] .= rrd::gprint("bufcahitrat","LAST","Hit ratio is %3.2lf percent\\n") ;
        $defcnt++;
    }

    if(preg_match('/^(.*)_lock_timeouts_per_sec/', $VAL['NAME'], $match)) {
	$dsname = $match[1];
	if(empty($opt[1])){
            $opt[1] = "--vertical-label \"Lock timeouts/s\" -l0 --title \"Locks timeouts / sec\" ";
	}
	if(empty($def[1])){
             $def[1] = "";
        }
        $ds_name[1] = "Lock Timeouts Per Second ";
        $def[1] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[1] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[1] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.6lf" );
        
    }

    if(preg_match('/^(.*)_lock_waits_per_sec/', $VAL['NAME'], $match)) {
	$dsname = $match[1];
	if(!defined($opt[1])){
            $opt[1] = "--vertical-label \"Lockwaity/s\" -l0 --title \"Lockwaits / sec\" ";
	}
	if(empty($def[1])){
             $def[1] = "";
        }
        $ds_name[1] = "Lockwaits / sec ";
        $def[1] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[1] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[1] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.6lf" );
        
    }

    if(preg_match('/^(.*)_deadlocks_per_sec/', $VAL['NAME'], $match)) {
	$dsname = $match[1];
	if(!defined($opt[1])){
            $opt[1] = "--vertical-label \"Deadlocks/s\" -l0 --title \"Deadlocks / sec\" ";
	}
	if(empty($def[1])){
             $def[1] = "";
        }
        $ds_name[1] = "Deadlocks / sec ";
        $def[1] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[1] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[1] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.6lf" );
    }

    if(preg_match('/^db_(.*)_free_pct/', $VAL['NAME'], $match)) {
	$dsname = $match[1];
	if(!defined($opt[1])){
            $opt[1] = "--vertical-label \"%\" -l0 --title \"DB Freespace %\" ";
	}
	if(empty($def[1])){
             $def[1] = "";
        }
        $ds_name[1] = "DB Freespace %";
        $def[1] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[1] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[1] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.2lf%%" );
    }
    if(preg_match('/^db_(.*)_free$/', $VAL['NAME'], $match)) {
	$dsname = $match[1];
	if(empty($opt[2])){
            $opt[2] = "--vertical-label \"MB\" --title \"DB Freespace MB\" ";
	}
	if(empty($def[2])){
             $def[2] = "";
        }
        $ds_name[2] = "DB Freespace MB";
        $def[2] .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
        $def[2] .= rrd::line1   ("var".$KEY, rrd::color($KEY), rrd::cut($dsname, 12) ) ;
        $def[2] .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%3.2lf %SM" );
    }
    
    if(preg_match('/^select$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Output from sql-query for $servicedesc";
        $opt[$defcnt] = "--vertical-label \"Counts\" --title \"Output from sql-query for $servicedesc on $hostname\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("var".$KEY,$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::area("var".$KEY,"#111111");
        $def[$defcnt] .= rrd::gprint("var".$KEY,array("LAST", "MAX", "AVERAGE"),"%3.2lf Counts") ;
        $defcnt++;
    }

   if(preg_match('/^(.*)bck_age$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "Last DB Backup";
        $opt[$defcnt] = "--vertical-label \"Hours\" --title \"Last DB Backup\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("var".$KEY,$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::area("var".$KEY,"#111111");
        $def[$defcnt] .= rrd::gprint("var".$KEY,array("LAST", "MAX", "AVERAGE"),"%3.2lf Counts") ;
        $defcnt++;
    }

    if(preg_match('/^sql_runtime$/', $VAL['NAME'])) {
        $ds_name[$defcnt] = "SQL runtime";
        $opt[$defcnt] = "--vertical-label \"Seconds\" --title \"Execution time of the SQL statement\" ";
        $def[$defcnt] = "";
        $def[$defcnt] .= rrd::def("runtime",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE:reduce=LAST") ;
        $def[$defcnt] .= rrd::area("runtime","#111111");
        $def[$defcnt] .= rrd::gprint("runtime",array("LAST", "MAX", "AVERAGE"),"%3.2lf Seconds") ;
        $defcnt++;
    }
}
?>

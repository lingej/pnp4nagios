<?php
#
# Plugin: check_apachestatus.pl by De Bodt Lieven
# http://exchange.nagios.org/directory/Plugins/Web-Servers/Apache/check_apachestatus/details
# plugin produces GAUGE values
#
# pnp4nagios templating: Roman Ovchinnikov <coolthecold@gmail.com>
# pnp version >= 0.6.5
#
#
# from apache's scoreboard.h
#	 #define SERVER_DEAD 0
#	 #define SERVER_STARTING 1   /* Server Starting up */
#	 #define SERVER_READY 2      /* Waiting for connection (or accept() lock) */
#	 #define SERVER_BUSY_READ 3  /* Reading a client request */
#	 #define SERVER_BUSY_WRITE 4 /* Processing a client request */
#	 #define SERVER_BUSY_KEEPALIVE 5 /* Waiting for more requests via keepalive */
#	 #define SERVER_BUSY_LOG 6   /* Logging the request */
#	 #define SERVER_BUSY_DNS 7   /* Looking up a hostname */
#	 #define SERVER_CLOSING 8    /* Closing the connection */
#	 #define SERVER_GRACEFUL 9   /* server is gracefully finishing request */
#	 #define SERVER_IDLE_KILL 10     /* Server is cleaning up idle children. */
#	 #define SERVER_NUM_STATUS 11    /* number of status settings */

# perfdata example:
# Performance Data: 'Waiting for Connection'=90 'Starting Up'=0 'Reading Request'=10 'Sending Reply'=196 'Keepalive (read)'=360 'DNS Lookup'=0 'Closing Connection'=77 'Logging'=0 'Gracefully finishing'=0 'Idle cleanup'=0 'Open slot'=547 'Requests/sec'=119.0 'kB per sec'=8294.4KB 'kB per Request'=69.9KB
#


# first graph - workers that do smth, not just idling..
$ds_name[1]="Working slots";
$opt[1] = " --vertical-label \"Workers\" --title \"Apache Workers: working on $hostname / $servicedesc\" ";
$opt[1] .= " --slope-mode ";
$def[1] = rrd::def("var2",$RRDFILE[2],$DS[2],"AVERAGE"); #starting
$def[1] .= rrd::cdef("negstarting","var2,-1,*"); #starting
$def[1] .= rrd::def("var3",$RRDFILE[3],$DS[3],"AVERAGE"); #reading
$def[1] .= rrd::cdef("negreading","var3,-1,*"); #reading
$def[1] .= rrd::def("var4",$RRDFILE[4],$DS[4],"AVERAGE"); #reply
$def[1] .= rrd::def("var6",$RRDFILE[6],$DS[6],"AVERAGE"); #DNS LOOKUP
$def[1] .= rrd::cdef("negdns","var6,-1,*"); #DNS LOOKUP
$def[1] .= rrd::def("var7",$RRDFILE[7],$DS[7],"AVERAGE"); #closing
$def[1] .= rrd::cdef("negclosing","var7,-1,*"); #closing
$def[1] .= rrd::def("var8",$RRDFILE[8],$DS[8],"AVERAGE"); #logging
$def[1] .= rrd::cdef("neglogging","var8,-1,*"); #logging
$def[1] .= rrd::area("negclosing","#b7ff9e","closing",true ); #closing
$def[1] .= rrd::line1("negclosing","#2eae00");
$def[1] .= rrd::gprint("var7",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[1] .= rrd::area("negstarting","#993300","starting"); #startung
$def[1] .= rrd::gprint("var2",array("LAST","MAX","AVERAGE"),"%7.0lf");
$def[1] .= rrd::area("negreading","#001919","reading",true); #reading
$def[1] .= rrd::gprint("var3",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[1] .= rrd::area("negdns","#009900","DNS lookup",true); #dns lookup
$def[1] .= rrd::gprint("var6",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[1] .= rrd::area("neglogging","#ff0000","logging",true); #logging
$def[1] .= rrd::gprint("var8",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[1] .= rrd::area("var4","#6fb7ff","replying"); #replying
$def[1] .= rrd::line1("var4","#0019ff",FALSE,FALSE);
$def[1] .= rrd::gprint("var4",array("LAST","MAX","AVERAGE"),"%7.0lf") ;


# second graph - idling workers
#some idle actions like graceful shutdown, open slots & such
$ds_name[2]="Idling slots";
$opt[2] = " --vertical-label \"Workers\" --title \"Apache Workers: idling on $hostname / $servicedesc\" ";
$opt[2] .= " --slope-mode ";
$def[2] = rrd::def("var2",$RRDFILE[5],$DS[5],"AVERAGE"); #keepalive (read)
$def[2] .= rrd::def("var3",$RRDFILE[1],$DS[1],"AVERAGE"); #waiting
$def[2] .= rrd::def("var4",$RRDFILE[10],$DS[10],"AVERAGE"); #idle cleanup
$def[2] .= rrd::def("var5",$RRDFILE[9],$DS[9],"AVERAGE"); #gracefully finishing
$def[2] .= rrd::area("var3","#ffe4c4","waiting for conn");
$def[2] .= rrd::line1("var3","#eb891d");
$def[2] .= rrd::gprint("var3",array("LAST","MAX","AVERAGE"),"%7.0lf") ;#TODO - add border lines like for finishing
$def[2] .= rrd::area("var2","#503020","keepalive",true); #TODO - add border lines like for finishing
$def[2] .= rrd::gprint("var2",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[2] .= rrd::area("var4","#66cdaa","idle cleanup",true);
$def[2] .= rrd::gprint("var4",array("LAST","MAX","AVERAGE"),"%7.0lf") ;
$def[2] .= rrd::area("var5","#6fb7ff","gracefully finishing",true);
$def[2] .= rrd::cdef("grline","var2,var3,var4,var5,+,+,+"); #line shouldn't be stacked over
$def[2] .= rrd::line1("grline","#0019ff",FALSE,FALSE);
$def[2] .= rrd::gprint("var5",array("LAST","MAX","AVERAGE"),"%7.0lf") ;

#third graph - open slots count
$ds_name[3]="Open slots";
$opt[3] = " --vertical-label \"Workers\" --title \"Apache open slots on $hostname / $servicedesc\" ";
$opt[3] .= " --slope-mode ";
$def[3] = rrd::def("var1",$RRDFILE[11],$DS[11],"AVERAGE");
$def[3] .= rrd::area("var1","#e0e0e0","open slots" );
$def[3] .= rrd::line1("var1","#858585");
$def[3] .= rrd::gprint("var1",array("LAST","MAX","AVERAGE"),"%7.0lf") ;


#fourth graph - server's traffic total and per request
$ds_name[4]="Apache Traffic";
$opt[4] = " --vertical-label \"Traffic\" -b 1024 --title \"Apache Traffic for $hostname / $servicedesc\" ";
$opt[4] .= " --slope-mode ";
$def[4] = rrd::def("var1",$RRDFILE[13],$DS[13],"AVERAGE");
$def[4] .= rrd::cdef("trbytes","var1,1024,*");
$def[4] .= rrd::def("var2",$RRDFILE[14],$DS[14],"AVERAGE");
$def[4] .= rrd::cdef("negperreq","var2,-1,*");
$def[4] .= "VDEF:totalbytes=trbytes,TOTAL ";
$def[4] .= rrd::area("trbytes","#b0c0fb","throughput");
$def[4] .= rrd::line1("trbytes","#3c63ff");
$def[4] .= rrd::gprint("trbytes",array("LAST","MAX","AVERAGE"),"%7.2lf %sB/sec") ;
$def[4] .= rrd::line2("negperreq","#00ff00","kB/request");
$def[4] .= rrd::gprint("var2",array("LAST","MAX","AVERAGE"),"%7.2lf %s$UNIT[14]") ;
$def[4] .= "GPRINT:totalbytes:\"%3.0lf %sB total\\n\" ";

#fifth graph - requests per sec ( rate is calculated by apache, time smoothed, very averaged, shouldn't be believed)
$ds_name[5]="Apache requests";
$opt[5] = " --vertical-label \"Request/sec\" --title \"Apache request/sec for $hostname / $servicedesc\" ";
$opt[5] .= " --slope-mode ";
$def[5] = rrd::def("var1",$RRDFILE[12],$DS[12],"AVERAGE");
$def[5] .= rrd::area("var1","#b7ff9e","Requests / sec");
$def[5] .= rrd::line1("var1","#2eae00");
$def[5] .= rrd::gprint("var1",array("LAST","MAX","AVERAGE"),"%7.2lf %s") ;

?>

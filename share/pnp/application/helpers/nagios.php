<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*
* 
*/
class nagios_Core {

	public function SummaryLink($hostname,$start,$end){
        $smon = date('m' , $start);
        $sday = date('d' , $start);
        $syear = date('Y' , $start);
        $shour = date('G' , $start);
        $smin = date('i' , $start);
        $ssec = date('s' , $start);
        $emon = date('m' , $end);
        $eday = date('d' , $end);
        $eyear = date('Y' , $end);
        $ehour = date('G' , $end);
        $emin = date('i' , $end);
        $esec = date('s' , $end);
        $nagios_base = $this->config->conf['nagios_base'];
        print "<a href=\"$nagios_base/summary.cgi?report=1&displaytype=1&timeperiod=custom&smon=$smon&sday=$sday&syear=$syear&shour=$shour&smin=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&hostgroup=all&servicegroup=all&host=$hostname&alerttypes=3&statetypes=3&hoststates=7&servicestates=120&limit=999\"";

		// FIXME i18n
        print " title=\"Most Recent Alerts for this Timerange\"><img src=\"media/images/notify.gif\"></a>\n";
	}

	public function AvailLink($hostname,$servicedesc,$start,$end){
		$smon = date('m' , $start);
        $sday = date('d' , $start);
        $syear = date('Y' , $start);
        $shour = date('G' , $start);
        $smin = date('i' , $start);
        $ssec = date('s' , $start);
        $emon = date('m' , $end);
        $eday = date('d' , $end);
        $eyear = date('Y' , $end);
        $ehour = date('G' , $end);
        $emin = date('i' , $end);
        $esec = date('s' , $end);
        $nagios_base = $this->config->conf['nagios_base'];
        if($servicedesc == "_HOST_"){
                print "<a href=\"$nagios_base/avail.cgi?show_log_entries=&host=$hostname&timeperiod=custom&smon=$smon&sday=$sday&syear=$syear&shour=$shour&sm
in=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&rpttimeperiod=&assumeinitialstates=yes&assumestateretention=yes&ass
umestatesduringnotrunning=yes&includesoftstates=yes&initialassumedservicestate=6&backtrack=4\"";
        }else{
                print "<a href=\"$nagios_base/avail.cgi?show_log_entries=&host=$hostname&service=$servicedesc&timeperiod=custom&smon=$smon&sday=$sday&syear=$
syear&shour=$shour&smin=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&rpttimeperiod=&assumeinitialstates=yes&assumes
tateretention=yes&assumestatesduringnotrunning=yes&includesoftstates=yes&initialassumedservicestate=6&backtrack=4\"";

        }
		// FIXME i18n
        print " title=\"Nagios Availability Report for this Timerange\"><img src=\"media/images/trends.gif\" ></a>\n";
}



} 

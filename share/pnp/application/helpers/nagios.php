<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*
* 
*/
class nagios_Core {

    public static function SummaryLink($hostname,$start,$end){
        $config     = new Config_Model();
        $config->read_config();

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
        $nagios_base = $config->conf['nagios_base'];
        print "<a href=\"$nagios_base/summary.cgi?report=1&displaytype=1&timeperiod=custom&smon=$smon&sday=$sday&syear=$syear&shour=$shour&smin=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&hostgroup=all&servicegroup=all&host=$hostname&alerttypes=3&statetypes=3&hoststates=7&servicestates=120&limit=999\"";
        print " title=\"".Kohana::lang('common.nagios-summary-link-title')."\"><img src=\"".url::base()."media/images/notify.gif\"></a>\n";
    }

    public static function AvailLink($hostname,$servicedesc,$start,$end){
        $config     = new Config_Model();
        $config->read_config();
        $hostname = urlencode($hostname);    
        $servicedesc = urlencode($servicedesc);    
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
        $nagios_base = $config->conf['nagios_base'];
        if($servicedesc == "Host+Perfdata"){
                print "<a href=\"$nagios_base/avail.cgi?show_log_entries=&host=$hostname&timeperiod=custom&smon=$smon&sday=$sday&syear=$syear&shour=$shour&smin=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&rpttimeperiod=&assumeinitialstates=yes&assumestateretention=yes&assumestatesduringnotrunning=yes&includesoftstates=yes&initialassumedservicestate=6&backtrack=4\"";
        }else{
                print "<a href=\"$nagios_base/avail.cgi?show_log_entries=&host=$hostname&service=$servicedesc&timeperiod=custom&smon=$smon&sday=$sday&syear=$syear&shour=$shour&smin=$smin&ssec=$ssec&emon=$emon&eday=$eday&eyear=$eyear&ehour=$ehour&emin=$emin&esec=$esec&rpttimeperiod=&assumeinitialstates=yes&assumestateretention=yes&assumestatesduringnotrunning=yes&includesoftstates=yes&initialassumedservicestate=6&backtrack=4\"";

        }
        print " title=\"".Kohana::lang('common.nagios-avail-link-title')."\"><img src=\"".url::base()."media/images/trends.gif\" ></a>\n";
}



} 

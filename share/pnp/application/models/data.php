<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Retrieves and manipulates current status of hosts (and services?)
 */
class Data_Model extends System_Model
{

    private $XML   = array();
    public  $DS    = array();
    public  $MACRO = array();
    private $RRD   = array();
    public  $STRUCT  = array();
    public  $TIMERANGE  = array();    
    public  $PAGE_DEF   = array();    
    public  $PAGE_GRAPH = array();
    public  $XPORT = "";
    public  $TEMPLATE_FILE = "";
    public  $GRAPH_TYPE = 'normal';
    /*
    * 
    *
    */
    public function __construct(){
        $this->config = new Config_Model();    
        $this->config->read_config();
        $this->auth = new Auth_Model();    
    }

    /*
    * Get All Special Templates
    *
    */
    public function getSpecialTemplates(){
        $conf = $this->config->conf;
        $templates = array();
        if (is_dir($conf['special_template_dir'])){
            if ($dh = opendir($conf['special_template_dir'])) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == "." || $file == "..")
                        continue;
                    if (!preg_match("/(.*)\.php$/", $file, $template))
                        continue;
                    $templates[] = $template[1];
                }
            }
        }
        if(sizeof($templates) > 0){
            sort($templates);
            return $templates;
        }else{
            return FALSE;
        }
    }

    public function getFirstSpecialTemplate(){
        $templates = $this->getSpecialTemplates();
        if($templates === FALSE){
            return FALSE;
        }else{
            return $templates[0];
        }
    }
    /*
    * 
    *
    */
    public function getHosts() {
        $hosts = array();
        $conf = $this->config->conf;
        $i = 0;
            if (is_dir($conf['rrdbase'])) {
                if ($dh = opendir($conf['rrdbase'])) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file == "." || $file == ".." || $file == ".pnp-internal")
                            continue;
                        
                        if (is_file($conf['rrdbase'] . "/" . $file) )
                            continue;

                        if($this->auth->is_authorized($file) === FALSE)
                            continue;

                        $stat = stat($conf['rrdbase'] . "/" . $file);
                        $age = (time() - $stat['mtime']);
                        $hosts[$i]['name'] = $file;
                        $hosts[$i]['sort'] = strtoupper($file);
                        if ($age < $conf['max_age']) {
                            $hosts[$i]['state'] = 'active';
                        } else {
                            $hosts[$i]['state'] = 'inactive';
                        }
                $i++;
                   }
                   closedir($dh);
               } else {
                    throw new Kohana_User_Exception('Perfdata Dir', "Can not open $path");
               }
           }
        if(sizeof($hosts)>0){
            # Obtain a list of columns
            foreach ($hosts as $key => $row) {
                $sort[$key]  = $row['sort'];
            }
            # Sort the data with volume descending, edition ascending
            # Add $data as the last parameter, to sort by the common key
            array_multisort($sort, SORT_ASC, $hosts);
        }else{
            throw new Kohana_Exception('error.perfdata-dir-empty', $conf['rrdbase'] );
        }
        return $hosts;
    }


    /*
    * 
    *
    */
    function getRawServices($hostname) {
        $services = array ();
        $host     = array();
        $conf     = $this->config->conf;
        $i        = 0;
        $path     = $conf['rrdbase'] . $hostname;
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while ( ($file = readdir($dh) ) !== false) {
                    if ($file == "." || $file == "..")
                        continue;

                    if (!preg_match("/(.*)\.xml$/", $file, $servicedesc))
                        continue;

                    $fullpath = $path . "/" . $file;
                    $stat = stat("$fullpath");
                    $age = (time() - $stat['mtime']);
                    
                    $state = "active";    
                    if ($age > $conf['max_age']) { # 6Stunden
                        $state = "inactive";
                    }
                    $services[$i]['state'] = $state;
                    $services[$i]['name'] = $servicedesc[1]; 
                    $i++;
                }
            }
        }else{
            throw new Kohana_Exception('error.perfdata-dir-for-host', $path, $hostname );
        }
        if( is_array($services) && sizeof($services) > 0){
            # Obtain a list of columns
            foreach ($services as $key => $row) {
                $sort[$key]  = $row['name'];
            }
            # Sort the data with volume descending, edition ascending
            # Add $data as the last parameter, to sort by the common key
            array_multisort($sort, SORT_STRING, $services);
        }else{
            throw new Kohana_Exception('error.host-perfdata-dir-empty', $path, $hostname );
        }        
        return $services;
    }
    /*
    * 
    *
    */
    function getServices($hostname) {
        $services     = array ();
        $host         = array();
        $i            = 0;
        $service_list = $this->getRawServices($hostname);
        foreach( $service_list as $s ){
            if(!$this->readXML($hostname, $s['name'], FALSE)){
                continue;
            }
            if($s['name'] == "_HOST_"){
                // Check authorization
                if($this->auth->is_authorized((string) $this->XML->NAGIOS_AUTH_HOSTNAME, "_HOST_") === FALSE)
                    continue;
 
                $host[0]['name']             = "_HOST_";
                $host[0]['hostname']         = (string) $this->XML->NAGIOS_HOSTNAME;
                $host[0]['state']            = $s['state'];
                $host[0]['servicedesc']      = "Host Perfdata";
                $host[0]['is_multi']         = (string) $this->XML->DATASOURCE[0]->IS_MULTI[0];
            }else{
                // Check authorization
                if($this->auth->is_authorized((string) $this->XML->NAGIOS_AUTH_HOSTNAME, (string) $this->XML->NAGIOS_AUTH_SERVICEDESC) === FALSE )
                    continue;
 
                $services[$i]['name']        = $s['name'];
                // Sorting check_multi
                if( (string) $this->XML->NAGIOS_MULTI_PARENT == ""){
                    $services[$i]['sort']        = strtoupper($s['name']);
                }else{
                    $services[$i]['sort']        = strtoupper((string) $this->XML->NAGIOS_MULTI_PARENT);
                    $services[$i]['sort']       .= (string) $this->XML->DATASOURCE[0]->IS_MULTI[0];
                    $services[$i]['sort']       .= strtoupper($s['name']);
                }
                $services[$i]['state']       = $s['state'];
                $services[$i]['hostname']    = (string) $this->XML->NAGIOS_DISP_HOSTNAME;
                $services[$i]['servicedesc'] = (string) $this->XML->NAGIOS_DISP_SERVICEDESC;
                $services[$i]['is_multi']    = (string) $this->XML->DATASOURCE[0]->IS_MULTI[0];
            }
            $i++;
        }
        #print Kohana::debug($services);
        if( is_array($services) && sizeof($services) > 0){
            # Obtain a list of columns
            foreach ($services as $key => $row) {
                $sort[$key]  = $row['sort'];
            }
            # Sort the data with volume descending, edition ascending
            # Add $data as the last parameter, to sort by the common key
            array_multisort($sort, SORT_STRING, $services);
        }        
        if(is_array($host) && sizeof($host) > 0 ){
            array_unshift($services, $host[0]);
        }
        return $services;
    }

    /*
    * 
    *
    */
    public function getFirstService($hostname) {
        $conf = $this->config->conf;
        $services = $this->getServices($hostname);
        foreach ($services as $srv) {
            if ($srv['state'] == "active" ) {
                break;
            }
        }
        if(sizeof($srv) == 0){
            throw new Kohana_Exception('error.get-first-service', $hostname );
        }
        return $srv['name'];
    }

    /*
    * 
    *
    */
    public function getFirstHost() {
        $conf = $this->config->conf;
        $hosts = $this->getHosts();
        foreach ($hosts as $host) {
            if ($host['state'] == "active" ) {
                break;
            }
        }
        if(sizeof($host) == 0){
            throw new Kohana_Exception('error.get-first-host');
        }
        return $host['name'];
    }

    /*
    * 
    *
    */
    public function readXML ($hostname, $servicedesc, $throw_exception=TRUE){
        $conf        = $this->config->conf;
        $xmlfile     = $conf['rrdbase'].$hostname."/".$servicedesc.".xml";
        $xml         = array();
        if (file_exists($xmlfile)) {
            libxml_use_internal_errors(TRUE);
            libxml_clear_errors(TRUE);
            if(! $xml = simplexml_load_file($xmlfile) ){;
                if( $throw_exception == TRUE ){
                    $errors = '<br>';
                    foreach(libxml_get_errors() as $error) {
                         $errors .= $error->message."<br>";
                    }
                    throw new Kohana_Exception('error.xml-generic_error',$xmlfile, $errors);
                }else{
                    return FALSE;
                }
            }
            $this->XML   = array();
            $this->MACRO = array();
            $this->MACRO['AUTH_SERVICEDESC'] = '';
            $this->MACRO['AUTH_HOSTNAME'] = '';
            $this->DS    = array();
            // Throw excaption without a valid structure version
            if(!isset($xml->XML->VERSION) && $throw_exception == TRUE){
                throw new Kohana_Exception('error.xml-structure-without-version-tag',$xmlfile);
            }
            if(!isset($xml->XML->VERSION) && $throw_exception == FALSE){
                return FALSE;
            }
            foreach ( $xml as $key=>$val ){
                if(preg_match('/^NAGIOS_(.*)$/', $key, $match)){
                    #print $match[1]." => ".$val."\n";
                    $key = $match[1];
                    $this->MACRO[$key] = (string) $val;
                }
            }
            $i=0;
            foreach ( $xml->DATASOURCE as $datasource ){
                foreach ( $datasource  as $key=>$val){
                    #print "$key => $val\n";
                    #$$key[$i] = (string) $val;
                    $this->DS[$i][$key] = (string) $val;
                }
                $i++; 
            }
            $this->XML = $xml;
            return TRUE;
        }else{
            throw new Kohana_Exception('error.xml-not-found', $xmlfile);
        }
    }

    /*
    * 
    *
    */
    public function buildDataStruct ($host = FALSE, $service = FALSE, $view = NULL, $source = NULL){
        if($host === FALSE && $service === FALSE){
            return FALSE;
        }
        $conf = $this->config->conf;

        /*
         * Special templates without Host/Service
         */
        if($host == '__special' ){
            // $service contains the template name
            $this->includeTemplate($service,'special');
        }else{
            if( $this->readXML($host,$service) == FALSE ){
                throw new Kohana_Exception('error.xml-not-found', "Undefined error");
                return false;
            }
            $this->includeTemplate($this->DS[0]['TEMPLATE']);
        }
        if(isset($this->TIMERANGE['type']) && $this->TIMERANGE['type'] == "start-end"){
            $view = intval($view);
            $i=0;
            foreach( $this->RRD['def'] as $key=>$val){
                if( ! is_null($source) && $source != $key ){
                    continue;
                }
                $tmp_struct = array();
                $tmp_struct['LEVEL']         = $i;
                $tmp_struct['VIEW']          = $view;
                $tmp_struct['TEMPLATE_FILE'] = $this->TEMPLATE_FILE;;
                $tmp_struct['SOURCE']        = $key;
                $tmp_struct['RRD_CALL']      = $this->TIMERANGE['cmd'] . " ". $this->RRD['opt'][$key] . " " . $this->RRD['def'][$key];
                $tmp_struct['TIMERANGE']     = $this->TIMERANGE;
                $tmp_struct['GRAPH_WIDTH']   = $this->getGraphDimensions('width',  $tmp_struct['RRD_CALL']);
                $tmp_struct['GRAPH_HEIGHT']  = $this->getGraphDimensions('height', $tmp_struct['RRD_CALL']);
                if(isset($this->RRD['ds_name'][$key]) ){
                     $tmp_struct['ds_name']   = $this->RRD['ds_name'][$key];
                }elseif(array_key_exists($i, $this->DS)){
                     $tmp_struct['ds_name']   = $this->DS[$i]['NAME'];
                }else{
                     $tmp_struct['ds_name']   = "UNDEF";
                }
                $tmp_struct['MACRO']   = $this->MACRO;
                if(isset($this->XML->XML->VERSION)){
                    $tmp_struct['VERSION']   = pnp::xml_version_check( (string) $this->XML->XML->VERSION);
                }else{
                    $tmp_struct['VERSION']   = pnp::xml_version_check("0");
                }
                $this->addToDataStruct($tmp_struct);
                $i++;
            }
        return;
        }
        if( $view === ""){
            $v = 0;
            foreach($this->config->views as $view_key=>$view_val){
                $i=0;
                foreach( $this->RRD['def'] as $key=>$val){
                    if( ! is_null($source) && $source != $key ){
                        continue;
                    }
                    $tmp_struct = array();
                    $tmp_struct['LEVEL']         = $i;
                    $tmp_struct['VIEW']          = $view_key;
                    $tmp_struct['TEMPLATE_FILE'] = $this->TEMPLATE_FILE;;
                    $tmp_struct['SOURCE']        = $key;
                    $tmp_struct['RRD_CALL']      = $this->TIMERANGE[$v]['cmd'] . " " . $this->RRD['opt'][$key] . " " . $this->RRD['def'][$key];
                    $tmp_struct['GRAPH_WIDTH']   = $this->getGraphDimensions('width',  $tmp_struct['RRD_CALL']);
                    $tmp_struct['GRAPH_HEIGHT']  = $this->getGraphDimensions('height', $tmp_struct['RRD_CALL']);
                    if(isset($this->RRD['ds_name'][$key]) ){
                        $tmp_struct['ds_name']   = $this->RRD['ds_name'][$key];
                    }elseif(array_key_exists($i, $this->DS)){
                        $tmp_struct['ds_name']   = $this->DS[$i]['NAME'];
                    }else{
                        $tmp_struct['ds_name']   = "UNDEF";
                    }
                    $tmp_struct['TIMERANGE']     = $this->TIMERANGE[$v];
                    $tmp_struct['MACRO']         = $this->MACRO;
                    if(isset($this->XML->XML->VERSION)){
                        $tmp_struct['VERSION']   = pnp::xml_version_check( (string) $this->XML->XML->VERSION);
                    }else{
                        $tmp_struct['VERSION']   = pnp::xml_version_check("0");
                    }
                    $this->addToDataStruct($tmp_struct);
                    $i++;
                }
            $v++;
            }
        }else{
            $view = intval($view);
            $i=0;
            foreach( $this->RRD['def'] as $key=>$val){
                if( ! is_null($source) && $source != $key ){
                    continue;
                }
                $tmp_struct = array();
                $tmp_struct['LEVEL']         = $i;
                $tmp_struct['VIEW']          = $view;
                $tmp_struct['TEMPLATE_FILE'] = $this->TEMPLATE_FILE;;
                $tmp_struct['SOURCE']        = $key;
                $tmp_struct['RRD_CALL']      = $this->TIMERANGE[$view]['cmd'] . " ". $this->RRD['opt'][$key] . " " . $this->RRD['def'][$key];
                $tmp_struct['TIMERANGE']     = $this->TIMERANGE[$view];
                $tmp_struct['GRAPH_WIDTH']   = $this->getGraphDimensions('width',  $tmp_struct['RRD_CALL']);
                $tmp_struct['GRAPH_HEIGHT']  = $this->getGraphDimensions('height', $tmp_struct['RRD_CALL']);
                if(isset($this->RRD['ds_name'][$key]) ){
                    $tmp_struct['ds_name']   = $this->RRD['ds_name'][$key];
                }elseif(array_key_exists($i, $this->DS)){
                    $tmp_struct['ds_name']   = $this->DS[$i]['NAME'];
                }else{
                    $tmp_struct['ds_name']   = "UNDEF";
                }
                $tmp_struct['MACRO']         = $this->MACRO;
                if(isset($this->XML->XML->VERSION)){
                    $tmp_struct['VERSION']   = pnp::xml_version_check( (string) $this->XML->XML->VERSION);
                }else{
                    $tmp_struct['VERSION']   = pnp::xml_version_check("0");
                }
                   $this->addToDataStruct($tmp_struct);
                   $i++;
            }
        }
    }

    /*
    * 
    *
    */
    private function addToDataStruct ($data=FALSE) {
        if($data === FALSE)
            return FALSE;

        array_push($this->STRUCT, $data);
    } 

    /*
    * 
    *
    */
    private function includeTemplate($template=FALSE,$type='normal'){
        if($template===FALSE){
            return FALSE;
        }
        $this->RRD = array();
        /*
        * Normal PNP Templates
        */
        if($type == 'normal'){
            $template_file = $this->findTemplate( $template );
            $this->TEMPLATE_FILE = $template_file;
            $hostname      = $this->MACRO['HOSTNAME'];
            $servicedesc   = $this->MACRO['SERVICEDESC'];
            $TIMERANGE     = $this->TIMERANGE;
        }elseif($type == 'special'){
            $template_file = $this->findTemplate( $template, $type );
            $TIMERANGE     = $this->TIMERANGE;
        }
        $def     = FALSE;
        $opt     = FALSE;
        $ds_name = FALSE;
        /*
        * 0.4.x Template compatibility 
        */
        foreach($this->DS as $key=>$val ){
            $key++;
            foreach(array_keys($val) as $tag){
                ${$tag}[$key] = $val[$tag];
            }
        }
        foreach($this->MACRO as $key=>$val ){
            ${"NAGIOS_".$key} = $val;
        }
        if(isset($RRDFILE[1])){
            $rrdfile = $RRDFILE[1];
        }
        // Include template
        if($template_file == FALSE){
            throw new Kohana_Exception('error.no-templates-found');
        }else{
            ob_start();
            include($template_file);
            ob_end_clean();
        }
        // Compatibility for very old Templates
        if(!is_array($def) && $def != FALSE){
            $tmp[1] = $def;
            $def = $tmp;
        }    
        if(!is_array($opt) && $opt != FALSE){
            $tmp[1] = $opt;
            $opt = $tmp;
        }
        if(!is_array($ds_name) && $ds_name != FALSE){
            $tmp[1] = $ds_name;
            $ds_name = $tmp;
        }
        //
        if($def != FALSE){
            $this->RRD['def'] = $this->array_reindex($def);
        }else{
            throw new Kohana_Exception('error.template-without-def', $template_file);
        }
        if($opt != FALSE ){
            $this->RRD['opt'] = $this->array_reindex($opt);
        }else{
            throw new Kohana_Exception('error.template-without-opt', $template_file);
        }
        if( $ds_name != FALSE ){
            $this->RRD['ds_name'] = $this->array_reindex($ds_name);
        }
        return TRUE;        
    }

    #
    #
    #
    private function getGraphDimensions($search, $command){
        if($search == 'width'){
            if(preg_match_all('/(-w|--width|--width=)\s([0-9]+)\s/i',$command,$match)){
                $value = array_pop($match[2]); 
                return $value;
            }else{
                return $this->config->conf['graph_width'];
            }
        }
        if($search == 'height'){
            if(preg_match_all('/(-h|--height|--height=)\s([0-9]+)\s/i',$command,$match)){
                $value = array_pop($match[2]); 
                return $value;
            }else{
                return $this->config->conf['graph_height'];
            }
        }
        return FALSE;
    }
    #
    #
    #
    private function array_reindex($data){
        $i=0;
        foreach($data as $d){
            $tmp[$i] = $d;
            $i++; 
        }
        return $tmp;
    }
    
    /*
    * 
    *
    */
    public function findTemplate($template,$type='normal'){
        $conf = $this->config->conf;
        /*
         * Normal templates
         */
        if($type == 'normal'){
            // Build a list of directories to search for templates 
            $template_dirs = array();
            if(array_key_exists('template_dirs', $this->config->conf)){
                foreach($this->config->conf['template_dirs'] as $dir){
                    $template_dirs[] = $dir;
                }
            }    
            foreach(Kohana::config('core.template_dirs') as $dir){
                $template_dirs[] = $dir;
            }
            #throw new Kohana_Exception(print_r($template_dirs,TRUE));
            foreach($template_dirs as $dir){
                $match = $this->findRecursiveTemplate($template,$dir);
                if($match != FALSE){
                    return $match;
                }
            }
            /*
             * Fallback to default templates
             */
            foreach($template_dirs as $dir){
                $match = $this->findRecursiveTemplate('default',$dir);
                if($match != FALSE){
                    return $match;
                }
            }
            return FALSE;
        }
        /*
         * Special Templates
         */
        if($type == 'special'){
            if (is_readable($conf['special_template_dir'] . '/' . $template . '.php')) {
                $template_file = $conf['special_template_dir'].'/' . $template . '.php';
            }else{
                throw new Kohana_Exception("Special Template '$template' not found");
            }
            return $template_file;
        }
    }

    /*
    * 
    *
    */
    function findRecursiveTemplate($template, $dir) {
        if(!is_readable($dir)){
            return FALSE;
        }
        $template_file = "";
        $r_template_file = "";
        $r_template = "";
        $recursive = explode("_", $template);
        if($this->config->conf['enable_recursive_template_search'] == 1){
            $i = 0;
            foreach ($recursive as $value) {
                if ($i == 0) {
                    $r_template = $value;
                } else {
                    $r_template = $r_template . '_' . $value;
                }
                $file = $dir . '/' . $r_template . '.php';
                if (is_readable($file)) {
                    $r_template_file = $file;
                }
                $i++;
            }
            if($r_template_file != ""){
                return $r_template_file;
            }else{
                return FALSE;
            }
        }else{
            $file = $dir . '/' . $template . '.php';
            if (is_readable($file)) {
                return $file;
            }else{
                return FALSE;
            }
        }
    }

    public function getTimeRange($start=FALSE ,$end=FALSE ,$view="") {
        if($end != FALSE){
            // we are in a custom timerange
            if(!is_numeric($end)){
                $timestamp = strtotime($end);
                if(!$timestamp){
                    throw new Kohana_User_Exception('Wrong Format', "End -> $end");
                }else{
                    $end = $timestamp;
                }
            }
        }else{
            $end = time();
        }
        if($start != FALSE ){
            // we are in a custom timerange
            if(!is_numeric($start)){
                $timestamp = strtotime($start);
                if(!$timestamp){
                    throw new Kohana_User_Exception('Wrong Format', "Start -> $start");
                }else{
                    $start = $timestamp;
                }
            }
        }
        if($start && $end){
            $timerange['title']   = Kohana::lang('common.timerange-selector-link');
            $timerange['start']   = $start;
            $timerange['f_start'] = date($this->config->conf['date_fmt'],$start);
            $timerange['end']     = $end;
            $timerange['f_end']   = date($this->config->conf['date_fmt'],$end);
            $timerange['cmd']     = " --start $start --end $end ";
            $timerange['type']    = "start-end";
            $this->TIMERANGE = $timerange;
            return;
        }
        
           $view=intval( pnp::clean($view) );
           if($view >= sizeof($this->config->views)){
               $view = 1;
           }

           if(!$end){
               $end = time();
           }elseif(!is_numeric($end)){
               $timestamp = strtotime($end);
               if(!$timestamp){
                throw new Kohana_User_Exception('Wrong Format', "$end");
               }else{
                   $end = $timestamp;
               }
           }else{
               $end = $end;
           }

           if(!$start){
               $start = ( $end - $this->config->views[$view]['start']);
           }elseif(!is_numeric($start)){
               $timestamp = strtotime($start);
               if(!$timestamp){
                throw new Kohana_User_Exception('Wrong Format', "$start");
               }else{
                   $start = $timestamp;
               }
           }else{
            $start = pnp::clean($start);
           }

           if($start >= $end){
            //Fixme i18n
            throw new Kohana_User_Exception('Wrong Timerange', "start >= end");
           }

           $timerange['title']   = $this->config->views[$view]['title'];
           $timerange['start']   = $start;
           $timerange['f_start'] = date($this->config->conf['date_fmt'],$start);
           $timerange['end']     = $end;
           $timerange['f_end']   = date($this->config->conf['date_fmt'],$end);
           $timerange['cmd']     = " --start $start --end $end ";
           $timerange['type']    = "views";
           for ($i = 0; $i < sizeof($this->config->views); $i++) {
               $timerange[$i]['title']   = $this->config->views[$i]['title'];
               $timerange[$i]['start']   = $end - $this->config->views[$i]['start'];
               $timerange[$i]['f_start'] = date($this->config->conf['date_fmt'],$end - $this->config->views[$i]['start']);
               $timerange[$i]['end']     = $end;
               $timerange[$i]['f_end']   = date($this->config->conf['date_fmt'],$end);
               $timerange[$i]['cmd']     = " --start " . ($end - $this->config->views[$i]['start']) . " --end  $end" ;
           }
           $this->TIMERANGE = $timerange;
    }

    public function buildBasketStruct($basket,$view = NULL){
        if(is_array($basket) && sizeof($basket) > 0){
	    if($view == ""){
                 $views = array_keys($this->config->views);
            }else{
		 $views = array($view);
            }
            foreach($views as $view){
                foreach($basket as $item){
                    # explode host::service::source
                    $slices = explode("::",$item);
                    if(sizeof($slices) == 2)
                        $this->buildDataStruct($slices[0], $slices[1], $view);
                    if(sizeof($slices) == 3)
                        $this->buildDataStruct($slices[0], $slices[1], $view, $slices[2]);
                }
                # break on custom time ranges
		if(isset($this->TIMERANGE['type']) && $this->TIMERANGE['type'] == "start-end"){
                    break;
                }
            }
        }
    }

    public function buildPageStruct($page,$view){
        $servicelist = array();
        $this->parse_page_cfg($page);
        $hosts = $this->getHostsByPage();
        # No regex so we keep the order defined by config
        if($this->PAGE_DEF['use_regex'] == 0){
            #Loop through graph definitions 
            foreach($this->PAGE_GRAPH as $graph){
                $hosts_to_search_for = explode(",", $graph['host_name']);
                foreach($hosts_to_search_for as $host){
                    if(in_array($host, $hosts)){
                        $services = $this->getServices($host);
                        foreach($services as $service) {
                            // search for definition
                            $data = $this->filterServiceByPage($graph,$host,$service);
                            if($data){
                                $servicelist[] = array( 'host' => $host, 'service' => $service['name'], 'source' => $data['source']);
                
                            }
                        }
                    }    
                }
            }
        }else{
            foreach($hosts as $host){
                $services = $this->getServices($host);
                foreach($services as $service) {
                    // search for definition
                    $data = $this->filterServiceByPage($this->PAGE_GRAPH,$host,$service);
                    if($data){
                        $servicelist[] = array( 'host' => $host, 'service' => $service['name'], 'source' => $data['source']);
                    }
                }
            }
        }
        #print Kohana::debug($servicelist);
        if(sizeof($servicelist) > 0 ){
            foreach($servicelist as $s){
                $this->buildDataStruct($s['host'],$s['service'],$view,$s['source']);
            }
        }else{
            $this->ERROR = "ERROR: ". Kohana::lang('error.no-data-for-page', $page.".cfg" );
        }
    }


    public function parse_page_cfg($page){
        $page_cfg = $this->config->conf['page_dir'].$page.".cfg";
        if(is_readable($page_cfg)){
            $data = file($page_cfg);
        }else{
            throw new Kohana_Exception('error.page-not-readable', $page.".cfg" );
        }
        $l = 0;
        $line = "";
        $tag = "";
        $inside=0;
        $this->PAGE_DEF['page_name'] = 'UNDEF';
        $this->PAGE_DEF['use_regex'] = 0;
        $this->PAGE_GRAPH = array();
        $allowed_tags = array("page", "graph");
        foreach($data as $line){
            if(preg_match('/(^#|^;)/',$line)) {
                continue;
            }

            preg_match('/define\s+(\w+)\W+{/' ,$line, $tag);
            if(isset($tag[1]) && in_array($tag[1],$allowed_tags)){
                $inside = 1;
                $t = $tag[1];
                $l++;
                continue;
            }
            if(preg_match('/\s?(\w+)\s+([^#]+)(#.*)?$/',$line, $key) && $inside == 1){
                $k=strtolower($key[1]);
                $v=$key[2];
                if($t=='page'){
                    $this->PAGE_DEF[$k] = trim($v);
                }elseif($t=='graph'){
                    $this->PAGE_GRAPH[$l][$k] = trim($v);
                }
            }
            if(preg_match('/}/',$line)){
                $inside=0;
                $t = "";
                continue;
            }
        }
    }

    /*
    *
    */
    public function getHostsByPage(){
        $hosts = $this->getHosts();
        $new_hosts = array();
        foreach( $hosts as $host){
            if($host['state'] == "inactive"){
                continue;
            }
            if($tmp = $this->filterHostByPage($host['name'])){
                $new_hosts[] = $tmp;
            }
        }
        return $new_hosts;
    }
    /*
    *
    */
    private function filterHostByPage($host){
        if(isset($this->PAGE_DEF['use_regex']) && $this->PAGE_DEF['use_regex'] == 1){
            // Search Host by regex
            foreach( $this->PAGE_GRAPH as $g ){
                if(isset($g['host_name']) && preg_match('/'.$g['host_name'].'/',$host)){
                    return $host;
                }
            }
        }else{
            foreach( $this->PAGE_GRAPH as $g ){
                $hosts_to_search_for = explode(",", $g['host_name']);
                if(isset($g['host_name']) && in_array($host ,$hosts_to_search_for) ){
                    return $host;
                }
            }
        }
        return FALSE;
    }

    private function filterServiceByPage($g,$host,$service){
        $data = array();
        if(isset($this->PAGE_DEF['use_regex']) && $this->PAGE_DEF['use_regex'] == 1){
            // Search Host by regex
            foreach( $this->PAGE_GRAPH as $g ){
                if(isset($g['host_name']) && preg_match('/'.$g['host_name'].'/',$host)){
                    if(isset($g['service_desc']) && preg_match('/'.$g['service_desc'].'/',$service['name'])){
                        $data['service_desc'] = $g['service_desc'];
                        $data['host_name'] = $g['host_name'];
                        $data['source'] = NULL; 
                        // if we only want a single image 
                        if(isset($g['source'])){
                            $this->readXML($host,$service['name']);
                            $this->includeTemplate($this->DS[0]['TEMPLATE']);
                            $source = intval($g['source']);
                            if(array_key_exists($source,$this->RRD['def'])){
                                $data['source'] = $source;
                            }
                        }
                        return $data;
                    }
                }
            }
        }else{
            $hosts_to_search_for = explode(",", $g['host_name']);
            $services_to_search_for = explode(",", $g['service_desc']);
            if(isset($g['host_name']) && in_array($host ,$hosts_to_search_for) ){
                if(isset($g['service_desc']) && in_array($service['name'] ,$services_to_search_for) ){
                    $data['service_desc'] = $g['service_desc'];
                    $data['host_name'] = $g['host_name'];
                    $data['source'] = NULL;
                    // if we only want a single image 
                    if(isset($g['source'])){
                        $this->readXML($host,$service['name']);
                        $this->includeTemplate($this->DS[0]['TEMPLATE']);
                        $source = intval($g['source']);
                        if(array_key_exists($source,$this->RRD['def'])){
                            $data['source'] = $source;
                        }
                    }
                    return $data;
                }
            }
        }
        return FALSE;
    }

    /*
    *
    */
    public function getPages() {
        $pages = array();
        if (is_dir($this->config->conf['page_dir'])) {
            if ($dh = opendir($this->config->conf['page_dir'])) {
                 while (($file = readdir($dh)) !== false) {
                      if(preg_match('/(.*)\.cfg$/',basename($file),$page)){
                           $pages[] = urlencode($page[1]);
                      }
                 }
                closedir($dh);
            } else {
                die("Cannot open directory:  $path");
            }
        }
        if(sizeof($pages)>0){
            
            natsort($pages);
        }else{
            return FALSE; 
        }
        return $pages;
    }

    /*
    *
    */
    public function getFirstPage(){
        $pages = $this->getPages();
        if(sizeof($pages) > 0 ){
            return urldecode($pages[0]);
        }else{
            return FALSE;
        }
    }

    /*
    *
    */
    public function getPageDetails($page){
        $this->parse_page_cfg($page);
        return $this->PAGE_DEF['page_name'];
    }

    /*
    *
    */
    public function buildXport($host,$service){
        // FIXME add max rows to config
        $this->XPORT = " -m 2000";
        $this->XPORT .= " --start=".$this->TIMERANGE['start'];
        $this->XPORT .= " --end=".$this->TIMERANGE['end'];
        $this->readXML($host,$service);
        $count = 0;
        $RRAs = array('MIN','MAX','AVERAGE');
        foreach($this->DS as $key=>$value){
            foreach($RRAs as $RRA){ 
                $this->XPORT .= sprintf(" DEF:%d%s=%s:%d:%s ",$count,$RRA,$value['RRDFILE'],$value['DS'],$RRA);
                $this->XPORT .= sprintf(" XPORT:%d%s:%s_%s "    ,$count,$RRA,$value['NAME'],$RRA);
            }
            $count++;
        }
    }
    /*
    *
    */
    public function xml2csv($string){
        $xml = simplexml_load_string($string);
        $csv = "timestamp";
        foreach($xml->meta->legend->entry as $key=>$value){
            $csv .= ";" . $value ;
        }

        /*
        * from rrdtool 1.5 on, timestamp ( value->t ) is not set anymore
        * from 1.6 on it can be reactivated by -- showrime option to rrdtool xport
        * here we take care of the timestamp ourselves regardless which 
        * rrdtool version is used
        */
        $timestamp2 = $xml->meta->start;
        $step = $xml->meta->step;
        $csv .= "\n";
        foreach($xml->data->row as $key=>$value){
            $csv .= $timestamp2;
            foreach($value->v as $item){
                $csv .= ";".floatval((string) $item);
            }
            $csv .= "\n";
            $timestamp2 = $timestamp2 + $step;
        }
        return $csv;    
    }

    /*
    * 
    * Used in Special Templates to gather data
    */
    public function tplGetData ($hostname, $servicedesc, $throw_exception=TRUE){
        $conf        = $this->config->conf;
        $xmlfile     = $conf['rrdbase'].$hostname."/".$servicedesc.".xml";
        $data = array();
        if (file_exists($xmlfile)) {
            $xml = simplexml_load_file($xmlfile);
            // Throw excaption without a valid structure version
            if(!isset($xml->XML->VERSION) && $throw_exception == TRUE){
                throw new Kohana_Exception('error.xml-structure-without-version-tag',$xmlfile);
            }
            if(!isset($xml->XML->VERSION) && $throw_exception == FALSE){
                return FALSE;
            }
            foreach ( $xml as $key=>$val ){
                if(preg_match('/^NAGIOS_(.*)$/', $key, $match)){
                    $key = $match[1];
                    $data['MACRO'][$key] = (string) $val;
                }
            }
            $i=0;
            foreach ( $xml->DATASOURCE as $datasource ){
                foreach ( $datasource  as $key=>$val){
                    $data['DS'][$i][$key] = (string) $val;
                }
                $i++; 
            }
            return $data;
        }else{
            throw new Kohana_Exception('error.xml-not-found', $xmlfile);
        }
    }
    /*
    * 
    * Used in Special Templates to gather data
    */
    public function tplGetServices ($hostregex=FALSE, $serviceregex = ''){
        if($hostregex === FALSE){
            return FALSE; 
        }
        $hostregex    = sprintf("/%s/",$hostregex);
        $serviceregex = sprintf("/%s/",$serviceregex);
        $hosts = $this->getHosts();
        $new_hosts = array();
        foreach( $hosts as $host){
            if(preg_match($hostregex,$host['name'])){
                $new_hosts[] = $host['name'];
            }
        }

        if(sizeof($new_hosts) == 0){
            throw new Kohana_Exception('error.tpl-no-hosts-found', $hostregex);
        }

        $i = 0;
        $new_services = array();
        foreach($new_hosts as $host){
            $services = $this->getRawServices($host);
            if(sizeof($services) == 0){
                throw new Kohana_Exception('error.tpl-no-services-found', $serviceregex);
            }
            foreach($services as $service){
                if(preg_match($serviceregex, $service['name'])){
                    $new_services[$i]['hostname'] = $host;
                    $new_services[$i]['host']     = $host;
                    $new_services[$i]['service_description'] = $service['name'];
                    $new_services[$i]['service']             = $service['name'];
                    $i++;
                }
            }
        }

        if(sizeof($new_services) == 0){
            throw new Kohana_Exception('error.tpl-no-services-found', $serviceregex);
        }

        return $new_services;
            
    }
} 

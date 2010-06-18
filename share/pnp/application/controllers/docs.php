<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Docs_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();

        $this->template->docs   = $this->add_view('docs');
        $this->template->docs->search_box    = $this->add_view('search_box');
        $this->template->docs->docs_box    = $this->add_view('docs_box');
        $this->template->docs->logo_box    = $this->add_view('logo_box');
    }

    public function index(){
        url::redirect("docs/view/");        
    }

    public function view($page=FALSE){
        if($page == FALSE)
            url::redirect("docs/view/start"); 

        $this->page = $page;
        $this->lang = $this->config->conf['lang'];
        if($this->lang != "de_DE" || $this->lang != "en_US" ){
            $this->lang = "en_US";
        }
        $file = sprintf("documents/%s/%s.html", $this->lang, $this->page);
        $file_toc  = sprintf("documents/%s/start.html", $this->lang);
        $this->content = file_get_contents($file);
        $toc = file( $file_toc );
        $this->toc = "";
        $in = FALSE; 
        foreach($toc as $t){
            if(preg_match("/SECTION/", $t) && $in == FALSE){
                $in = TRUE;
                continue;
            }
            if(preg_match("/SECTION/", $t) && $in == TRUE){
                $in = FALSE;
                continue;
            }
            if($in == TRUE){
                $this->toc .= $t; 
            }
        }
        #
        # Some String replacements
        #
        $this->toc         = str_replace("/de/pnp-0.6/", "", $this->toc);
        $this->toc         = str_replace("/pnp-0.6/", "", $this->toc);
        $this->toc         = preg_replace("/<h2>.*<\/h2>/", "<h2><a href='start'>Home</a></h2>" , $this->toc);
        $this->content     = str_replace("/de/pnp-0.6/", "", $this->content);
        $this->content     = str_replace("/pnp-0.6/", "", $this->content);
        $this->content     = str_replace("/_media", "../../documents/_media", $this->content);
        $this->content     = str_replace("/_mediagallery", "/_media", $this->content);
        $this->content     = str_replace("/_detail", "../../documents/_media", $this->content);
        $this->graph_width = ($this->config->conf['graph_width'] + 140);
    }
}

<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Docs controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Docs_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
        $this->template                    = $this->add_view('template');
        $this->template->docs              = $this->add_view('docs');
        $this->template->docs->search_box  = $this->add_view('search_box');
        $this->template->docs->docs_box    = $this->add_view('docs_box');
        $this->template->docs->logo_box    = $this->add_view('logo_box');
        $this->doc_language = $this->config->conf['doc_language']; 
    }

    public function index(){
        url::redirect("docs/view/");        
    }

    public function view($lang=FALSE, $page=FALSE){
        if($lang == FALSE){
            if(!in_array($this->config->conf['lang'],$this->doc_language) ){
                $this->lang = $this->doc_language[0];
            }else{
                $this->lang = $this->config->conf['lang'] ;
            }
        }else{
            if(in_array($lang,$this->doc_language) ){
                $this->lang = $lang;
            }else{
                $this->lang = $this->doc_language[0];
                url::redirect("docs/view/");        
            }
        }
        
        if($page == FALSE){
            url::redirect("docs/view/".$this->lang."/start"); 
        }

        $this->page = $page;
        $file = sprintf("documents/%s/%s.html", $this->lang, $this->page);
        $file_toc  = sprintf("documents/%s/start.html", $this->lang);
        if(!file_exists($file)){
            url::redirect("docs/view/start");
        }
        $this->content = file_get_contents($file);
        $toc = file( $file_toc );
        $this->toc = "";
        $in = FALSE; 
        foreach($toc as $t){
            if(preg_match("/SECTION/", $t) ){
                $in = ! $in;
                continue;
            }
            if($in == TRUE){
                $this->toc .= $t; 
            }
        }
        #
        # some string replacements
        #
        $this->toc         = str_replace("/de/pnp-0.6/", "", $this->toc);
        $this->toc         = str_replace("/pnp-0.6/", "", $this->toc);
        $this->toc         = preg_replace("/<h2>.*<\/h2>/", "" , $this->toc);
        $this->content     = str_replace("/templates/", "http://docs.pnp4nagios.org/templates/", $this->content);
        $this->content     = str_replace("/de/pnp-0.6/", "", $this->content);
        $this->content     = str_replace("/pnp-0.6/", "", $this->content);
        $this->content     = str_replace("/_media", url::base()."documents/_media", $this->content);
        $this->content     = str_replace("gallery", "", $this->content);
        $this->content     = str_replace("/_detail", url::base()."documents/_media", $this->content);
        $this->content     = str_replace("/lib/images", url::base()."documents/images", $this->content);
        $this->graph_width = ($this->config->conf['graph_width'] + 140);
    }
}

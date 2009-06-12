<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Base controller.
 *
 * @package pnp4nagios 
 * @author  Joerg Linge
 * @license GPL
 */
class System_Controller extends Template_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->theme_path = Kohana::config('config.theme_path').Kohana::config('config.current_theme');
		$this->theme_url = 'application/views/'.$this->theme_path; 

		# set base template file to current theme
		$this->template = $this->add_view('template');

		$this->data      = new Data_Model();
		$this->config    = new Config_Model();
		$this->rrdtool    = new Rrdtool_Model();
		$this->config->read_config();
	}

	public function __call($method, $arguments)
	{
		// Disable auto-rendering
		$this->auto_render = FALSE;

		// By defining a __call method, all pages routed to this controller
		// that result in 404 errors will be handled by this method, instead of
		// being displayed as "Page Not Found" errors.
		echo $this->_("The requested page doesn't exist") . " ($method)";
	}

	/**
	 * Handle paths to current theme etc
	 *
	 */
	public function add_view($view=false)
	{
		$view = trim($view);
		if (empty($view)) {
			return false;
		}
		return new View($this->theme_path.$view);
	}

	/**
	 * Set correct image path considering
	 * the path to current theme.
	 */
	public function img_path($rel_path='')
	{
		return $this->add_path($rel_path);
	}

	/**
	 * Set correct image path considering
	 * the path to current theme.
	 */
	public function add_path($rel_path)
	{
		$rel_path = trim($rel_path);
		if (empty($rel_path)) {
			return false;
		}

		$path = false;
		# assume rel_path is relative from current theme
		$path = 'application/views/'.$this->theme_path.$rel_path;
		# make sure we didn't mix up start/end slashes
		$path = str_replace('//', '/', $path);
		return $path;
	}

    public function isAuthorizedFor($auth) {
        $conf = $this->config->conf;
        if ($auth == "service_links") {

                $users = explode(",", $conf['allowed_for_service_links']);
                if (in_array('EVERYONE', $users)) {
                        return 1;
                }
                elseif (in_array('NONE', $users)) {
                        return 0;
                }
                elseif (in_array($_SERVER["REMOTE_USER"], $users)) {
                        return 1;
                } else {
                        return 0;
                }
        }
        if ($auth == "host_search") {
                $users = explode(",", $conf['allowed_for_host_search']);
                if (in_array('EVERYONE', $users)) {
                        return 1;
                }
                elseif (in_array('NONE', $users)) {
                        return 0;
                }
                elseif (in_array($_SERVER["REMOTE_USER"], $users)) {
                        return 1;
                } else {
                        return 0;
                }
        }
        if ($auth == "host_overview") {
                $users = explode(",", $conf['allowed_for_host_overview']);
                if (in_array('EVERYONE', $users)) {
                        return 1;
                }
                elseif (in_array('NONE', $users)) {
                        return 0;
                }
                elseif (in_array($_SERVER["REMOTE_USER"], $users)) {
                        return 1;
                } else {
                        return 0;
                }
        }
        if ($auth == "pages") {
                $users = explode(",", $conf['allowed_for_pages']);
                if (in_array('EVERYONE', $users)) {
                        return 1;
                }
                elseif (in_array('NONE', $users)) {
                        return 0;
                }
                elseif (in_array($_SERVER["REMOTE_USER"], $users)) {
                        return 1;
                } else {
                        return 0;
                }
        }
    }
}

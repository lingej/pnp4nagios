<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default controller.
 *
 * @package    pnp4nagios 
 * @author     Joerg Linge
 * @license    GPL
 */
class Start_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
            url::redirect("graph", 302);
    }

}

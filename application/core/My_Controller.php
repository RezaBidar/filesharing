<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * My Base Controller
 *
 * All controller extends this controller
 * Every code whitin this class will run in all controller
 * 
 * @package  FileSharing
 */
class My_Controller extends CI_Controller{

	public $data = array() ; // load in layout view
	

	public function __construct()
	{
		parent::__construct();

		// this array inject in views manually 
		$this->data['c_data'] = array() ; 
		$this->data['site_url'] = site_url() ;


	}
}
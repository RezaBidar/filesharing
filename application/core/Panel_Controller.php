<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * My Panel Base Controller
 *
 * All panel controller extends this controller
 * All code that depend on login-check run in this class
 * 
 * @package  FileSharing
 */
class Panel_Controller extends My_Controller{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		if(!$this->m_user->loggedin())
			redirect('sign/in');
	}
}
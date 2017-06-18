<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Login and Logout class
*
* This class contain panel-user authentication and register method
*
* @package FileSharing
* 
*/
class Sign extends My_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
	}

	/**
	 * login controller
	 *
	 * if form submitted then check user data and 
	 * if data was correct redirect it to dashboard else show error
	 */
	public function in()
	{
		// if user loggedin redirect to dashboard page
		if($this->m_user->loggedin()) 
			redirect('panel/dashboard');
		//set form validation
		$rules = array(
			'email' => array( 'field' => 'email' , 'label' => $this->lang->line('app_form_email') , 'rules' => 'required|trim|valid_email' ) ,
			'password' => array( 'field' => 'password' , 'label' => $this->lang->line('app_form_password') , 'rules' => 'required|trim' ) ,
			);
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			$data = array(
				'email' => $this->input->post('email') ,
				'password' => $this->input->post('password') ,
				) ;
			//if true check user pass
			if($this->m_user->login($data))
			{
				//if ture redirect to dashboard
				redirect('panel/dashboard') ;		
			}
			else
			{
				$this->session->set_flashdata('error', 'Email or Password is incorrect');
			}
		}
		else
		{
			$this->session->set_flashdata('error', validation_errors('<span>' , '</span><br/>'));
		}

		//load view
		$this->data['content_view'] = 'signin' ;
		$this->load->view('layouts/public_layout', $this->data);
	}

	/**
	 * SingUp controller
	 *
	 * get user data and 
	 * if data was valid then save it in system and redirect it to login page 
	 * else show error
	 *
	 * Notice : there is no policy for password
	 */
	public function up()
	{

		//set form validation
		$rules = array(
			'email' => array( 'field' => 'email' , 'label' => $this->lang->line('app_form_email') , 'rules' => 'required|trim|valid_email|is_unique[user.' . m_user::COLUMN_EMAIL . ']' ) ,
			'password' => array( 'field' => 'password' , 'label' => $this->lang->line('app_form_password') , 'rules' => 'required|trim' ) ,
			'password_conf' => array( 'field' => 'password_conf' , 'label' => $this->lang->line('app_form_password_conf') , 'rules' => 'required|trim|matches[password]' ) ,
			);
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			$data = array(
				'email' => $this->input->post('email') ,
				'password' => $this->input->post('password') ,
				) ;
			$this->m_user->signup($data) ;
			redirect('sign/in');

		}
		else
		{
			$this->session->set_flashdata('error', validation_errors('<span>' , '</span><br/>'));
		}
		
		//load view
		$this->data['content_view'] = 'signup' ;
		$this->load->view('layouts/public_layout', $this->data);
	}

	/**
	 * Sign out controller
	 *
	 * clear session data and redirect to login page
	 */
	public function out()
	{
		$this->m_user->logout();
		redirect('sign/in')	;
	}
}

?>
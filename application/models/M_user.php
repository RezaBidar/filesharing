<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * user and repository model
 * 
 * @package FileSharing       
 */
class m_user extends My_Model{

	// table column names
	const COLUMN_ID = 'usr_id' ;
	const COLUMN_NAME = 'usr_name' ;
	const COLUMN_EMAIL = 'usr_email' ;
	const COLUMN_PASSWORD = 'usr_password' ;
	const COLUMN_SPACE = 'usr_space' ;
	
	protected $_table_name = 'user';
	protected $_primary_key = 'usr_id';

	/**
	 * login user
	 *
	 * get email and password in data array
	 * and check if user exist then set session
	 * @param  array 	$data 	it contains 
	 * @return bollean     		if data was correct and user login return true otherwise return false
	 */
	public function login($data)
	{
		$where = array(
			self::COLUMN_EMAIL => $data['email'],
			self::COLUMN_PASSWORD => $this->hash($data['password']),	
		);
		$user = $this->get_by($where,true);
		if($user !== NULL)
		{
			$session_data = array(
				"email" => $user->{self::COLUMN_EMAIL},
				"user_id" => $user->{self::COLUMN_ID} ,
				"logged_in" => TRUE ,
			);
			$this->session->set_userdata($session_data);
			return TRUE;
		}
		return FALSE ;
	}

	/**
	 * save user to database
	 * @param  array 	$data_arr 	it contains email and password
	 * @return int      		    user id
	 */
	public function signup($data_arr)
	{
		
		$data = array(
			self::COLUMN_EMAIL => $data_arr['email'] ,
			self::COLUMN_PASSWORD => $this->hash($data_arr['password']) 
		);
		return $this->save($data) ;
	}
	
	/**
	 * unset session data
	 */
	public function logout()
	{
		$session_data = array('email', 'logged_in' , 'user_id');
		$this->session->unset_userdata($session_data);
	}
	
	/**
	 * check user logged in or not
	 * @return boolean 
	 */
	public function loggedin()
	{
		return (bool) $this->session->userdata('logged_in');
	}
	
	/**
	 * add salt and hash it
	 * @param  string $input 
	 * @return string        
	 */
	public function hash($input)
	{
		return md5($input . config_item('encryption_key'));
	}

	/**
	 * return an array that contain repository information
	 * @param  int $user_id 
	 * @return array          
	 */
	public function get_panel_information($user_id)
	{	
		$user = $this->get($user_id) ;
		if($user === NULL)
			return NULL ;
		$panel = array(
			'size' => $user->{self::COLUMN_SPACE} ,
			'max_file' => $user->{self::COLUMN_MAX_FILE} ,
			'max_folder' => $user->{self::COLUMN_MAX_FOLDER} ,
			);
		return $panel ;
	}

	/**
	 * get email and return user id
	 * @param  string $email 
	 * @return int        
	 */
	public function get_id_by_email($email)
	{
		$this->db->where(self::COLUMN_EMAIL , $email);
		$user = $this->get(NULL , TRUE);
		if($user != NULL)
		{
			return $user->{self::COLUMN_ID};
		}
		return NULL ;
	}

	/**
	 * get user id and return email
	 * @param  int $id 
	 * @return string     
	 */
	public function get_email_by_id($id)
	{
		$user = $this->get($id , TRUE);
		if($user != NULL)
		{
			return $user->{self::COLUMN_EMAIL};
		}
		return NULL ;
	}

	/**
	 * generate a usage line in bootstrap format
	 * @param  int $user_id 
	 * @return string        
	 */
	public function get_space_usage_line($user_id)
	{
		$CI =& get_instance();
		$CI->load->model('m_file') ;
		$user = $this->get($user_id , TRUE);
		if($user == NULL)
			return NULL ;
		$space = $user->{self::COLUMN_SPACE} * 1000 ; // convert to kilobyte
		$usage = $CI->m_file->get_usage_space($user_id) ;
		$percent_usage = ((int)$usage / (int)$space) * 100 ;
		$color = ($percent_usage < 70)? 'success' : (($percent_usage < 90)? 'warning' : 'danger') ;
		$output = 'REPOSITORY SPACE USAGE <span class="label label-info">' . digital_size_show($user->{self::COLUMN_SPACE} , 'mb') . ' / ' . digital_size_show($usage , 'kb') . '</span>';
		$output .= '<div class="progress">' ;
  		$output .= '<div class="progress-bar progress-bar-'. $color .'" role="progressbar" aria-valuenow="'. $percent_usage .'" aria-valuemin="0" aria-valuemax="100" style="width: '. $percent_usage .'%">';
  		$output .= '<span class="sr-only">'. $percent_usage .'% Used</span>' ;
  		$output .= '</div>' ;
  		$output .= '</div>' ;


  		return $output ;

	}

	/**
	 * return repository space
	 * @param  int $user_id 
	 * @return int          
	 */
	public function get_space($user_id = '')
	{
		$user = $this->get($user_id , TRUE);
		if($user == NULL)
			return NULL ;
		return $user->{self::COLUMN_SPACE};
	}


}

?>
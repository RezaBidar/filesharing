<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * access file model
 *
 * this table is contain user ids that can access to a specific file
 * @package FileSharing       
 */
class m_access_file extends My_Model{

	//table column names
	const COLUMN_ID = 'acf_id' ;
	const COLUMN_USER_ID = 'acf_user_id' ;
	const COLUMN_FILE_ID = 'acf_file_id' ;
	
	protected $_table_name = 'access_file';
	protected $_primary_key = 'acf_id';

	/**
	 * get file and user id and save it to access file table
	 * @param int $file_id 
	 * @param int $user_id 
	 */
	public function add_user($file_id, $user_id)
	{
		$data = array(
			self::COLUMN_FILE_ID => $file_id ,
			self::COLUMN_USER_ID => $user_id ,
			);
		$acf = $this->get_by($data , TRUE) ;
		if($acf == NULL)
			return $this->save($data) ;
		return $acf->{self::COLUMN_ID} ;
	}

	/**
	 * list of user that can access to this file
	 * @param  string $file_id 
	 * @return string          return with html in bootstrap format
	 */
	public function get_user_table($file_id)
	{
		$CI =& get_instance();
		$CI->load->model('m_user');

		$users = $this->get_user_list($file_id);

		$output = '<div class="panel panel-info">' ;
		$output .= '<div class="panel-heading">These users have access to this file </div>';
		$output .= '<div class="panel-body">' ;
		
                
		foreach ($users as $user) 
		{
			//<p><a href="#"><span class="fa fa-remove" style="color:red"></span></a> rezabidar@live.com <span class="label label-danger">read/write</span></p>
            //<hr/>
            $output .= '<p>';
			$output .= ' ' . $user->{m_user::COLUMN_EMAIL} . ' ' ;
			$output .= '</p>';
			$output .= '<hr/>' ;
		}

		$output .= '</div>' ;
		$output .= '</div>' ;
		
		return $output ;

	}

	/**
	 * access user list
	 * return array that contain all user that access to this file
	 * @param  int $file_id 
	 * @return array        
	 */
	public function get_user_list($file_id)
	{
		$CI =& get_instance();
		$CI->load->model('m_user');

		$this->db->join('user' , m_user::COLUMN_ID . ' = ' . self::COLUMN_USER_ID) ;
		$this->db->where(self::COLUMN_FILE_ID , $file_id);
		$users = $this->get() ;
		return $users ;
	}

	/**
	 * check user has access to this file or not
	 *
	 * if link of file was public then return true
	 * else file is private and user_id == null then return false
	 * if user_id is owner of file's repository then return true
	 * if user_id is in access_file table for this file then return true
	 * if user_id is a member of file's repository and have read access then return true
	 * otherwise return false 
	 * @param  int  $file_id 
	 * @param  int  $user_id 
	 * @return boolean       
	 */
	public function has_access($file_id , $user_id = NULL)
	{
		$CI =& get_instance();
		$CI->load->model('m_file') ;
		$CI->load->model('m_access_user') ;
		$file = $CI->m_file->get($file_id , TRUE);

		if($file == NULL)
			return FALSE ;
		if($file->{m_file::COLUMN_LINK_MODE} == m_file::LINK_MODE_PUPBLIC)
			return TRUE ;
		if($user_id == NULL)
			return FALSE ;
		if($user_id == $file->{m_file::COLUMN_USER_ID})
			return TRUE ;

		$this->db->where(self::COLUMN_USER_ID , $user_id);
		$this->db->where(self::COLUMN_FILE_ID , $file_id);
		if($this->get(NULL,TRUE) != NULL)
			return TRUE ;

		if($CI->m_access_user->has_access($file->{m_file::COLUMN_USER_ID} , $user_id , m_access_user::LEVEL_READ))
			return TRUE ;

		return FALSE ;
		
	}

}
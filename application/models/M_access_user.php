<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * access user model
 *
 * this table contain user that can access to other repository
 * @package FileSharing       
 */
class m_access_user extends My_Model{

	//table column names
	const COLUMN_ID = 'acu_id' ;
	const COLUMN_LEVEL = 'acu_level' ;
	const COLUMN_USER_ID = 'acu_user_id' ;
	const COLUMN_FRIEND_ID = 'acu_friend_id' ;

	protected $_table_name = 'access_user';
	protected $_primary_key = 'acu_id';

	const LEVEL_READ = '1' ;
	const LEVEL_READ_WRITE = '2' ;
	const LEVEL_READ_WRITE_EDIT = '3' ;

	/**
	 * friend list
	 * return all user that can access to this repository
	 * Notice : repository and user is same in this system
	 * @param  int $user_id 
	 * @return array        
	 */
	public function get_friend_list($user_id)
	{
		$CI =& get_instance();
		$CI->load->model('m_user');

		$this->db->join('user' , m_user::COLUMN_ID . ' = ' . self::COLUMN_FRIEND_ID) ;
		$this->db->where(self::COLUMN_USER_ID , $user_id);
		$friends = $this->get() ;
		return $friends ;
	}

	/**
	 * number of user that this user can access it
	 *
	 * return number of repositories that this user_id can access it
	 * @param  int $user_id 
	 * @return int          
	 */
	public function get_repository_count($user_id)
	{
		$this->db->where(self::COLUMN_FRIEND_ID , $user_id);		
		return sizeof($this->get()) ;
	}

	/**
	 * firend list in bootstrap format
	 *
	 * return all user that can access tho this repository
	 * Notice : $user_id is same as $repository_id
	 * @param  int $user_id 
	 * @return string       
	 */
	public function get_friend_table($user_id)
	{
		$CI =& get_instance();
		$CI->load->model('m_user');

		$this->db->order_by(self::COLUMN_LEVEL , 'DESC');
		$friends = $this->get_friend_list($user_id);
		$output = '<div class="panel panel-info">' ;
		$output .= '<div class="panel-heading">Members ';
		if($user_id == $CI->session->userdata('user_id'))
			$output .= '<a href="'. site_url('panel/dashboard/add_user') .'"><span class="fa fa-plus fa-2x pull-right" style="color:green"></span></a>';
		$output .= '</div>';
		$output .= '<div class="panel-body">' ;
		
        $output .= '<p>';
		$output .= ' ' . $CI->m_user->get_email_by_id($user_id) . ' ' ;
		$output .= '<span class="label label-primary">Owner</span>' ;
		$output .= '<hr/>' ;
		$output .= '</p>' ;

		foreach ($friends as $friend) 
		{
			//<p><a href="#"><span class="fa fa-remove" style="color:red"></span></a> rezabidar@live.com <span class="label label-danger">read/write</span></p>
            //<hr/>
            $output .= '<p>';
            if($user_id == $CI->session->userdata('user_id'))
				$output .= '<a href="'. site_url('panel/dashboard/remove_user/' . $friend->{m_user::COLUMN_ID} ).'"><span class="fa fa-remove" style="color:red"></span></a>' ;
			$output .= ' ' . $friend->{m_user::COLUMN_EMAIL} . ' ' ;
			if($friend->{self::COLUMN_LEVEL} == self::LEVEL_READ)
				$output .= '<span class="label label-warning">';
			else
				$output .= '<span class="label label-danger">';
			$output .= $this->get_level_name($friend->{self::COLUMN_LEVEL}) .'</span>' ;
			$output .= '</p>' ;
			$output .= '<hr/>' ;
		}

		$output .= '</div>' ;
		$output .= '</div>' ;
		
		return $output ;

	}

	/**
	 * save to access_user table
	 * 
	 * add user($firend_id) to access_user table for specific repository($user_id)
	 * and level can read or read/write
	 * @param int $user_id   
	 * @param int $friend_id 
	 * @param int $level     level code // i define it as constant
	 */
	public function add_friend($user_id, $friend_id, $level)
	{
		$where = array(
				self::COLUMN_USER_ID => $user_id ,
				self::COLUMN_FRIEND_ID => $friend_id ,
			); 
		$obj = $this->get_by($where, TRUE) ;
		if($obj != NULL)
		{
			$data = array(
					self::COLUMN_LEVEL => $level ,
				);
			return $this->save($data, $obj->{self::COLUMN_ID});	
		}
		else
		{
			$data = array(
					self::COLUMN_USER_ID => $user_id ,
					self::COLUMN_FRIEND_ID => $friend_id ,
					self::COLUMN_LEVEL => $level ,
				);
			return $this->save($data);
		}
	}

	/**
	 * delete from user_access table
	 * clear access of user($friend_id) from repository($user_id)
	 * @param  int $user_id   [description]
	 * @param  int $friend_id [description]
	 * @return [type]            [description]
	 */
	public function remove_friend($user_id, $friend_id)
	{
		$this->db->where(self::COLUMN_USER_ID , $user_id);
		$this->db->where(self::COLUMN_FRIEND_ID , $friend_id);
		$acu = $this->get(NULL , TRUE);
		if($acu != NULL)
			$this->delete($acu->{self::COLUMN_ID});
	}

	/**
	 * 
	 * check user($friend_id) has access to repository($user_id) or not
	 * @param  int $user_id   
	 * @param  int $friend_id 
	 * @return boolean             
	 */
	public function are_friend($user_id, $friend_id)
	{
		$this->db->where(self::COLUMN_USER_ID , $user_id);
		$this->db->where(self::COLUMN_FRIEND_ID , $friend_id);
		$acu = $this->get(NULL , TRUE);
		return ($acu != NULL) ? TRUE : FALSE ;
	}

	/**
	 * return levels array
	 * it used in bootstrap select input
	 * @return array
	 */
	public function get_levels()
	{
		return array(
			self::LEVEL_READ => $this->lang->line('app_form_level_read') , 
			self::LEVEL_READ_WRITE => $this->lang->line('app_form_level_read_write'),
			self::LEVEL_READ_WRITE_EDIT => $this->lang->line('app_form_level_read_write_edit')
			);
	}

	/**
	 * get level code and return level name
	 * @param  int $level_id int
	 * @return int           int
	 */
	public function get_level_name($level_id)
	{
		$levels = $this->get_levels();
		return $levels[$level_id] ;
	}

	/**
	 * return a list of all repositories that this user can access it
	 * @param  int $user_id 
	 * @return int          
	 */
	public function get_repository_table($user_id)
	{
		$this->db->join('user', m_user::COLUMN_ID . ' = ' . self::COLUMN_USER_ID);
		$this->db->where(self::COLUMN_FRIEND_ID , $user_id);
		$list = $this->get();
		$output = '<ul>' ;
		foreach ($list as $user) 
		{
			$output .= '<li>' ;
			$output .= '<a href="' . site_url('panel/dashboard/repository_content/' . $user->{m_user::COLUMN_ID}) . '">' ;
			$output .= $user->{m_user::COLUMN_EMAIL} . ' - ' . '( '. $this->get_level_name($user->{self::COLUMN_LEVEL}).' )' ;
			$output .= '</a>' ;
			$output .= '</li>' ;
		}
		$output .= '</ul>' ;
		return $output ;
	}

	/**
	 * check user can access to repository with that level
	 * if user is owner of repository return true
	 * if user has added before to access_user table for this repository return true
	 * otherwise return false 
	 * @param  int  $repository_id 
	 * @param  int  $user_id       
	 * @param  int  $level         
	 * @return boolean             
	 */
	public function has_access($repository_id,$user_id,$level = self::LEVEL_READ)
	{
		if($repository_id == $user_id)
			return TRUE ;
		$this->db->where(self::COLUMN_FRIEND_ID , $user_id);
		$this->db->where(self::COLUMN_USER_ID , $repository_id);
		$acu = $this->get(NULL,TRUE);
		if($acu == NULL)
			return FALSE ;
		if($acu->{self::COLUMN_LEVEL} >= $level)
			return TRUE ;
		return FALSE ;
	}

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * file model
 *
 * this table contain all information of files that stored in our system
 * @package FileSharing       
 */
class m_file extends My_Model{


	//table columns name 
	const COLUMN_ID = 'fil_id' ;
	const COLUMN_NAME = 'fil_name' ;
	const COLUMN_TYPE = 'fil_type' ;
	const COLUMN_FILE_TYPE = 'fil_file_type' ;
	const COLUMN_FILE_EXT = 'fil_file_ext' ;
	const COLUMN_FILE_SIZE = 'fil_file_size' ;
	const COLUMN_HASH_NAME = 'fil_hash_name' ;
	const COLUMN_LINK_ID = 'fil_link_id' ;
	const COLUMN_LINK_MODE = 'fil_link_mode' ;
	const COLUMN_IS_IMAGE = 'fil_is_image' ;
	const COLUMN_IMAGE_TYPE = 'fil_image_type' ;
	const COLUMN_IMAGE_WIDTH = 'fil_image_width' ;
	const COLUMN_IMAGE_HEIGHT = 'fil_image_height' ;
	const COLUMN_USER_ID = 'fil_user_id' ;
	const COLUMN_PARENT_ID = 'fil_parent_id' ;
	
	protected $_table_name = 'file';
	protected $_primary_key = 'fil_id';

	const LINK_MODE_PUPBLIC = '0' ;
	const LINK_MODE_PRIVATE = '1' ;
	const TYPE_FILE = '1' ;
	const TYPE_FOLDER = '2' ;

	/**
	 * return file tree of specific repository and folder
	 * 
	 * Notice : parent_id and folder_id is same because only folder can be parent
	 * 
	 * @param  int $user_id  
	 * @param  int $parent_id
	 * @return string            bootstrap format
	 */
	public function get_repository_table($user_id, $parent_id = '')
	{	

		$CI =& get_instance();

		if($parent_id != '')
		{
			$this->db->where(self::COLUMN_PARENT_ID , $parent_id);	
		}
		else
		{
			$this->db->where(self::COLUMN_PARENT_ID . ' IS NULL' , null , false);
		}

		$this->db->where(self::COLUMN_USER_ID , $user_id);
		$this->db->order_by(self::COLUMN_TYPE , 'DESC' ) ;
		$this->db->order_by(self::COLUMN_NAME , 'ASC' ) ;
		$content_list = $this->get();
		$output = '<ul class="list-group">' ;
		if($parent_id != '')
		{
			$output .= '<li class="list-group-item">' ;
			$output .= '<span class="fa fa-folder-open fa-2x" style="color:lightblue"></span>' ;
			$output .= ' <a href="'. site_url('panel/dashboard/repository_content/'. $user_id . '/' . $this->parent_of($parent_id)).'" style="color:black"> ... </a>' ;
			$output .= '</li>';
		}
		
		if(sizeof($content_list) == 0)
		{
			$output .= '<li class="list-group-item">There is noting to show . Upload your files ...</li>';
		}

		$write_access = $CI->m_access_user->has_access($user_id , $CI->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE);
		foreach ($content_list as $row) 
		{
			if($row->{self::COLUMN_TYPE}  == self::TYPE_FILE)
			{

				$output .= '<li class="list-group-item">' ;
				$output .= '<span class="fa fa-file fa-2x" style="color:#555573"></span>' ;
				$output .= ' <a href="'. site_url('panel/dashboard/file_view/' . $row->{self::COLUMN_ID}).'" style="color:black"> ';
				$output .=  $row->{self::COLUMN_NAME} . ' (<small>' . digital_size_show($row->{self::COLUMN_FILE_SIZE} , 'kb') . '</small>) </a>' ;
				if($write_access)
					$output .= '<div class="pull-right"><a href="'. site_url('panel/dashboard/remove_file/' . $row->{self::COLUMN_ID}).'" class="fa fa-remove fa-2x" style="color:red"></a></div>' ;
				$output .= '</li>';
			}
			else if($row->{self::COLUMN_TYPE}  == self::TYPE_FOLDER)
			{
				$output .= '<li class="list-group-item">' ;
				$output .= '<span class="fa fa-folder fa-2x" style="color:#6098AB"></span>' ;
				$output .= ' <a href="'. site_url('panel/dashboard/repository_content/'. $user_id . '/' . $row->{self::COLUMN_ID}).'" style="color:black"> '. $row->{self::COLUMN_NAME} .'</a>' ;
				if($write_access)
					$output .= '<div class="pull-right"><a href="'. site_url('panel/dashboard/remove_file/' . $row->{self::COLUMN_ID}).'" class="fa fa-remove fa-2x" style="color:red"></a></div>' ;
				$output .= '</li>';

			} 
		}

		$output .= '</ul>' ;
		return $output ;
		
	}

	/**
	 * return a breadcrumb for see parent of folders
	 * html are in bootstrap format
	 * @param  int $user_id   //repository_id
	 * @param  string $folder_id 
	 * @return string            
	 */
	public function get_parent_breadcrumb($user_id, $folder_id = '')
	{
		if($folder_id == '')
		{
			return '<ol class="breadcrumb"><li class="active">Root</li></ol>' ;
		}
		$active_folder = $folder_id ;
		$folders = array() ;
		do{
			$folder = $this->get($folder_id, TRUE);
			if($folder == NULL) 
				break ;
			$parent_id = $folder->{self::COLUMN_PARENT_ID} ;
			$folder_id = $parent_id ;
			if($folder_id != NULL)
				array_push($folders , $folder_id) ;
		}while($parent_id != NULL);

		$output = '<ol class="breadcrumb">' ;
		$output .= '<li><a href="'. site_url('panel/dashboard/repository_content/'. $user_id) .'">Root</a></li>' ;
		for($i = sizeof($folders) - 1 ; $i >= 0 ; $i--)
		{
			$output .= '<li><a href="'. site_url('panel/dashboard/repository_content/'. $user_id . '/' . $folders[$i]) .'">'
				. $this->get_name($folders[$i]) .'</a></li>' ;

		}
		$output .= '<li class="active">'. $this->get_name($active_folder) .'</li>' ;

		$output .= '</ol>' ;
		return $output ;

	}

	/**
	 * get id of file and return name of it
	 * @param  int $id 
	 * @return string     
	 */
	public function get_name($id)
	{
		$resource = $this->get($id,TRUE);
		if($resource != NULL)
			return $resource->{self::COLUMN_NAME} ;
	}

	/**
	 * delete resource
	 * if resource type be file then remove first all access_file row and then remove file
	 * if resource type be folder then remove first all file that this folder is parent of them then remove folder
	 * @param  int $id resource id
	 * @return boolean
	 */
	public function delete($id)
	{
		$CI =& get_instance();
		$CI->load->model('m_access_file') ;
		$resourse = $this->get($id,TRUE) ;
		if($resourse == NULL) return NULL ;
		
		if($resourse->{self::COLUMN_TYPE} == self::TYPE_FILE)
		{
			$CI->m_access_file->delete(array(m_access_file::COLUMN_FILE_ID => $id)) ;
			parent::delete($id);
			@unlink('../files/' . $resourse->{self::COLUMN_HASH_NAME});
		}
		else if($resourse->{self::COLUMN_TYPE} == self::TYPE_FOLDER)
		{
			// should start transaction in controller
			$this->db->where(self::COLUMN_PARENT_ID , $resourse->{self::COLUMN_ID});
			$this_folder_content = $this->get();
			foreach ($this_folder_content as $row) 
			{
				$this->delete($row->{self::COLUMN_ID});
			}
			return parent::delete($id);
		}

	}

	/**
	 * check this file name is in this folder or not
	 * user_id is repository id and parent_id is folder id
	 * @param  int  $user_id   
	 * @param  string  $name      
	 * @param  int  $parent_id 
	 * @return boolean            
	 */
	public function is_overwrite($user_id, $name, $parent_id = '')
	{

		$this->db->where(m_file::COLUMN_USER_ID , $user_id) ;
		$this->db->where(m_file::COLUMN_NAME , $name) ;
		
		if(is_numeric($parent_id))
			$this->db->where(m_file::COLUMN_PARENT_ID , $parent_id) ;
		else
			$this->db->where(self::COLUMN_PARENT_ID . ' IS NULL' , null , false);
		
		$file = $this->m_file->get(NULL , TRUE);

		return $file ;
	}

	/**
	 * return parent id of this resource id
	 * if resource doesnt has parent return null
	 * @param  int $id 
	 * @return int     
	 */
	public function parent_of($id)
	{
		$resourse = $this->get($id);
		if($resourse != NULL)
			return $resourse->{self::COLUMN_PARENT_ID} ;
		return NULL ;
	}

	/**
	 * generate a zip file-tree list 
	 * @param  array $tree   
	 * @param  int   $zip_id // zip file id
	 * @return string         
	 */
	public function get_zip_table($tree, $zip_id)
	{
		$output = '<div id="jstree_div">' ;
		$output .= $this->tree_arr_to_li($tree, $zip_id) ;
		$output .= '</div>';
		return $output ;
	}

	/**
	 * recursively generate zip_tree list in html format
	 * @param  array $arr  
	 * @param  int $zip_id 
	 * @return string         
	 */
	private function tree_arr_to_li($arr, $zip_id)
	{
		$output = '<ul>';
		foreach ($arr as $key => $value) 
		{
			if(is_array($value))
			{
				$output .= '<li>' . $key;
				$output .= $this->tree_arr_to_li($value, $zip_id) ;
				$output .= '</li>' ;
			}
			else
			{
				$output .= '<li data-jstree=\'{"icon":"fa fa-file "}\' ><a href="'. site_url('panel/dashboard/download_file/'. $zip_id . '/' . $key ) .'">' . $value . '</a></li>';
			}
		}
		$output .= '</ul>';
		return $output ;

	}

	/**
	 * return space usage of specific repository
	 * sum of all file_size from spacific $user_id(repository)
	 * @param  int $user_id 
	 * @return int          
	 */
	public function get_usage_space($user_id)
	{
		$this->db->select_sum(self::COLUMN_FILE_SIZE);
		$this->db->group_by(self::COLUMN_USER_ID);
		$this->db->where(self::COLUMN_USER_ID , $user_id);

		$usage = $this->get(NULL,TRUE);
		return ($usage) ? $usage->{self::COLUMN_FILE_SIZE} : NULL ;
	}


}
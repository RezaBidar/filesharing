<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Public Site Controller
 *
 * this class does not check user logged or not 
 * 
 * @package FileSharing
 */
class Site extends My_Controller{

	public function __construct()
	{
		parent::__construct();
		//load models
		$this->load->model('m_user');
		$this->load->model('m_file');
		$this->load->model('m_access_user');
		$this->load->model('m_access_file');

		//load helpers
		$this->load->helper('btform');

		

	}

	/**
	 * public file view controller
	 * get link and show file if exists
	 * if file was private then check user have access to this file or not
	 * @param  string $link_id 
	 */
	public function file_view($link_id = '')
	{	
		
		if($link_id == '')
			die('Link id is required') ;
		//get file 
		$file = $this->m_file->get_by(array(m_file::COLUMN_LINK_ID => $link_id), TRUE);
		//if is not file die
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
		{
			$this->data['c_data']['message'] = 'Your link is invalid !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}


		//check access file
		if(!$this->m_access_file->has_access($file->{m_file::COLUMN_ID},$this->session->userdata('user_id')) )
		{
			$this->data['c_data']['message'] = 'You dont have access to see this file !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}

		//if is image show image info
		$file_info = array(
			'name' => $file->{m_file::COLUMN_NAME} ,
			'size' => $file->{m_file::COLUMN_FILE_SIZE} ,
			'is_image' => FALSE ,
			'is_zip' => FALSE ,
			'is_text' => FALSE ,
			'link' => $file->{m_file::COLUMN_LINK_ID} ,
		);	
		
		if($file->{m_file::COLUMN_IS_IMAGE} == TRUE)
		{
			$file_info['is_image'] = TRUE ;
			$file_info['height'] = $file->{m_file::COLUMN_IMAGE_HEIGHT} ;
			$file_info['width'] = $file->{m_file::COLUMN_IMAGE_WIDTH} ;
			$file_info['thumbnail_link'] = site_url('site/image_thumbnail/' . $link_id) ;
		}
		else if($file->{m_file::COLUMN_FILE_EXT} == '.zip') //if is zip show zip info
		{
			$file_info['is_zip'] = TRUE ;

			$zip = new My_zip();
			if ($zip->open('../files/' . $file->{m_file::COLUMN_HASH_NAME}) !== TRUE) 
			{
				$file_info['is_zip'] = FALSE ;				
				$this->session->set_flashdata('error', 'System can not open this zip file');
			}
			else
				$this->data['c_data']['zip_tree'] = $this->m_file->get_zip_table($zip->getTree() , $file->{m_file::COLUMN_ID}) ;
		}
		else if($file->{m_file::COLUMN_FILE_EXT} == '.txt') //if is zip show zip info
		{
			$file_info['is_text'] = TRUE ;
			$this->data['c_data']['text'] = file_get_contents('../files/' . $file->{m_file::COLUMN_HASH_NAME} , NULL , NULL , 0 , 500) . '...';
		}

		$this->data['c_data']['file_info'] = $file_info ;
		$this->data['content_view'] = 'public_file_view' ;
		$this->load->view('layouts/public_layout' , $this->data);
	}


	/**
	 * image thumbnail show
	 * get link and show image if exists
	 * if file was private then check user have access to this file or not
	 * @param  string $link_id 
	 */
	public function image_thumbnail($link_id = '')
	{		
		
		if($link_id == '')
			die('link id is required') ;
		//get file 
		$image = $this->m_file->get_by(array(m_file::COLUMN_LINK_ID => $link_id), TRUE) ;
		if($image == NULL OR $image->{m_file::COLUMN_IS_IMAGE} == FALSE)
		{
			$this->data['c_data']['message'] = 'Your link is invalid !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}
			

		//check access file
		if(!$this->m_access_file->has_access($image->{m_file::COLUMN_ID},$this->session->userdata('user_id')) )
		{
			$this->data['c_data']['message'] = 'You dont have access to see this file !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}

		

		//it set in config/my_app_config.php 
		$config = $this->config->item('my_app_image_lib_config') ;
		$config['source_image'] = '../files/' . $image->{m_file::COLUMN_HASH_NAME};

		$this->load->library('image_lib', $config);

		if(!$this->image_lib->resize())
		{
			echo $this->image_lib->display_errors();
		}
		

	}


	/**
	 * public download file controller
	 * get link and show file if exists
	 * if file was private then check user have access to this file or not
	 * if zip_index pass to this controller then it download file inside zip with that index
	 * @param  string $link_id 
	 * @param  string $zip_index [file index in zip file]
	 */
	public function download_file($link_id = '', $zip_index = '')
	{
		if($link_id == '')
			die('Link id is required') ;
		//get file 
		$file = $this->m_file->get_by(array(m_file::COLUMN_LINK_ID => $link_id), TRUE);

		//if is not file die
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
		{
			$this->data['c_data']['message'] = 'Your link is invalid !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}


		//check access file
		if(!$this->m_access_file->has_access($file->{m_file::COLUMN_ID},$this->session->userdata('user_id')) )
		{
			$this->data['c_data']['message'] = 'You dont have access to see this file !!!' ;
			$this->data['content_view'] = 'empty' ;
			$this->load->view('layouts/public_layout' , $this->data);
			return ;
		}

		if(is_numeric($zip_index))
		{

			$zip = new My_zip();
			if ($zip->open('../files/' . $file->{m_file::COLUMN_HASH_NAME}) !== TRUE) 
				die('failed');// be jaye die bas ye fekre dige kard
			$stat = $zip->statIndex( $zip_index );
			$zipname =  'zip://' . $zip->filename . '#' . $zip->getNameIndex($zip_index) ;
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mim_type = finfo_file($finfo,$zipname );
			// var_dump($stat) ;
			header('Content-Type: ' . $mim_type );
			header('Content-disposition: attachment; filename='. $zip->getNameIndex($zip_index));
			header('Content-Length: ' . $stat['size']);
			readfile($zipname);			
		}
		else
		{
			$filename = '../files/' . $file->{m_file::COLUMN_HASH_NAME} ;
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mim_type = finfo_file($finfo,$filename );
			header('Content-Type: ' . $mim_type);
			header('Content-disposition: attachment; filename='. $file->{m_file::COLUMN_NAME});
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
		}
	}

}

?>
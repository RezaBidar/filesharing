<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * All panel controller define in this class
 *
 * Notice : in this system parent_id and folder_id are same because only folder can be parent ;
 * Notice : in this system user_id and repository_id are same because each user has on repository;
 * @package FileSharing
 */
class Dashboard extends Panel_Controller{

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

		
		//its for security
		$user = $this->m_user->get($this->session->userdata('user_id')) ;
		if($user == NULL) 
			die('You Are Not Valid') ;
		$this->data['user_email'] = $user->{m_user::COLUMN_EMAIL};
		$this->data['friend_repository'] = $this->m_access_user->get_repository_count($user->{m_user::COLUMN_ID}) ;
	}

	/**
	 * index controller load default in class
	 * 
	 */
	public function index()
	{
		redirect('panel/dashboard/repository_content');
	}

	/**
	 * show repository content
	 *
	 * it contain upload form and new folder form below of repository tree
	 * you can delete resource by click on (x) at right side of each resource
	 * you can see who can access to this repository in left side panel and if you are owner you can add or remove them
	 * 
	 * @param  string $user_id   user_id is equal repository_id in our system
	 * @param  string $parent_id parent_id means folder id
	 */
	public function repository_content($user_id = '', $parent_id = '')
	{

		$user_id = ($user_id == '') ? $this->session->userdata('user_id') : $user_id ;

		//check access
		if(!$this->m_access_user->has_access($user_id,$this->session->userdata('user_id') , m_access_user::LEVEL_READ) )
			die('you dont have permission to see this page') ;

		$file_list = $this->m_file->get_repository_table($user_id, $parent_id) ;
		$friend_list = $this->m_access_user->get_friend_list($user_id);
		$this->data['c_data']['parent_breadcrumb'] = $this->m_file->get_parent_breadcrumb($user_id , $parent_id) ;
		$this->data['c_data']['file_list'] = $file_list ;
		$this->data['c_data']['parent_id'] = $parent_id ;
		$this->data['c_data']['user_id'] = $user_id ;
		$this->data['c_data']['write_access'] = $this->m_access_user->has_access($user_id,$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) ;
		$this->data['c_data']['repository_owner'] = $this->m_user->get_email_by_id($user_id) ;
		$this->data['c_data']['usage_percent_line'] = $this->m_user->get_space_usage_line($user_id);
		$this->data['left_panel'] = $this->m_access_user->get_friend_table($user_id) ;
		$this->data['content_view'] = 'repository_content' ;
		$this->load->view('layouts/panel_layout' , $this->data);

	}

	/**
	 * create new folder and redirect to repository_content
	 * @param  string $user_id   
	 * @param  string $parent_id 
	*/
	public function new_folder($user_id = '', $parent_id = '')
	{
		$user_id = ($user_id === '') ? $this->session->userdata('user_id') : $user_id ;
		
		//check access
		if(!$this->m_access_user->has_access($user_id,$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this word') ;


		//set form validation
		$rules = array(
			'folder_name' => array( 'field' => 'folder_name' , 'label' => $this->lang->line('app_form_newfolder_label') , 'rules' => 'required|trim' ),
			);
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			$data = array(
					m_file::COLUMN_TYPE => m_file::TYPE_FOLDER ,
					m_file::COLUMN_PARENT_ID => ($parent_id == '') ? NULL : $parent_id ,
					m_file::COLUMN_USER_ID => $user_id ,
					m_file::COLUMN_NAME => $this->input->post('folder_name') ,

				);
			$over_write_obj = $this->m_file->is_overwrite($user_id,$this->input->post('folder_name'),$parent_id) ;
			if($over_write_obj != NULL)
			{
				$this->session->set_flashdata('error', 'This folder is already exists');
			}
			else
			{
				$this->m_file->save($data) ;
			}

		}
		else
		{
			$this->session->set_flashdata('error', validation_errors('<span>','</span><br/>'));
		}
		redirect('panel/dashboard/repository_content/' . $user_id . '/' . $parent_id);


	}

	/**
	 * upload file
	 * upload file and save its information in file table
	 * Notice : user_id is repository_id
	 * Notice : parent_id is folder_id
	 * @param  string $user_id  // wich repository file upload to it 
	 * @param  string $parent_id // what is parent folder  
	 */
	public function upload_file($user_id = '', $parent_id = '')
	{
		$user_id = ($user_id === '') ? $this->session->userdata('user_id') : $user_id ;
		
		//check access
		if(!$this->m_access_user->has_access($user_id,$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this word') ;


		$freespace = ($this->m_user->get_space($user_id) * 1024) - $this->m_file->get_usage_space($user_id);

		//it set in config/my_app_config.php file
		$config = $this->config->item('my_app_upload_config') ;
		//max size is min of cofig['max_size'] and freespace
		$config['max_size'] = ($config['max_size'] > $freespace) ? $freespace : $config['max_size'] ;

		$this->load->library('upload' , $config);

		if ( ! $this->upload->do_upload('user_file'))
		{
			//set flash
			$this->session->set_flashdata('error', $this->upload->display_errors('<span>','</span><br/>'));
		}
		else
		{

			//get file and save in database 
			
			//get uploaded file information
			$upload_data = $this->upload->data();

			//store in database 
			$data = array(
					m_file::COLUMN_TYPE => m_file::TYPE_FILE ,
					m_file::COLUMN_PARENT_ID => ($parent_id == '') ? NULL : $parent_id ,
					m_file::COLUMN_USER_ID => $user_id ,
					m_file::COLUMN_NAME => $upload_data['orig_name'] ,
					m_file::COLUMN_FILE_SIZE => $upload_data['file_size'] ,
					m_file::COLUMN_HASH_NAME => $upload_data['file_name'] ,
					m_file::COLUMN_FILE_EXT => $upload_data['file_ext'] ,
					m_file::COLUMN_FILE_TYPE => $upload_data['file_type'] ,
					m_file::COLUMN_IS_IMAGE => $upload_data['is_image'] ,
					m_file::COLUMN_IMAGE_TYPE => $upload_data['image_type'] ,
					m_file::COLUMN_IMAGE_WIDTH => $upload_data['image_width'] ,
					m_file::COLUMN_IMAGE_HEIGHT => $upload_data['image_height'] ,

				);
			$over_write_obj = $this->m_file->is_overwrite($user_id,$upload_data['orig_name'],$parent_id) ;
			if($over_write_obj != NULL)
			{
				$this->m_file->delete($over_write_obj->{m_file::COLUMN_ID});
			}

			$this->m_file->save($data) ;
		}
		redirect('panel/dashboard/repository_content/' . $user_id . '/' . $parent_id);

	}

	/**
	 * add users to access_user table
	 * in this controller add user to access to your repository
	 * 
	 */
	public function add_user()
	{
		$user_id = $this->session->userdata('user_id') ;
		
		$rules = array(
			'email' => array( 'field' => 'email' , 'label' => $this->lang->line('app_form_user_email') , 'rules' => 'required|trim|valid_email' ) ,
			'level' => array( 'field' => 'level' , 'label' => $this->lang->line('app_form_user_permision_select') , 'rules' => 'required|trim' ) ,
			);
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			$friend_user_id = $this->m_user->get_id_by_email($this->input->post('email')) ;
			if($friend_user_id == NULL)
			{
				/// set flash and return
				$this->session->set_flashdata('error' , $this->input->post('email') . ' Not registered yet !!!');
			}
			else if($user_id == $friend_user_id)
			{
				$this->session->set_flashdata('error' , $this->input->post('email') . ' is your email address !!!');	
			}
			else if($this->m_access_user->are_friend($user_id, $friend_user_id))
			{
				$this->session->set_flashdata('error' , $this->input->post('email') . ' is already in your friend list !!!');		
			}
			else
			{
				
				$this->m_access_user->add_friend($user_id, $friend_user_id , $this->input->post('level'));
				$this->session->set_flashdata('success' , $this->input->post('email') . ' added successfully .');

				redirect('panel/dashboard/repository_content');
			}

		}
		else
		{
			$this->session->set_flashdata('error' , validation_errors('<span>','</span><br/>'));
		}
		$this->data['left_panel'] = $this->m_access_user->get_friend_table($user_id) ;
		$this->data['content_view'] = 'add_user_to_repo' ;
		$this->load->view('layouts/panel_layout' , $this->data);
		

	}

	/**
	 * remove user from access_user table
	 * 
	 * @param  int $friend_id 
	 */
	public function remove_user($friend_id = '')
	{
		if($friend_id == '')
			die('your friend id is required');
		$user_id = $this->session->userdata('user_id');
		$this->m_access_user->remove_friend($user_id,$friend_id);
		redirect('panel/dashboard/repository_content');
	}

	/**
	 * load a list of repositories that you(logged in user) can access them
	 * 
	 */
	public function repository_list()
	{
		$user_id = $this->session->userdata('user_id');
		$this->data['c_data']['list'] = $this->m_access_user->get_repository_table($user_id);
		$this->data['content_view'] = 'repository_list' ;
		$this->load->view('layouts/panel_layout' , $this->data);

	}

	/**
	 * show a preview of file and download link
	 * if file is image show a tumbnail of image
	 * if file is zip show inside of file as tree and if you click on each of file you can download it
	 * if file iz text show a brief of file content - 500 character limit
	 * if logged user has write access then can generate link for this file and make link private or public
	 * 
	 * @param  string $file_id [description]
	 */
	public function file_view($file_id = '')
	{	
		if($file_id == '')
			die('File id is required') ;
		//get file 
		$file = $this->m_file->get($file_id, TRUE);
		//if is not file die
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
			die('File id is incorrect');
		
		//check access
		if(!$this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ) )
			die('you dont have permission see this page') ;

		$file_info = array(
			'id' => $file->{m_file::COLUMN_ID} ,
			'name' => $file->{m_file::COLUMN_NAME} ,
			'size' => $file->{m_file::COLUMN_FILE_SIZE} ,
			'is_image' => FALSE ,
			'is_zip' => FALSE ,
			'is_text' => FALSE ,
			'link' => $file->{m_file::COLUMN_LINK_ID} ,
			'link_mode' => $file->{m_file::COLUMN_LINK_MODE} ,
		);	
		
		if($file->{m_file::COLUMN_IS_IMAGE} == TRUE)
		{
			$file_info['is_image'] = TRUE ;
			$file_info['height'] = $file->{m_file::COLUMN_IMAGE_HEIGHT} ;
			$file_info['width'] = $file->{m_file::COLUMN_IMAGE_WIDTH} ;
			$file_info['thumbnail_link'] = site_url('panel/dashboard/image_thumbnail/' . $file_id) ;
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
				$this->data['c_data']['zip_tree'] = $this->m_file->get_zip_table($zip->getTree() , $file_id) ;
		}
		else if($file->{m_file::COLUMN_FILE_EXT} == '.txt') //if is zip show zip info
		{
			//check hass access to edit text
			if($this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE_EDIT) )
			{
				$this->data['c_data']['edit_text_link'] = '<a href="' . site_url('panel/dashboard/edit_text/'.$file_id) . '">CILICK HERE TO EDIT THIS FILE</a>';
			}
			$file_info['is_text'] = TRUE ;
			$this->data['c_data']['text'] = file_get_contents('../files/' . $file->{m_file::COLUMN_HASH_NAME} , NULL , NULL , 0 , 500) . '...';
		}

		$this->data['c_data']['access_file_user_list'] = $this->m_access_file->get_user_table($file_id) ;
		$this->data['left_panel'] = $this->m_access_user->get_friend_table($file->{m_file::COLUMN_USER_ID}) ;
		$this->data['c_data']['file_info'] = $file_info ;
		$this->data['c_data']['write_access'] = $this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) ;
		$this->data['content_view'] = 'file_view' ;
		$this->load->view('layouts/panel_layout' , $this->data);
	}

	/**
	 * show image tumbnail
	 * @param  string $image_id //file id 
	 */
	public function image_thumbnail($image_id = '')
	{		
		
		if($image_id == '')
			die('File id is required') ;
		//get file 
		$image = $this->m_file->get($image_id, TRUE);
		if($image == NULL OR $image->{m_file::COLUMN_IS_IMAGE} == FALSE)
			die('Image id is invalid') ;

		//check access
		if(!$this->m_access_user->has_access($image->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ) )
			die('you dont have permission see this page') ;

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
	 * Edit text file form
	 * 
	 * @param  string $text_id File id
	 */
	public function edit_text($text_id = '')
	{
		if($text_id == '')
			die('File id is required') ;
		//get file 
		$text = $this->m_file->get($text_id, TRUE);
		if($text == NULL OR $text->{m_file::COLUMN_FILE_EXT} != '.txt')
			die('Text id is invalid') ;

		//check access
		if(!$this->m_access_user->has_access($text->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE_EDIT) )
			die('you dont have permission see this page') ;
		
		$config = $this->config->item('my_app_upload_config') ;
		$file_path = $config['upload_path'] . $text->{m_file::COLUMN_HASH_NAME} ;
		$rules = array('text' => array( 'field' => 'text' , 'label' => 'Text' , 'rules' => 'required' ) );
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			if(file_put_contents($file_path,$this->input->post('text'))) 
				$this->session->set_flashdata('success', 'Text file has been updated');
		}
		else
		{
			$this->session->set_flashdata('error', validation_errors('<span>','</span>'));
		}


		$file = file_get_contents($file_path);
		$this->data['c_data']['file_name'] = $text->{m_file::COLUMN_NAME} ;
		$this->data['c_data']['file_content'] = $file ;
		//load view
		$this->data['content_view'] = 'edit_text' ;
		$this->load->view('layouts/panel_layout', $this->data);

	}

	/**
	 * download file 
	 * if zip_index pass to this controller then it download file inside zip file at this index
	 * @param  string $file_id   
	 * @param  string $zip_index zip file index          
	 */
	public function download_file($file_id = '', $zip_index = '')
	{
		//check access

		if($file_id == '')
			die('File id is required') ;
		//get file 
		$file = $this->m_file->get($file_id, TRUE);
		//if is not file die
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
			die('File id is incorrect');

		//check access
		if(!$this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ) )
			die('you dont have permission see this page') ;

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
			header('Content-Type: ' . mime_content_type($filename));
			header('Content-disposition: attachment; filename='. $file->{m_file::COLUMN_NAME});
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
		}
	}


	/**
	 * generate unique link id
	 * if link_id of this file be null then generate new unique string and update file row
	 * otherwise only change link mode
	 * @param  int $file_id   
	 * @param  int $link_mode // private or public 
	 */
	public function link_generator($file_id = '',$link_mode = '')
	{
		
		if($file_id == '')
			die('File id is required') ;
		$file = $this->m_file->get($file_id , TRUE) ;
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
			die('File id is invalid');


		//check access
		if(!$this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this work') ;

		//generate uniq id
		$this->load->helper('string');
		$link_id = random_string('unique');
		if($file->{m_file::COLUMN_LINK_ID} == NULL)
			$data[m_file::COLUMN_LINK_ID] = $link_id ;

		//update file row
		$link_mode = ($link_mode == m_file::LINK_MODE_PRIVATE) ? $link_mode : m_file::LINK_MODE_PUPBLIC ;
		$data[m_file::COLUMN_LINK_MODE] =  $link_mode ;

		$this->m_file->save($data,$file_id);
		redirect('panel/dashboard/file_view/'.$file_id);

	}

	/**
	 * it clear link id from file row
	 * @param  int $file_id 
	 */
	public function clear_link($file_id = '')
	{
		
		if($file_id == '')
			die('File id is required') ;
		$file = $this->m_file->get($file_id , TRUE) ;
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
			die('File id is invalid');

		//check access
		if(!$this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this work') ;

		$data = array(
			m_file::COLUMN_LINK_ID => NULL ,
			m_file::COLUMN_LINK_MODE => 0 ,
			);

		$this->m_file->save($data,$file_id);
		redirect('panel/dashboard/file_view/'.$file_id);

	}	

	/**
	 * check this name is created before in this repository and folder or not
	 * this controller use for ajax
	 * @return string 
	 */
	public function is_override()
	{
		$user_id = $this->input->get('user_id') ;
		$parent_id = $this->input->get('parent_id') ;
		$name = $this->input->get('name') ;

		if(!is_numeric($user_id) OR $name == '')
			die() ;

		if($this->m_file->is_overwrite($user_id,$name,$parent_id) != NULL) 
			echo 'YES' ;
		else
			echo 'NO' ;
	}

	/**
	 * get user id from post and add it to access_file table for this file
	 * @param string $file_id 
	 */
	public function add_access_link_user($file_id = '')
	{
		if($file_id == '')
			die('File id is required') ;
		$file = $this->m_file->get($file_id , TRUE) ;
		if($file == NULL OR $file->{m_file::COLUMN_TYPE} != m_file::TYPE_FILE)
			die('File id is invalid');


		//check access
		if(!$this->m_access_user->has_access($file->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this work') ;


		$rules = array(
			'email' => array( 'field' => 'email' , 'label' => $this->lang->line('app_form_user_email') , 'rules' => 'required|trim|valid_email' ) 
			);
		$this->form_validation->set_rules($rules) ;
		//check validation
		if($this->form_validation->run() == TRUE)
		{
			$user_id = $this->m_user->get_id_by_email($this->input->post('email')) ;
			if($user_id == NULL)
			{
				// die('user not exists');
				$this->session->set_flashdata('error', $this->input->post('email') . ' does not exists in system !!!');
			}
			else{
				$this->m_access_file->add_user($file_id, $user_id);
				$this->session->set_flashdata('success', $this->input->post('email') . ' added .');		
			}

		}
		else
		{
				$this->session->set_flashdata('error', $this->input->post('email') . ' is not valid email !!!');
		}
		redirect('panel/dashboard/file_view/'. $file_id);
	}

	/**
	 * remove resource controller
	 * 
	 * @param  string $file_id 
	 */
	public function remove_file($file_id = '')
	{

		$resource = $this->m_file->get($file_id , TRUE);
		if($resource == NULL)
			die('file id is invalid');

		//check access
		if(!$this->m_access_user->has_access($resource->{m_file::COLUMN_USER_ID},$this->session->userdata('user_id') , m_access_user::LEVEL_READ_WRITE) )
			die('you dont have permission to do this work') ;

		$user_id = $resource->{m_file::COLUMN_USER_ID};
		$parent_id = $resource->{m_file::COLUMN_PARENT_ID};
		$this->db->trans_start();
		$this->m_file->delete($file_id);
		$this->db->trans_complete();

		$this->session->set_flashdata('success', ' your resource removed successfully .');		
		redirect('panel/dashboard/repository_content/' . $user_id . '/' . $parent_id) ;

	}

	/**
	 * upgrade user
	 * it is sample form for increase repository space
	 * @param  string $plus_space // how much space in mb should add to repository 
	 */
	public function upgrade_panel($plus_space = '')
	{
		$user_id = $this->session->userdata('user_id');
		$user = $this->m_user->get($user_id,TRUE);

		if(is_numeric($plus_space))
		{
			$data= array(m_user::COLUMN_SPACE => ($user->{m_user::COLUMN_SPACE} + $plus_space)) ;
			$this->m_user->save($data , $user_id) ;
			$this->session->set_flashdata('success', $plus_space . ' MB added to your space successfully');
			redirect('panel/dashboard/repository_content');
		}

		$this->data['c_data']['panel_info'] = array(
			'id' => $user->{m_user::COLUMN_ID},
			'email' => $user->{m_user::COLUMN_EMAIL},
			'space' => $user->{m_user::COLUMN_SPACE},
			);

		$this->data['content_view'] = 'upgrade_panel' ;
		$this->load->view('layouts/panel_layout' , $this->data);		
	}
}


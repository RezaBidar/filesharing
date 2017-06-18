<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$config['my_app_empty'] = '' ;
$config['my_app_upload_config'] = array(
		'upload_path' => '../files/' ,
		'allowed_types' => 'gif|jpg|png|jpeg|zip|rar|txt|mp3' ,
		'max_size' => 10000 ,
		'encrypt_name' => TRUE , // must be true 
	);
$config['my_app_image_lib_config'] = array(
	'image_library' => 'gd2' ,
	'create_thumb' => TRUE ,
	'maintain_ratio' => TRUE ,
	'width' => 200 ,
	'height' => 200 ,
	'dynamic_output' => TRUE ,
	);

?>
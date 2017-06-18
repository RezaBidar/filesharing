<?php
defined('BASEPATH') OR exit('No direct script access allowed');
echo '<h2>'. $repository_owner .' Repository</h2>' ;
echo '<hr/>' ;
echo $usage_percent_line ;
echo '<hr/>' ;
echo $parent_breadcrumb ;
echo $file_list ;
echo '<hr/>' ;

if($write_access):

echo '<div class="col-md-6">';
echo btform::form_open_multipart('panel/dashboard/upload_file/' . $user_id . '/' . $parent_id) ;
echo btform::form_upload($this->lang->line('app_form_upload_file_label') , array('name' => 'user_file'));
echo btform::form_submit(array("name"=>"submit_upload" , "class"=>"btn btn-primary" ) , $this->lang->line('app_form_upload_btn'));
echo btform::form_close();
echo '</div>' ;

echo '<div class="col-md-6">';
echo btform::form_open('panel/dashboard/new_folder/' . $user_id . '/' . $parent_id) ;
echo btform::form_input($this->lang->line('app_form_newfolder_label') , array('name' => 'folder_name'));
echo btform::form_submit(array("name"=>"submit_newfolder" , "class"=>"btn btn-primary" ) , $this->lang->line('app_form_newfolder_btn'));
echo btform::form_close();
echo '</div>' ;
echo btform::form_hidden('parent_id' , $parent_id);
echo btform::form_hidden('user_id' , $user_id);

endif ;
 ?>

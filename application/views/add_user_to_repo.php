<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

echo '<h3>Add User To Your Repository</h3>';
echo btform::form_open();
echo btform::form_input($this->lang->line('app_form_user_email') , array('name' => 'email'));
$select_arr = $this->m_access_user->get_levels();
echo btform::form_select($this->lang->line('app_form_user_permision_select') , 'level' , $select_arr , '' , ' class="form-control" ') ;
echo btform::form_submit(array("name"=>"submit" , "class"=>"btn btn-primary" ) , $this->lang->line('app_form_add_user_btn'));
echo btform::form_close();

?>
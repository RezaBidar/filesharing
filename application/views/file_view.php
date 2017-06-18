<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Download File</h2>
<div class="row">
<div class="col-md-8">
<?php 
echo '<div class="panel panel-info">';
echo '<div class="panel-heading"> '. $file_info['name'] .' </div>';
echo '<div class="panel-body">';
if($file_info['is_image'])
{
	echo '<img src="'.$file_info['thumbnail_link'].'" />' ;
}
else if($file_info['is_zip'])
{	
	echo '<p class="alert alert-warning">Click on each file to download it</p>' ;
	echo $zip_tree ;
}else if($file_info['is_text'])
{
	echo 'Text content : ' ;
	if(isset($edit_text_link)) echo $edit_text_link;
	echo '<div class="well">' . $text . '</div>' ;
}
echo '<hr />' ;
echo '<p><b>File Name : </b>' . $file_info['name'] . '</p>' ;
echo '<p><b>File Size : </b>' . digital_size_show($file_info['size'] , 'kb') . '</p>' ;
if($file_info['is_image'])
{
	echo '<p><b>Image height : </b>' . $file_info['height'] . 'px </p>' ;
	echo '<p><b>Image width : </b>' . $file_info['width'] .  'px </p>' ;	
}
echo '<p><b><a href="'. site_url('panel/dashboard/download_file/' . $file_info['id']).'" class="btn btn-primary">Click Here to Download This File</a></b></p>' ;
echo '</div>';
echo '</div>';

// var_dump($file_info);
 ?>
 </div>
 <?php if($write_access): ?>
 <div class="col-md-4">
 <div class="panel panel-success">
 <div class="panel-heading">Link Info</div>
 <div class="panel-body">
 	<?php 
 	if($file_info['link'] == NULL) 
 		echo '<a href="'. site_url('panel/dashboard/link_generator/' . $file_info['id']) 
 		.'" style="width:100%" class="btn btn-primary">Get Link for this file</a>' ;
	else
	{
		echo '<label>Link Address <a href="'. site_url('site/file_view/'.$file_info['link']) .'" target="_blank"><i class="fa fa-external-link"></i></a></label>' ;
		echo '<p><input type="text" class="form-control" value="'. site_url('site/file_view/'.$file_info['link']) .'" readonly/></p>';
 		echo '<p><a href="'. site_url('panel/dashboard/clear_link/' . $file_info['id']) 
 			.'" style="width:100%" class="btn btn-danger">Clear Link</a></p>' ;
	 	if($file_info['link_mode'] == m_file::LINK_MODE_PUPBLIC)
	 		echo '<p><a href="'. site_url('panel/dashboard/link_generator/' . $file_info['id'] . '/' 
	 			. m_file::LINK_MODE_PRIVATE) .'" style="width:100%" class="btn btn-warning">MAKE LINK PRIVATE</a></p>' ;
	 	else // link_mode == m_file::LINK_MODE_PRIVATE
	 		echo '<p><a href="'. site_url('panel/dashboard/link_generator/' . $file_info['id'] . '/' 
	 			. m_file::LINK_MODE_PUPBLIC) .'" style="width:100%" class="btn btn-info">MAKE LINK PUBLIC</a></p>' ;
	}
	if($file_info['link_mode'] == m_file::LINK_MODE_PRIVATE)
	{
		echo '<hr/>';
		echo btform::form_open('panel/dashboard/add_access_link_user/'. $file_info['id']);
		echo btform::form_input('Add user to access this file' , array('name' => 'email' , 'placeholder' => 'Email')) ;
		echo btform::form_submit(array('name' => 'submit' , 'style' => 'width:100%', 'class'=>'btn btn-success') , 'ADD');
		echo btform::form_close();
 	}
 	?>
 </div>
 </div>

 <?php 
	if($file_info['link_mode'] == m_file::LINK_MODE_PRIVATE)
	{
		echo $access_file_user_list ;
 	}
 ?>
 </div>
<?php endif; ?>

 </div>	

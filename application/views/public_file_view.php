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
	echo 'Text content :' ;
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
echo '<p><b><a href="'. site_url('site/download_file/' . $file_info['link']).'" class="btn btn-primary">Click Here to Download This File</a></b></p>' ;
echo '</div>';
echo '</div>';


 ?>
 </div>
 

 </div>	

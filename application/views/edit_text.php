<h2>Edit Text - <small><?php echo $file_name ?></small></h2>
<?php  
echo btform::form_open();
echo btform::form_textarea('Text',array('name'=>'text'),$file_content) ;
echo btform::form_submit('submit', 'Save');
echo btform::form_close();
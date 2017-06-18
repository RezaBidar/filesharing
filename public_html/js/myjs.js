
$(document).ready(function(){

	var site_url = $("input[name=site_url").val() ;
	
	$('#jstree_div').on('changed.jstree', function (e, data) {
	    var link = data.node.a_attr.href;
	    if(link != "#")
	    	window.location.href = link ;
	  }).jstree();

	var file_overwrite = false ;
	$("input[name=submit_upload]").click(function(e){
		if(file_overwrite){
			bootbox.alert('file uploading ...');
			file_overwrite = false ;
		} 
		else
		{
			e.preventDefault() ;
			$(this).attr("disabled", true);
			var user_val = $("input[name=user_id]").val().trim() ; 
			var parent_val = $("input[name=parent_id]").val().trim() ; 
			var name_val = $("input[name=user_file]").val().split('\\').pop() ; 
			
			if(!name_val.trim()) {
				bootbox.alert('Select a file please ...') ;
				$(this).attr("disabled", false);
				return ;
			}


			$.ajax({
					url : site_url + "panel/dashboard/is_override",
					data : { user_id: user_val, parent_id: parent_val, name: name_val } ,
					success : function(result){
						if(result == "YES"){
							bootbox.confirm('The file “'+ result +'” already exists in this directory .'
												+'Do you want to continue your upload and overwrite it?' , function(answer){
													if(answer == true) {
														file_overwrite = true ;
														$("input[name=submit_upload]").attr("disabled", false);	
														$("input[name=submit_upload]").trigger('click');
													}else{
														$("input[name=submit_upload]").attr("disabled", false);								
													}

												});
						}else{
							file_overwrite = true ;
							$("input[name=submit_upload]").attr("disabled", false);
							$("input[name=submit_upload]").trigger('click');
						}
					}
			}) ;
		}
	});	

	var folder_overwrite = false ;
	$("input[name=submit_newfolder]").click(function(e){
		if(folder_overwrite){
			bootbox.alert('creating new folder ...');
			folder_overwrite = false ;
		} 
		else
		{
			$(this).attr("disabled", true);
			e.preventDefault() ;
			var user_val = $("input[name=user_id]").val().trim() ; 
			var parent_val = $("input[name=parent_id]").val().trim() ; 
			var name_val = $("input[name=folder_name]").val().trim() ; 

			if(!name_val.trim()) {
				bootbox.alert('Write folder name please ...') ;
				$(this).attr("disabled", false);
				return ;
			}
			
			$.ajax({
					url : site_url + "panel/dashboard/is_override",
					data : { user_id: user_val, parent_id: parent_val, name: name_val } ,
					success : function(result){
						if(result == "YES"){
							bootbox.alert('The folder “'+ name_val +'” already exists in this directory . You can not create this folder .') ;
							$('input[name=submit_newfolder]').attr("disabled", false);
						}else{
							folder_overwrite = true ;
							$('input[name=submit_newfolder]').attr("disabled", false);
							$("input[name=submit_newfolder]").trigger('click');
						}
					}
			}) ;
		}
	});
	
});
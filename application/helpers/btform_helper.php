<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * bootstrap form helper
 * return form helper in bootstrap format
 */
class btform {
	
	public static function form_open($action = '' , $attribute = '' ,$hidden = array()){
		return form_open($action , $attribute ,$hidden);
	}
	
	public static function form_open_multipart($action = '', $attributes = array(), $hidden = array()){
	    return form_open_multipart($action, $attributes, $hidden) ;
	}
	
	public static function form_input($label = '', $data = '', $value = '', $extra = '' , $help = ''){
		//add form-control class to input
		if(isset($data['class'])) $data['class'] .= ' form-control ' ;	
		else $data['class'] = 'form-control' ;

		$answer  = '<div class="form-group">' ;
		if($label != ''){
			$answer .= form_label($label, $data['name']);
		}
		$answer .= form_input($data, $value, $extra);
		if($help != '')
			$answer .= '<span class="help-block">' . $help . '</span>' ;
		$answer .= '</div>';
		return $answer ;
	}

	
	public static function form_password($label = '', $data = '', $value = '', $extra = '' , $help = ''){
		//add form-control class to input
		if(isset($data['class'])) $data['class'] .= ' form-control ' ;	
		else $data['class'] = 'form-control' ;

		$answer  = '<div class="form-group">' ;
		if($label != ''){
		    $answer .= form_label($label, $data['name']);
		}
		$answer .= form_password($data, $value, $extra);
		if($help != '')
		    $answer .= '<span class="help-block">' . $help . '</span>' ;
		$answer .= '</div>';
		return $answer ;
	}
	
	public static function form_upload($label = '', $data = '', $value = '', $extra = '' , $help = ''){
		$answer  = '<div class="form-group">' ;
		if($label != ''){
			$answer .= form_label($label, $data['name']);
		}
		$answer .= form_upload($data, $value, $extra);
		if($help != '')
			$answer .= '<span class="help-block">' . $help . '</span>' ;
		$answer .= '</div>';
		return $answer ;
		
	}
	
	public static function form_textarea($label = '', $data = '', $value = '', $extra = ''){
		//add form-control class to input
		if(isset($data['class'])) $data['class'] .= ' form-control ' ;	
		else $data['class'] = 'form-control' ;

		$answer  = '<div class="form-group">' ;
		if($label != ''){
			$answer .= form_label($label, $data['name']);
		}
		$answer .= form_textarea($data, $value, $extra);
		$answer .= '</div>';
		return $answer ;
	}
	
	public static function form_checkbox($label = '', $data = '', $value='', $checked = FALSE, $extra = '' ){
		
		//$disabled = (is_array($data) && isset($data["disabled"]) && $data["disabled"])? '' : 'disabled' ;
		$answer  = '<div class="checkbox">' ;
		if($label != ''){
			$answer .= '<label>';
			$answer .= $label ;
		}
		$answer .= form_checkbox($data, $value, $checked, $extra);
		if($label != ''){
			
			$answer .= '</label>';
		}
		$answer .= '</div>';
		return $answer ;
	}
	
	public static function form_radio($label = '', $radio_arr = array() , $data = '', $checked = '', $extra = '' ){
		
		//$disabled = (is_array($data) && isset($data["disabled"]) && $data["disabled"])? '' : 'disabled' ;
		$answer  = '<div class="form-group">' ;
		if($label != ''){
			
			$answer .= form_label($label, $data['name']) . "<br>";
		}
		
		foreach ($radio_arr as $r_label => $r_value){
			$answer .= '<label class="radio-inline" >';
			$answer .= $r_label ;
			$selected = ($checked == $r_value) ? TRUE : FALSE ;
			$answer .= form_radio($data, $r_value, $selected, $extra);
			$answer .= '</label><br/>';
		}
		$answer .= '</div>';
		return $answer ;
	}
	
	public static function form_close($extra = ''){
		return form_close($extra);
	}

	public static function form_submit($data='', $value='', $extra=''){
		return form_submit($data,$value,$extra);
	}
	
	public static function form_hidden($name , $value = '' , $recursing = NULL){
		return form_hidden($name,$value,$recursing);
	}
	
	public static function form_button($data,$content = "",$extra = ""){
		return '<div class="form-group">' . form_button($data,$content,$extra) . '</div>';
	}
	
	public static function form_select($label , $name , $options , $selected = '' , $extra = ''){

		$answer  = '<div class="form-group">' ;
		if($label != ''){
			$answer .= form_label($label, $name);
		}
		if($selected == '') {
			//basi ranj bordam dar inja :))))
			//agar select nashode bashad yek option khali be ebtedaye optionha ezafe minomayad
			$regex = "/(<select[^>]*>)\s*((?:<option[^>]*>(?:.*?)<\/option>\s*)*)\s*(<\/select>)/i" ;	
			$drop_down = form_dropdown($name,$options,$selected,$extra) ;
			$answer .= preg_replace($regex, "$1<option selected disabled hidden value=''></option>$2$3", $drop_down) ;
		}else{
			$answer .= form_dropdown($name,$options,$selected,$extra) ;
		}
		$answer .= '</div>';
		return $answer ;
	}
	
	
}
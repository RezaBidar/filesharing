<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

function digital_size_show($size , $mode = 'b')
{
	switch ($mode) {
		case 'b':
			return format_size_unit($size) ;
		break;
		case 'kb':
			return format_size_unit($size * 1024) ;
		break;
		case 'mb':
			return format_size_unit($size * 1024 * 1024) ;
		break;
	}
}

function format_size_unit($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}
	
	return $bytes;
}


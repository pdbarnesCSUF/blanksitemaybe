<?php
/*
 * @file image_support.php
 * @desc gd_info return showing what image types are supported
 */
 
$responsearr['action'] = 00; //unknown error
$responsearr['data'] = false;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');
	
	$gd = gd_info();
	$responsearr['data']['gif'] =	$gd['GIF Read Support'];
	$responsearr['data']['jpeg'] =	$gd['JPEG Support'];
	$responsearr['data']['png'] =	$gd['PNG Support'];
	$responsearr['data']['wbmp'] =	$gd['WBMP Support'];
	$responsearr['data']['xpm'] =	$gd['XPM Support'];
	$responsearr['data']['xbm'] =	$gd['XBM Support'];
	$responsearr['data']['webp'] =	$gd['WebP Support'];
	
	$responsearr['action'] = 1;
echo json_encode($responsearr);
?>


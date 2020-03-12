<?php
/*
 * @file template_ajax.php
 * @desc template for ajax pages
 */
 
$responsearr['action'] = 00; //unknown error
$responsearr['data'] = false;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	id_verbose('PAGE:ajax/template_ajax.php');
	id_init();
	//do something
	
	$responsearr['data'] = true;
	$responsearr['action'] = 1; //no problem
	//-----end-----
	$responsearr['message'] = $id_SESSION['message'];
	$responsearr['debug'] = $id_SESSION['debug'];
}
else
{
	$responsearr['action'] = 02; //include error
	$responsearr['message'][] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}

echo json_encode($responsearr);
?>

<?php
/**
 *  @file id_api.php
 *  @brief ID api - library
 */
$idapi['status'] = array(
	'loggedin' =>		false,
	'userid' =>			0,
	'username' =>		'Not Logged In',
	'profilename' =>	'',
	'preferredname' =>	'',
	'unread_messages' =>0
);
$idapi['message'] = array();
$idapi['debug'] = array();
if (include_once('id_api_settings.php'))
{
	if (include_once($idapisettings['id_path']))
	{
		//======= init status
		//-----------------------NOT READY YET
		//$idapi['status'] = ;
		//=======
		$idapi['message'] = $id_SESSION['message'];
		$idapi['debug'] = $id_SESSION['debug'];
	}
	else
	{
		$idapi['action'] = 02; //include error
		$idapi['message'] = "Server Error";
		$idapi['debug'][] = "ID Include error";
	}
else
{
	$idapi['action'] = 02; //include error
	$idapi['message'] = "Include Error";
	$idapi['debug'][] = "id_api_settings.php include error";
}
//================================================================
function idapi_profile($uid)
{
	//
}
?>

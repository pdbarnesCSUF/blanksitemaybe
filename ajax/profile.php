<?php
/*
 * @file profile.php
 * @desc return profile info based on GET user id
 */
 
$responsearr['action'] = 00; //unknown error
///< @todo make this into a class issue #13
$responsearr['data'] = array(
	"username"		=> '',
	"datetime_created"	=> 0,
	"active"		=> false,
	"status"		=> '',
	"name"			=> '',
	"prefer_name"	=> 0,
	"public_view"	=> 0,
	"location"		=> '',
	"gender"		=> '',
	"picture"		=> false
);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	$uid = isset($_GET['uid']) ? filter_var($_GET['uid'],FILTER_VALIDATE_INT,array('options'=>array('default'=>0,'min_range'=>1))) : 0;
	if ($uid > 0)
	{
		id_init();
		$profile = id_profile_view_login($uid);
		//viewing yourself
		if ($profile['username'])
		{
			$responsearr['data'] = $profile;
			$responsearr['action'] = 1;
		}
		//not public, doesnt exist
		else
		{
			$responsearr['action'] = 06; //User does not exist or is private
		}
	}
	else
		$responsearr['action'] = 07; //bad input
	//-----end-----
	$responsearr['message'] = $id_SESSION['message'];
	$responsearr['debug'] = $id_SESSION['debug'];
}
else
{
	$responsearr['action'] = 02; //include error
	$responsearr['message'] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}

echo json_encode($responsearr);
?>

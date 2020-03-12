<?php
/*
 * @file options.php
 * @desc feed options data
 */
 
$responsearr['action'] = 00; //unknown error
$responsearr['data'] = false;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');
$OPTIONS = '../config/sitesettings.php';
if (file_exists($OPTIONS))
{
	require($OPTIONS);
	//unset unsafe vars for display
	unset($sitesettings['db_hostname']	);
	unset($sitesettings['db_database']	);
	unset($sitesettings['db_username']	);
	unset($sitesettings['db_password']	);
	unset($sitesettings['debug']		);

	$responsearr['data'] = $sitesettings;
	$responsearr['action'] = 1; //no problem
}
else
{
	$responsearr['action'] = 02; //include error
	$responsearr['message'] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}
echo json_encode($responsearr);
?>

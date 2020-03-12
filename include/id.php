<?php
/**
 *  @file id.php
 *  @brief ID login system backend. Only for present.pt23.net/isds454 and present.pt23.net/api/id
 */
//nothing in this file will READ $ID_SESSION (except id_init), only write!
//this will make functions more flexible (especially for admin functions)
//======================== VARS
//INCLUDEmessages['id'] - errors that happen just simply from including this file
//$ID_SESSION['message'] - messages from the ID system meant for user or site dev
//$ID_SESSION['debug'] - don't always exist dependant on settings/debug
//include, root, vendor, same as in include.php
$ID_ROOT = __DIR__.'/..'; ///< @todo THIS IS STUPID
$ID_INCLUDE = __DIR__;
$ID_IMAGES_PROFILES = $ID_INCLUDE.'/../data/user_images';
$IDstatus = 1;
$INCLUDEmessages['id'] = array();
//======ID SESSION arr setup
$ID_SESSION['id'] = 0;
$ID_SESSION['sessid'] = 0;
$ID_SESSION['loggedin'] = false;
$ID_SESSION['id_current_user'] = NULL;
$ID_SESSION['message'] = array();
$ID_SESSION['debug'] = array();
//======================== SETUP
/*
 * step pre-init = 0 - 100 = ready
 * 0	pre-init, nothing
 * 1	dependancies loaded
 * 2	core files loaded
 * 10	id settings loaded
 * 20
 * 30	database loaded
 * 40
 * 100	ready
 */
//=============CORE REQUIRES
//I want white page of death if fail
require_once($ID_ROOT.'/vendor/PHPMailer/src/Exception.php');
require_once($ID_ROOT.'/vendor/PHPMailer/src/PHPMailer.php');
require_once($ID_ROOT.'/vendor/PHPMailer/src/SMTP.php');
$IDstatus = 1;
require_once($ID_INCLUDE.'/common.php');
require_once($ID_INCLUDE.'/id/id_class_user.php');
require_once($ID_INCLUDE.'/id/id_account.php');
require_once($ID_INCLUDE.'/id/id_create.php');
require_once($ID_INCLUDE.'/id/id_messages.php');
require_once($ID_INCLUDE.'/id/id_profile.php');
require_once($ID_INCLUDE.'/id/id_session.php');
require_once($ID_INCLUDE.'/id/id_email.php');
$IDstatus = 2;
//=============Continueable fails
if (file_exists($ID_ROOT.'/config/sitesettings.php'))
{
	$IDstatus = 3;
	require_once ($ID_ROOT.'/config/sitesettings.php');
	$IDstatus = 4;
	if (isset($sitesettings['db_hostname']))
		$IDstatus = 10;
	else
		$INCLUDEmessages['id'][] = 'database not configured';
}
else
	$INCLUDEmessages['id'][] = 'missing sitesettings.php';

//load database
if ($IDstatus >= 10)
{
	# Type="POSTGRES"
	//KEEP SEPERATED
	try
	{
		$iddbPDO = new PDO(	'pgsql:dbname='.$sitesettings['db_database'].
								';host='.$sitesettings['db_hostname'].
								';user='.$sitesettings['db_username'].
								';password='.$sitesettings['db_password']
								);
		$IDstatus = 11;
		$iddbPDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$IDstatus = 12;
		//$iddbPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$iddbPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		//echo "PDO connection object created";
		$IDstatus = 30;
	}
	catch (Exception $e)
	{
		error_log($e->getMessage());
		$INCLUDEmessages['id'][] = "DB_Error";
	}
}

switch ($sitesettings['debug'])
{
	case 2:
		id_verbose("id verbose enabled");
	case 1:
		id_debug("id debug enabled");
		break;
}

if ($IDstatus == 30)
	$IDstatus = 100;
id_debug("IDstatus:".$IDstatus);
//=========================== END OF SETUP
//=========================== FUNCTION DEFINITION

/**
 * @brief Puts message into $ID_SESSION['message']
 *
 * @param [in] string message
 */
function id_message($msg)
{
	global $ID_SESSION;
	$ID_SESSION['message'][] = $msg;
}

/**
 * @brief Puts message into $ID_SESSION['debug'] if debug mode
 *
 * @param [in] string message
 * @param [in] boolean force regardless of settings
 */
function id_debug($msg, $forced = false)
{
	global $sitesettings, $ID_SESSION;
	if (($sitesettings['debug'] >= 1) || $forced)
		$ID_SESSION['debug'][] = $msg;
}

/**
 * @brief Puts message into $ID_SESSION['debug'] if verbose
 *
 * @param [in] string message
 */
function id_verbose($msg)
{
	global $sitesettings, $ID_SESSION;
	if ($sitesettings['debug'] >= 2)
		$ID_SESSION['debug'][] = $msg;
}


/**
 *  @brief validates id login session, sets up $ID_SESSION
 *
 *  @return true if logged in
 *
 *  @details Makes sure that a session is active and validates the login status.
 */
function id_init()
{
	global $ID_SESSION, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//get session from cookie OR GET
	$sessid = false;
	if (isset($_COOKIE['id_sessid']))
		$sessid = $_COOKIE['id_sessid'];
	else if (isset($_GET['sid']))
		$sessid = $_GET['sid'];
	//continue
	if ($sessid)
	{
		$userid = id_session_valid($sessid);
		if ($userid > 0)
		{
			id_session_resume($sessid);
			$ID_SESSION['id'] = $userid;
			$ID_SESSION['loggedin'] = true;
			$ID_SESSION['sessid'] = $sessid;
			$ID_SESSION['id_current_user'] = new ID_user($userid);
		}
		else
		{
			id_session_kill($sessid);
		}
	}
	else
	{
		id_debug("nosessid");
		id_session_logout();
	}
	id_verbose("(".__FUNCTION__."-END)");
}


?>

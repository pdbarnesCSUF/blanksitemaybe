<?php
$INCLUDE = __DIR__;
$ROOT = $INCLUDE.'/..';
$VENDOR = $ROOT.'/vendor';
$SITEstatus = 0;
$SITEmessage = array();
$SITEdebug = array();
$INCLUDEmessages['site'] = array();

//page info defaults
$pageinfo['title'] = "pagetitle";
$pageinfo['description'] = "pagedesc";
$pageinfo['type'] = "website";
$pageinfo['url'] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$pageinfo['image'] = "favicon.png";

//check if settings exists
if (file_exists($INCLUDE.'/id.php'))
{
	require_once ($INCLUDE.'/id.php');
	//id does its thing...
	if ($IDstatus >= 100)
	{
		id_init();
	}
}
else
{
	$INCLUDEmessages['site'][] = 'missing id.php';
	$IDstatus = 0;
	$INCLUDEmessages['id'] = array();
}


/*
 * step pre-init = 0 - 100 = ready
 * 0	pre-init, nothing
 * ...
 * 100	ready
 */

/*
 * step pre-init = 0 - 100 = ready
 * 0	pre-init, nothing
 * 5	site settings loading
 * 6	debug set
 * 7	tracy found
 * 8	tracy included
 * 9	tracy enabled
 * 10	site settings loaded
 */
//check if settings exists
if (file_exists($ROOT.'/config/settings.php'))
{
	$SITEstatus = 5; //site settings loading
	require_once ($ROOT.'/config/settings.php');
	if (isset($sitesettings['debug']))
	{
		$SITEstatus = 6; //debug mode set
	}
	$SITEstatus = 10; //site settings loaded
}
else
	$INCLUDEmessages['site'][] = 'missing settings.php';


if ($SITEstatus == 10)
	$SITEstatus = 100;
site_debug("SITEstatus:".$SITEstatus);

if ($SITEstatus >= 100)
{
	//setup page info defaults
	$pageinfo['title'] = $sitesettings['meta']['og:title'];
	$pageinfo['description'] = $sitesettings['meta']['og:description'];
	$pageinfo['type'] = $sitesettings['meta']['og:type'];
	$pageinfo['url'] = $sitesettings['meta']['og:url'];
	$pageinfo['image'] = $sitesettings['meta']['og:image'];
}
//=========================== END OF SETUP
//=========================== FUNCTION DEFINITION
/**
 * @brief Puts message into $SITEmessage
 * 
 * @param [in] string message
 */
function site_message($msg)
{
	global $SITEmessage;
	$SITEmessage[] = $msg;
}

/**
 * @brief Puts message into $SITEdebug if debug mode
 * 
 * @param [in] string message
 * @param [in] boolean force regardless of settings
 */
function site_debug($msg, $forced = false)
{
	global $sitesettings, $SITEdebug;
	if (($sitesettings['debug'] >= 1) || $forced)
		$SITEdebug[] = $msg;
}

/**
 * @brief Puts message into $SITEdebug if verbose
 * 
 * @param [in] string message
 */
function site_verbose($msg)
{
	global $sitesettings, $SITEdebug;
	if ($sitesettings['debug'] >= 2)
		$SITEdebug[] = $msg;
}

?>

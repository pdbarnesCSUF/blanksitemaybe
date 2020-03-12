<?php
/**
 *  @file template_admin_page.php
 *  @brief template admin_page. intended for /admin/
 *  
 */
use Tracy\Debugger;
require_once ('../vendor/tracy.php');
require_once('../include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "TEMPLATEADMIN - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "PT TEMPLATE server admin.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Template Admin- <?php echo $sitesettings['title']?></title>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong>TEMPLATE Admin Page</strong></h1>
	<div id="mainwell" class="well">
<?php if (id_account_getpermission($id_SESSION['id'],'template_admin')) { ?>
	<p>Has permission</p>
<?php } else { ?>
	<p>You do not have permission here.</p>
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

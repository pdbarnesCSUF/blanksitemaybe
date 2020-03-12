<?php
/**
 *  @file view_user.php
 *  @brief View user
 *  
 */
use Tracy\Debugger;
require_once ('vendor/tracy.php');
require_once('include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "View User - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "ID View User.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];

$admin_error = false;
if (isset($_GET['userid']))
{
	$user = new id_User($_GET['userid']);
	if ($user->datetime_created === NULL)
		$admin_error = "Invalid user or not public";
	else if ($user->public_view == 0)
		$admin_error = "Invalid user or not public";
	else if ($user->public_view == 2 && !id_session_loggedin())
		$admin_error = "Invalid user or not public";
}
else
{
	$admin_error = "No user passed";
}
if ($admin_error)
	$heading = "Error";
else
{
	$heading = $user->getpreferredname();

	$pageinfo['title'] = $user->getpreferredname()." - ".$sitesettings['meta']['og:title'];
	$pageinfo['description'] = "View ID profile: ".$user->getpreferredname();
	if ($user->picture)
		$pageinfo['image'] = $sitesettings['profile_pic_location'].'/'.$user->userid.'.png';
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title><?php echo $heading?> - <?php echo $sitesettings['title']?></title>
        <script>$(function(){$("#sitenav-viewprofile").addClass("active");});</script>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong><?php echo $heading ?></strong></h1>
	<div id="mainwell" class="well">
<?php if ($admin_error) { ?>
	<div class="alert alert-danger">
		<p><?php echo $admin_error;?></p>
	</div>
<?php } else { ?>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_picture">picture</label>
				<p class="form-control-static col-sm-10" id="id_profileview_picture">
					<?php
					if ($user->picture)
					{?>
						<img class="id_profpic" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $user->userid;?>.png" alt="Your profile picture">
				<?php  }
					else
					{
				?>
					<img class="id_profpic" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture">
				<?php  }?>
				</p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_id">id</label>
				<p class="form-control-static col-sm-10" id="id_profileview_id"><?php echo $user->userid;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_username">Username</label>
				<p class="form-control-static col-sm-10" id="id_profileview_username"><?php echo $user->username;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_datetimecreated">date created</label>
				<p class="form-control-static col-sm-10" id="id_profileview_datetimecreated"><?php echo $user->datetime_created;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_active">active</label>
				<p class="form-control-static col-sm-10" id="id_profileview_active"><?php echo $user->active;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_status">status</label>
				<p class="form-control-static col-sm-10" id="id_profileview_status"><?php echo $user->status;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_name">name</label>
				<p class="form-control-static col-sm-10" id="id_profileview_name"><?php echo $user->profilename;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_birthday">birthday</label>
				<p class="form-control-static col-sm-10" id="id_profileview_birthday"><?php echo $user->birthday;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_location">location</label>
				<p class="form-control-static col-sm-10" id="id_profileview_location"><?php echo $user->location;?></p>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileview_gender">gender</label>
				<p class="form-control-static col-sm-10" id="id_profileview_gender"><?php echo $user->gender;?></p>
			</div>
		</div><!-- form-horizontal -->
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

<?php
use Tracy\Debugger;
require_once ('../vendor/tracy.php');
require_once('../include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "Admin: NOUSER - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "ID admin, user : NOUSER";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];

$admin_error = false;
$user_permissions = array(
	'userid' => false,
	'id_admin' => false,
	'mc_admin' => false,
	'cs16_admin' => false,
	'csgo_admin' => false,
	'jka_admin' => false,
	'terraria_admin' => false
);
if(id_account_getpermission($id_SESSION['id'],'id_admin'))
{
	if (isset($_GET['userid']))
	{
		$user = new id_User($_GET['userid']);
		if ($user->datetime_created === NULL)
			$admin_error = "Invalid user";
		else
		{
			$user_permissions = id_account_getpermissionarr($_GET['userid']);
			$pageinfo['title'] = "Admin: ".$user->username." - ".$sitesettings['meta']['og:title'];
			$pageinfo['description'] = "ID admin, user : ".$user->username;
			if ($user->picture)
				$pageinfo['image'] = $sitesettings['profile_pic_location'].'/'.$user->userid.'.png';
		}
	}
	else
	{
		$admin_error = "No user passed";
	}
}
else
	$admin_error = "No Permission";
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Admin:<?php echo $user->username.' - '.$sitesettings['title'];?></title>
        <script>$(function(){$("#sitenav-id_admin").addClass("active");});</script>
</head>
<body>
<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
<?php
if (id_account_isadmin())
{
?>
        <h1><strong>Admin User Page</strong></h1>
        <div class="panel panel-danger">
        <div class="panel-heading">WARNING!!!!</div>
        <div class="panel-body">This page does little to no validation on entry.</div>
        </div>
        <?php
	if ($admin_error === false)
	{
	?>
	<!-- ================================================== -->
	<div id="admin_account_well" class="well">
 <script>
var email_verified = <?php echo ($user->email_verified) ? "true" : "false";?>;
function emailverifyui(setverified) {
	if (setverified) {
		$("#id_adminaccount_email_verified").removeClass("label-danger");
		$("#id_adminaccount_email_verified").addClass("label-success");
                $("#id_adminaccount_email_verified").text("Verified");
		$("#id_adminaccount_email_forceverify").removeClass("btn-warning");
		$("#id_adminaccount_email_forceverify").addClass("btn-danger");
		$("#id_adminaccount_email_forceverify").text("Set Unverified");
	} else {
		$("#id_adminaccount_email_verified").removeClass("label-success");
		$("#id_adminaccount_email_verified").addClass("label-danger");
                $("#id_adminaccount_email_verified").text("Unverified");
		$("#id_adminaccount_email_forceverify").removeClass("btn-danger");
                $("#id_adminaccount_email_forceverify").addClass("btn-warning");
		$("#id_adminaccount_email_forceverify").text("Set Verified");
	}
}
$(document).ready(function() {
	emailverifyui(email_verified);
	$("#id_adminaccount_email_forceverify").click(function(e) {
		//force unverify
		$("#id_adminaccount_email_forceverify").attr('disabled',true);
		$("#id_adminaccount_email_forceverify_status").text("Processing...");
		$.ajax({
			type: "POST",
			url: "../ajax/admin/admin_edit_emailverify.php",
			data: {	userid:<?php echo $user->userid;?>,
				setverify: !email_verified
				},
			success: function (rtndata) {
				if (rtndata.action == 1) {
					if (email_verified) {
						email_verified = false;
					} else {
						email_verified = true;
					}
					emailverifyui(email_verified);
					$("#id_adminaccount_email_forceverify_status").text("");
				} else {
					$("#id_adminaccount_email_forceverify_status").text("Error " + rtndata.data);
				}
				$("#id_adminaccount_email_forceverify").attr('disabled',false);
			},
			failure: function (rtndata) {
				$("#id_adminaccount_email_forceverify_status").text("Connection Error");
				$("#id_adminaccount_email_forceverify").attr('disabled',false);
			}
		});
	});
	$("#id_adminaccount_submit").click(function(e) {
		e.preventDefault();
		$("#id_adminaccount_submit").val( "Submitting...");
		$("#id_adminaccount_submit").attr('disabled',true);
		$("#id_adminaccount_submit_status").text('');
		$("#id_adminaccount_username_status").text('');
		$("#id_adminaccount_active_status").text('');
		$("#id_adminaccount_status_status").text('');
		$("#id_adminaccount_email_status").text('');
		$("#id_adminaccount_new_password_status").text('');
		$.ajax({
			type: "POST",
			url: "../ajax/admin/admin_edit_account.php",
			data: $("#id_adminaccount_form").serialize(),
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_adminaccount_submit_status").text('Saved');
				}
				else
				{
					$("#id_adminaccount_submit_status").text('Invalid Entry');
					$("#id_adminaccount_username_status").text('' +rtndata.data.username);
					$("#id_adminaccount_active_status").text('' +rtndata.data.active);
					$("#id_adminaccount_status_status").text('' +rtndata.data.status);
					$("#id_adminaccount_email_status").text('' +rtndata.data.email);
					$("#id_adminaccount_new_password_status").text('' +rtndata.data.new_password);
				}
			},
			fail: function (rtndata) {
				$("#id_adminaccount_submit_status").text("Connection Error");
			}
		});
		$("#id_adminaccount_submit").val( "Submit");
		$("#id_adminaccount_submit").attr('disabled',false);
	});
	var delete_sanity = 0;
	var delete_sanity_pass = 3;
	$("#id_adminaccount_delete").click(function(e) {
		if (delete_sanity < delete_sanity_pass)
		{
			++delete_sanity;
			$("#id_adminaccount_delete").text("Delete Account ("+delete_sanity+"/"+delete_sanity_pass+")");
		}
		else
		{
			$("#id_adminaccount_delete").text("Deleting Account...");
			//ajax here
			$.ajax({
				type: "POST",
				url: "../ajax/admin/admin_delete_account.php",
				data: {userid : <?php echo $user->userid; ?>},
				success: function (rtndata) {
					if (rtndata.action == 1)
					{
						//location.assign("admin.php");
					}
					else
					{
						delete_sanity = 0;
						$("#id_adminaccount_delete").text("Delete Account ("+delete_sanity+"/"+delete_sanity_pass+")");
						$("#id_adminaccount_delete_status").text("error msg");
					}
				},
				fail: function (rtndata) {
					delete_sanity = 0;
					$("#id_adminaccount_delete").text("Delete Account ("+delete_sanity+"/"+delete_sanity_pass+")");
					$("#id_adminaccount_delete_status").text("Connection Error");
				}
			});
			//if success
				//back to admin dashboard
		}
	});
});
</script>
		<h2 id="admin_account">Account</h2>
		<h3 id="admin_username"><?php echo $user->username;?></h3>
		<div class="form-horizontal">
			<form id="id_adminaccount_form">
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_id">ID</label>
					<p class="form-control-static col-sm-10" id="id_adminaccount_id"><?php echo $user->userid;?></p>
					<input type="hidden" name="userid" value="<?php echo $user->userid;?>">
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_username">Username</label>
					<div class="col-sm-10">
						<input class="form-control col-sm-10" id="id_adminaccount_username" name="username" value="<?php echo $user->username;?>">
						<p class="help-block">only to change capitalization</p>
						<div class="ajax-response" id="id_adminaccount_username_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_datetimecreated">Date Created</label>
					<p class="form-control-static col-sm-10" id="id_adminaccount_datetimecreated"><?php echo $user->datetime_created;?></p>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<div class="checkbox">
							<label><input id="id_adminaccount_active" name="active" type="checkbox" <?php if ($user->active) echo 'CHECKED';?>>Active</label>
							<div class="ajax-response" id="id_adminaccount_active_status"></div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_status">Status</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminaccount_status" name="status" placeholder="Enter Status" value="<?php echo $user->status;?>">
						<div class="ajax-response" id="id_adminaccount_status_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_email">Email</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminaccount_email" email="email" placeholder="Enter email" name="email" value="<?php echo $user->email;?>">
						<div class="ajax-response" id="id_adminaccount_email_status"></div>
						<span class="label" id="id_adminaccount_email_verified">Verified?</span>
						<button type="button" class="btn btn-danger" id="id_adminaccount_email_forceverify">Force unverify?</button>
						<div class="ajax-response" id="id_adminaccount_email_forceverify_status"></div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminaccount_new_password">New Password</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="id_adminaccount_new_password" name="new_password" placeholder="New Password">
						<div class="ajax-response" id="id_adminaccount_new_password_status"></div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<button class="btn btn-primary" id="id_adminaccount_submit">Submit</button>
					<div class="ajax-response" id="id_adminaccount_submit_status"></div>
				</div>
			</form>
				<button class="btn btn-danger" id="id_adminaccount_delete">Delete Account (0/3)</button>
				<p class="help-block">Should disable (and not delete) in most cases!</p>
				<div class="ajax-response" id="id_adminaccount_delete_status"></div>
		</div>
	</div>
	<!-- -------------------------------------------------- -->
	<div id="admin_permission_well" class="well">
<script>
$(document).ready(function() {
//-------permission edit---------
	$("#id_adminpermission_submit").click(function(e) {
		e.preventDefault();
		$("#id_adminpermission_submit").val( "Submitting...");
		$("#id_adminpermission_submit").attr('disabled',true);
		$("#id_adminpermission_submit_status").text('');
		$.ajax({
			type: "POST",
			url: "../ajax/admin/admin_edit_permission.php",
			data: $("#id_adminpermission_form").serialize(),
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_adminpermission_submit_status").text('Saved');
				}
				else
				{
					$("#id_adminpermission_submit_status").text('Invalid Entry');
				}
			},
			fail: function (rtndata) {
				$("#id_adminpermission_submit_status").text("Connection Error");
			}
		});
		$("#id_adminpermission_submit").val( "Submit");
		$("#id_adminpermission_submit").attr('disabled',false);
	});
});
</script>
	<h2>Permissions</h2>
		<div class="form-horizontal">
			<form id="id_adminpermission_form">
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_id">ID</label>
					<p class="form-control-static col-sm-10" id="id_adminpermission_id"><?php echo $user->userid;?></p>
					<input type="hidden" name="userid" value="<?php echo $user->userid;?>">
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_username">Username</label>
					<p class="form-control-static col-sm-10" id="id_adminpermission_username"><?php echo $user->username;?></p>
				</div>
				<!-- ========== -->
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_id_admin">ID Admin</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminpermission_id_admin" name="id_admin" type="checkbox" <?php echo $user_permissions['id_admin'] ? 'CHECKED' : '';?>>
						<p class="help-block">At this moment, it's total control over ID related functions/users</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_mc_admin">Minecraft</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminpermission_mc_admin" name="mc_admin" type="checkbox" <?php echo $user_permissions['mc_admin'] ? 'CHECKED' : '';?>>
						<p class="help-block">Server control page</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_cs16_admin">Counter-Strike 1.6</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminpermission_cs16_admin" name="cs16_admin" type="checkbox" <?php echo $user_permissions['cs16_admin'] ? 'CHECKED' : '';?>>
						<p class="help-block">Server control page</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_csgo_admin">Counter-Strike: Global Offensive</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminpermission_csgo_admin" name="csgo_admin" type="checkbox" <?php echo $user_permissions['csgo_admin'] ? 'CHECKED' : '';?>>
						<p class="help-block">Server control page</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminpermission_terraria_admin">Terraria</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminpermission_terraria_admin" name="terraria_admin" type="checkbox" <?php echo $user_permissions['terraria_admin'] ? 'CHECKED' : '';?>>
						<p class="help-block">Server control page</p>
					</div>
				</div>
				<!-- ==========-->
				<div class="form-group">
					<button class="btn btn-primary" id="id_adminpermission_submit">Submit</button>
					<div class="ajax-response" id="id_adminpermission_submit_status"></div>
				</div>
			</form>
		</div>
	</div>
	<!-- -------------------------------------------------- -->
	<div id="admin_profile_well" class="well">
<script>
$(document).ready(function() {
	//-------pic reset---------
	$("#id_adminprofile_picture_resetbtn").click(function(e) {
		$("#id_adminprofile_picture_resetbtn").val( "Removing...");
		$("#id_adminprofile_picture_resetbtn").attr('disabled',true);
		$.ajax({
			type: "POST",
			url: "../ajax/admin/admin_reset_picture.php",
			data: {userid:<?php echo $user->userid;?>},
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_adminprofile_picture").attr('src',"<?php echo $sitesettings['images_address'];?>/PTU_anon.png");
					$("#id_adminprofile_picture").attr('alt',"No profile picture");
				}
				else
				{
					$("#id_adminprofile_picture_reset_status").text('' + rtndata.data);
					$("#id_adminprofile_picture_resetbtn").attr('disabled',false);
				}
			},
			failure: function (rtndata) {
				$("#id_adminprofile_picture_reset_status").text("Connection Error");
				$("#id_adminprofile_picture_resetbtn").attr('disabled',false);
			}
		});
		$("#id_adminprofile_picture_resetbtn").val("Remove");
	});
	//-------profile edit---------
	$("#id_adminprofile_submit").click(function(e) {
		e.preventDefault();
		$("#id_adminprofile_submit").val( "Submitting...");
		$("#id_adminprofile_submit").attr('disabled',true);
		$("#id_adminprofile_submit_status").text('');
		$("#id_adminprofile_profilename_status").text('');
		$("#id_adminprofile_birthday_status").text('');
		$("#id_adminprofile_gender_status").text('');
		$("#id_adminprofile_location_status").text('');
		$("#id_adminprofile_publicview_status").text('');
		$("#id_adminprofile_prefername_status").text('');
		$.ajax({
			type: "POST",
			url: "../ajax/admin/admin_edit_profile.php",
			data: $("#id_adminprofile_form").serialize(),
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_adminprofile_submit_status").text('Saved');
				}
				else
				{
					$("#id_adminprofile_submit_status").text('Invalid Entry');
					$("#id_adminprofile_profilename_status").text('' +rtndata.data.profilename);
					$("#id_adminprofile_birthday_status").text('' +rtndata.data.birthday);
					$("#id_adminprofile_gender_status").text('' +rtndata.data.gender);
					$("#id_adminprofile_location_status").text('' +rtndata.data.location);
					$("#id_adminprofile_publicview_status").text('' +rtndata.data.publicview);
					$("#id_adminprofile_prefername_status").text('' +rtndata.data.prefername);
				}
			},
			fail: function (rtndata) {
				$("#id_adminprofile_submit_status").text("Connection Error");
			}
		});
		$("#id_adminprofile_submit").val( "Submit");
		$("#id_adminprofile_submit").attr('disabled',false);
	});
});
</script>
	<h2>Profile</h2>
	<h3 id="admin_preferredname">preferredname</h3>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_adminprofile_picture">Picture</label>
				<div class="col-sm-10">
					<?php
					if ($user->picture)
					{?>
						<img class="id_profpic col-sm-4" id="id_adminprofile_picture" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $user->userid;?>.png" alt="Your profile picture" />
					<?php  }
						else
						{
					?>
						<img class="id_profpic col-sm-4" id="id_adminprofile_picture" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture" />
					<?php  }?>
					<div class="col-sm-8">
						<input class="btn btn-danger" type="button" id="id_adminprofile_picture_resetbtn" value="Remove" <?php if (!$user->picture) echo 'DISABLED';?>>
						<div class="ajax-response" id="id_adminprofile_picture_reset_status"></div>
					</div>
				</div>
			</div>
			<hr>
			<form id="id_adminprofile_form">
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_id">ID</label>
					<p class="form-control-static col-sm-10" id="id_adminprofile_id"><?php echo $user->userid;?></p>
					<input type="hidden" name="userid" value="<?php echo $user->userid;?>">
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_username">Username</label>
					<p class="form-control-static col-sm-10" id="id_adminprofile_username"><?php echo $user->username;?></p>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_datetimecreated">Date Created</label>
					<p class="form-control-static col-sm-10" id="id_adminprofile_datetimecreated"><?php echo $user->datetime_created;?></p>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_profilename">Name</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminprofile_profilename" name="profilename" placeholder="Enter Name" value="<?php echo $user->profilename;?>">
						<div class="ajax-response" id="id_adminprofile_profilename_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_birthday">Birthday</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminprofile_birthday" name="birthday" placeholder="mm/dd/yyy" type="date" value="<?php echo $user->birthday;?>">
						<div class="ajax-response" id="id_adminprofile_birthday_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_location">Location</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminprofile_location" name="location" placeholder="Enter Location" value="<?php echo $user->location;?>">
						<div class="ajax-response" id="id_adminprofile_location_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_gender">Gender</label>
					<div class="col-sm-10">
						<select class="form-control" id="id_adminprofile_gender" name="gender" >
							<option value=""  <?php echo $user->gender == ''? 'SELECTED' : '';?>></option>
							<option value="m" <?php echo $user->gender == 'm'? 'SELECTED' : '';?>>He</option>
							<option value="f" <?php echo $user->gender == 'f'? 'SELECTED' : '';?>>She</option>
						</select>
						<div class="ajax-response" id="id_adminprofile_gender_status"></div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_publicview">Public</label>
					<div class="col-sm-10">
						<select class="form-control" id="id_adminprofile_publicview" name="publicview" >
							<option value="0" <?php echo $user->public_view == '0'? 'SELECTED' : '';?>>Private</option>
							<option value="1" <?php echo $user->public_view == '1'? 'SELECTED' : '';?>>Public</option>
							<option value="2" <?php echo $user->public_view == '2'? 'SELECTED' : '';?>>Visible to Members</option>
						</select>
						<div class="ajax-response" id="id_adminprofile_publicview_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_adminprofile_prefername">Prefer profile name over username</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_adminprofile_prefername" name="prefername" type="checkbox" <?php echo $user->prefer_profilename ? 'CHECKED' : '';?>>
						<div class="ajax-response" id="id_adminprofile_prefername_status"></div>
					</div>
				</div>
				<div class="form-group">
					<button class="btn btn-primary" id="id_adminprofile_submit">Submit</button>
					<div class="ajax-response" id="id_adminprofile_submit_status"></div>
				</div>
			</form>
		</div>
	</div><!-- admin_profile_well -->
	<!-- -------------------------------------------------- -->
	<div id="admin_session_well" class="well">
		<h2>Sessions</h2>
		<table class="table table-condensed table-hover" id="admin_session_table">
			<thead>
				<tr>
					<th><input type="checkbox" id="session_checkall"></th>
					<th>sessionid</th>
					<th>expires</th>
					<th>datetime_active</th>
					<th>last_ip</th>
					<th>device_type</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>placeholder for future</td>
				</tr>
			</tbody>
		</table>
	</div><!-- admin_session_well -->
	<!-- ================================================== -->
	<?php
	}
	else
	{?>
		<h2>Error</h2>
		<p><strong>Error:</strong><?php echo $admin_error;?></p>
		<a href="admin.php">Back to admin page</a>
	<?php
	}
?>
<?php
}//if id_admin
else
{
?>
	<div class="alert alert-danger">
		<p>No Permission</p>
	</div>
<?php
}
?>
</div><!--mainpage-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

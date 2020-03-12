<?php
/**
 *  @file user_profile.php
 *  @brief User management - Profile
 *  
 */
use Tracy\Debugger;
require_once ('vendor/tracy.php');
require_once('include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "Edit Profile - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "Edit ID Profile.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Profile - <?=$sitesettings['title']?></title>
        <script>$(function(){$("#sitenav-user-profile").addClass("active");});</script>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong>Profile</strong></h1>
	<div id="mainwell" class="well">
<?php if (id_session_loggedin()) { ?>
<script>
$(document).ready(function() {
	//http://blog.teamtreehouse.com/uploading-files-ajax
	//$("#id_profileedit_picture_form").submit( function(e) {
	$("#id_profileedit_picturesubmit").click( function(e) {
		e.preventDefault();
		$("#id_profileedit_picturesubmit").text("Uploading...");
		//get files
		var file = $("#id_profileedit_picturefile").prop("files");
		// Create a new FormData object.
		var formData = new FormData();
		
		formData.append('prof_picture', file[0], file[0].name);
		// Set up the request.
		//var xhr = new XMLHttpRequest();
		$.ajax({
			type: "POST",
			url: "ajax/edit_profile_picture.php",
			data: formData,
			processData: false,
			contentType: false,
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_profileedit_picture_status").text("Success");
					d = new Date();
					$("#id_profileedit_picture").attr('src',"<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $id_SESSION['id_current_user']->userid;?>.png?"+d.getTime());
					$("#id_profileedit_picture").attr('alt',"Your profile picture");
				}
				else
				{
					$("#id_profileedit_picture_status").text("Error"+rtndata.data);
					console.log("picup:"+rtndata.action);
				}
			},
			failure: function (rtndata) {
				$("#id_profileedit_picture_status").text("Connection Error"+rtndata.data);
				console.log("failure upload pic");
			}
		});
		$("#id_profileedit_picturesubmit").text("Upload");

	});
	//-------pic reset---------
	$("#id_profileedit_picture_resetbtn").click(function(e) {
		$("#id_profileedit_picture_resetbtn").val( "Removing...");
		$("#id_profileedit_picture_resetbtn").attr('disabled',true);
		$.ajax({
			type: "POST",
			url: "ajax/edit_profile_picture.php",
			data: {reset:true},
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_profileedit_picture").attr('src',"<?php echo $sitesettings['images_address'];?>/PTU_anon.png");
					$("#id_profileedit_picture").attr('alt',"No profile picture");
				}
				else
				{
					$("#id_profileedit_picture_reset_status").text('' + rtndata.data);
					$("#id_profileedit_picture_resetbtn").attr('disabled',false);
				}
			},
			failure: function (rtndata) {
				$("#id_profileedit_picture_reset_status").text("Connection Error");
				$("#id_profileedit_picture_resetbtn").attr('disabled',false);
			}
		});
		$("#id_profileedit_picture_resetbtn").val("Remove");
	});
	//-------profile edit---------
	$("#id_profileedit_submit").click(function(e) {
		e.preventDefault();
		$("#id_profileedit_submit").val( "Submitting...");
		$("#id_profileedit_submit").attr('disabled',true);
		$("#id_profileedit_submit_status").text('');
		$("#id_profileedit_profilename_status").text('');
		$("#id_profileedit_birthday_status").text('');
		$("#id_profileedit_gender_status").text('');
		$("#id_profileedit_location_status").text('');
		$("#id_profileedit_publicview_status").text('');
		$("#id_profileedit_prefername_status").text('');
		$.ajax({
			type: "POST",
			url: "ajax/edit_profile.php",
			data: $("#id_profileedit_form").serialize(),
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_profileedit_submit_status").text('Saved');
				}
				else
				{
					$("#id_profileedit_submit_status").text('Invalid Entry');
					$("#id_profileedit_profilename_status").text('' +rtndata.data.profilename);
					$("#id_profileedit_birthday_status").text('' +rtndata.data.birthday);
					$("#id_profileedit_gender_status").text('' +rtndata.data.gender);
					$("#id_profileedit_location_status").text('' +rtndata.data.location);
					$("#id_profileedit_publicview_status").text('' +rtndata.data.publicview);
					$("#id_profileedit_prefername_status").text('' +rtndata.data.prefername);
				}
			},
			fail: function (rtndata) {
				$("#id_profileedit_submit_status").text("Connection Error");
			}
		});
		$("#id_profileedit_submit").val( "Submit");
		$("#id_profileedit_submit").attr('disabled',false);
	});
});
</script>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-sm-2" for="id_profileedit_picture">Picture</label>
				<div class="col-sm-10">
					<?php
					if ($id_SESSION['id_current_user']->picture)
					{?>
						<img class="id_profpic col-sm-4" id="id_profileedit_picture" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $id_SESSION['id_current_user']->userid;?>.png" alt="Your profile picture" />
					<?php  }
						else
						{
					?>
						<img class="id_profpic col-sm-4" id="id_profileedit_picture" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture" />
					<?php  }?>
					<div class="col-sm-8">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $sitesettings['profile_pic_max_s'];?>" />
						<input class="btn btn-default" id="id_profileedit_picturefile" name="prof_picture" type="file" accept="image/png,image/jpeg,image/gif">
						<button id="id_profileedit_picturesubmit">Upload</button>
						<div class="help-block">
							PNG,JPG,GIF. Max file size:<?php echo $sitesettings['profile_pic_max_s']/1024;?> kb.
							Will resize to max of:<?php echo $sitesettings['profile_pic_max_w'];?>x<?php echo $sitesettings['profile_pic_max_h'];?>
						</div>
						<div class="ajax-response" id="id_profileedit_picture_status"></div>
						<input class="btn btn-danger" type="button" id="id_profileedit_picture_resetbtn" value="Remove" <?php if (!$id_SESSION['id_current_user']->picture) echo 'DISABLED';?>>
						<div class="ajax-response" id="id_profileedit_picture_reset_status"></div>
					</div>
				</div>
			</div>
			<hr>
			<form id="id_profileedit_form">
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_id">ID</label>
					<p class="form-control-static col-sm-10" id="id_profileedit_id"><?php echo $id_SESSION['id_current_user']->userid;?></p>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_username">Username</label>
					<p class="form-control-static col-sm-10" id="id_profileedit_username"><?php echo $id_SESSION['id_current_user']->username;?></p>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_datetimecreated">Date Created</label>
					<p class="form-control-static col-sm-10" id="id_profileedit_datetimecreated"><?php echo $id_SESSION['id_current_user']->datetime_created;?></p>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_profilename">Name</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_profileedit_profilename" name="profilename" placeholder="Enter Name" value="<?php echo $id_SESSION['id_current_user']->profilename;?>">
						<div class="ajax-response" id="id_profileedit_profilename_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_birthday">Birthday</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_profileedit_birthday" name="birthday" placeholder="mm/dd/yyy" type="date" value="<?php echo $id_SESSION['id_current_user']->birthday;?>">
						<div class="ajax-response" id="id_profileedit_birthday_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_location">Location</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_profileedit_location" name="location" placeholder="Enter Location" value="<?php echo $id_SESSION['id_current_user']->location;?>">
						<div class="ajax-response" id="id_profileedit_location_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_gender">Gender</label>
					<div class="col-sm-10">
						<select class="form-control" id="id_profileedit_gender" name="gender" >
							<option value=""  <?php echo $id_SESSION['id_current_user']->gender == ''? 'SELECTED' : '';?>></option>
							<option value="m" <?php echo $id_SESSION['id_current_user']->gender == 'm'? 'SELECTED' : '';?>>He</option>
							<option value="f" <?php echo $id_SESSION['id_current_user']->gender == 'f'? 'SELECTED' : '';?>>She</option>
						</select>
						<div class="ajax-response" id="id_profileedit_gender_status"></div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_publicview">Public</label>
					<div class="col-sm-10">
						<select class="form-control" id="id_profileedit_publicview" name="publicview" >
							<option value="0" <?php echo $id_SESSION['id_current_user']->public_view == '0'? 'SELECTED' : '';?>>Private</option>
							<option value="1" <?php echo $id_SESSION['id_current_user']->public_view == '1'? 'SELECTED' : '';?>>Public</option>
							<option value="2" <?php echo $id_SESSION['id_current_user']->public_view == '2'? 'SELECTED' : '';?>>Visible to Members</option>
							
						</select>
						<div class="ajax-response" id="id_profileedit_publicview_status"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="id_profileedit_prefername">Prefer profile name over username</label>
					<div class="col-sm-10">
						<input class="form-control" id="id_profileedit_prefername" name="prefername" type="checkbox" <?php echo $id_SESSION['id_current_user']->prefer_profilename ? 'CHECKED' : '';?>>
						<div class="ajax-response" id="id_profileedit_prefername_status"></div>
					</div>
				</div>
				<div class="form-group">
					<button class="btn btn-primary" id="id_profileedit_submit">Submit</button>
					<div class="ajax-response" id="id_profileedit_submit_status"></div>
				</div>
			</form><!--id_profileedit_form-->
		</div><!--form-horizontal-->
<?php } else { ?>
	<p>not loggedin</p>
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

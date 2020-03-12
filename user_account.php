<?php
/**
 *  @file user_account.php
 *  @brief user management - account
 *
 */
use Tracy\Debugger;
require_once ('vendor/tracy.php');
require_once('include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "Edit Account - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "Edit ID account.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Account - <?=$sitesettings['title']?></title>
        <script>$(function(){$("#sitenav-user-account").addClass("active");});</script>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong>Account</strong></h1>
	<div id="mainwell" class="well">
<?php if (id_session_loggedin()) { ?>
<script>
var email_verified = <?php echo ($id_SESSION['id_current_user']->email_verified) ? "true" : "false";?>;
function emailverifyui(setverified) {
        if (setverified) {
                $("#id_accountedit_email_verified").removeClass("label-danger");
                $("#id_accountedit_email_verified").addClass("label-success");
                $("#id_accountedit_email_verified").text("Verified");
		$("#id_accountedit_email_resend").attr('disabled',true);
		$("#id_accountedit_email_resend").hide();
                $("#id_accountedit_email_resend_status").hide();
        } else {
                $("#id_accountedit_email_verified").removeClass("label-success");
                $("#id_accountedit_email_verified").addClass("label-danger");
                $("#id_accountedit_email_verified").text("Unverified");
        }
}
$(document).ready(function() {
	emailverifyui(email_verified);
	//TODO probably make this page dynamic/ajax/json loaded as well. because i dont like this section.
	$("#id_accountedit_email_resend").click(function(e) {
		//verify_email.php ajax
                $("#id_accountedit_email_resend").attr('disabled',true);
                $("#id_accountedit_email_resend_status").text("Processing...");
                $.ajax({
                        type: "POST",
                        url: "ajax/email_verify.php",
                        //data: { },
                        success: function (rtndata) {
                                if (rtndata.action == 1) {
                                        $("#id_accountedit_email_resend_status").text("Sent!");
                                } else {
                                        $("#id_accountedit_email_resend_status").text("Error " + rtndata.data);
                                }
                                $("#id_accountedit_email_resend").attr('disabled',false);
                        },
                        failure: function (rtndata) {
                                $("#id_accountedit_email_resend_status").text("Connection Error");
                                $("#id_accountedit_email_resend").attr('disabled',false);
                        }
                });
	});
	$("#id_accountedit_submit").click(function(e) {
		e.preventDefault();
		$("#id_accountedit_submit").val( "Submitting...");
		$("#id_accountedit_submit").attr('disabled',true);
		$("#id_accountedit_submit_status").text('');
		$("#id_accountedit_username_status").text('');
		$("#id_accountedit_active_status").text('');
		$("#id_accountedit_status_status").text('');
		$("#id_accountedit_email_status").text('');
		$("#id_accountedit_new_password_status").text('');
		$("#id_accountedit_new_password_confirm_status").text('');
		$("#id_accountedit_current_password_status").text('');
		$.ajax({
			type: "POST",
			url: "ajax/edit_account.php",
			data: $("#id_accountedit_form").serialize(),
			success: function (rtndata) {
				if (rtndata.action == 1)
				{
					$("#id_accountedit_submit_status").text('Saved');
				}
				else
				{
					$("#id_accountedit_submit_status").text('Invalid Entry');
					$("#id_accountedit_username_status").text('' +rtndata.data.username);
					$("#id_accountedit_active_status").text('' +rtndata.data.active);
					$("#id_accountedit_status_status").text('' +rtndata.data.status);
					$("#id_accountedit_email_status").text('' +rtndata.data.email);
					$("#id_accountedit_new_password_status").text('' +rtndata.data.new_password);
					$("#id_accountedit_new_password_confirm_status").text('' +rtndata.data.new_password_confirm);
					$("#id_accountedit_current_password_status").text('' +rtndata.data.current_password);
				}
			},
			fail: function (rtndata) {
				$("#id_accountedit_submit_status").text("Connection Error");
			}
		});
		$("#id_accountedit_submit").val( "Submit");
		$("#id_accountedit_submit").attr('disabled',false);
	});
});
</script>
<div class="form-horizontal">
	<form id="id_accountedit_form">
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_id">ID</label>
			<p class="form-control-static col-sm-10" id="id_accountedit_id"><?php echo $id_SESSION['id_current_user']->userid;?></p>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_username">Username</label>
			<div class="col-sm-10">
				<input class="form-control" id="id_accountedit_username" name="username" value="<?php echo $id_SESSION['id_current_user']->username;?>">
				<p class="help-block">only to change capitalization</p>
				<div class="ajax-response" id="id_accountedit_username_status"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_datetimecreated">Date Created</label>
			<p class="form-control-static col-sm-10" id="id_accountedit_datetimecreated"><?php echo $id_SESSION['id_current_user']->datetime_created;?></p>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<div class="checkbox">
					<label><input id="id_accountedit_active" name="active" type="checkbox" <?php if ($id_SESSION['id_current_user']->active) echo 'CHECKED';?>>Active</label>
					<div class="ajax-response" id="id_accountedit_active_status"></div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_status">Status</label>
			<div class="col-sm-10">
				<input class="form-control" id="id_accountedit_status" name="status" placeholder="Enter Status" value="<?php echo $id_SESSION['id_current_user']->status;?>">
				<div class="ajax-response" id="id_accountedit_status_status"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_email">Email</label>
			<div class="col-sm-10">
				<input class="form-control" id="id_accountedit_email" email="email" placeholder="Enter email" name="email" value="<?php echo $id_SESSION['id_current_user']->email;?>">
				<div class="ajax-response" id="id_accountedit_email_status"></div>
				<span class="label" id="id_accountedit_email_verified">Verified?</span>
        <button type="button" class="btn btn-danger" id="id_accountedit_email_resend">Resend Verification</button>
        <div class="ajax-response" id="id_accountedit_email_resend_status"></div>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_new_password">New Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="id_accountedit_new_password" name="new_password" placeholder="New Password">
				<div class="ajax-response" id="id_accountedit_new_password_status"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_new_password_confirm">Confirm New Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="id_accountedit_new_password_confirm" name="new_password_confirm" placeholder="New Password Again">
				<div class="ajax-response" id="id_accountedit_new_password_confirm_status"></div>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<label class="control-label col-sm-2" for="id_accountedit_current_password">Current Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="id_accountedit_current_password" name="current_password" required>
				<p class="help-block">Required to change anything</p>
				<div class="ajax-response" id="id_accountedit_current_password_status"></div>
			</div>
		</div>
		<div class="form-group">
			<button class="btn btn-primary" id="id_accountedit_submit">Submit</button>
			<div class="ajax-response" id="id_accountedit_submit_status"></div>
		</div>
	</form><!--id_accountedit_form-->
</div><!--form-horizontal-->
<?php } else { ?>
	<p>not loggedin</p>
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

<?php
/**
 *  @file create.php
 *  @brief Create user account
 *
 */
use Tracy\Debugger;
require_once ('vendor/tracy.php');
require_once('include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "Create Account - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "Create an account.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Create Account - <?php echo $sitesettings['title'];?></title>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container">
	<h1><strong>Admin Page</strong></h1>
	<div id="mainwell" class="well">
		<h1>Create account</h1>
<?php if (id_session_loggedin()) { ?>
	Can't create account. Already logged in.
<?php } else { ?>
<script>
function labelajax(selector,state_class,message){
	if (state_class == 'hide')
	{
		$(selector).hide();
	}
	else {
		$(selector).show();
		$(selector).removeClass();
		$(selector).addClass("label " + state_class);
		$(selector).text(message);
	}
}//func labelajax
var valid_username = false;
var valid_email = false;
var valid_password = false;
var valid_password_verify = false;
	$(document).ready(function () {
		//TODO update when stop typing
		$('#id_create_username').on('blur',function(e){
			$.ajax({
				type: "POST",
				url: "ajax/create/validate_username.php",
				data: { username : $("#id_create_username").val()},
				success: function (rtndata) {
					if (rtndata.action == 1)
					{
						if (rtndata.data.valid)
						{
							labelajax("#id_create_username_status","label-success",rtndata.data.reason);
							valid_username = true;
						}
						else {
							labelajax("#id_create_username_status","label-danger",rtndata.data.reason);
							valid_username = false;
						}
					}
					else
					{
						labelajax("#id_create_username_status","label-danger","Server Error");
						valid_username = false;
					}
				},
				fail: function (rtndata) {
					labelajax("#id_create_username_status","label-danger","Connection Error");
					valid_username = false;
				}
			});
		});
		//TODO update when stop typing
		$('#id_create_email').on('blur',function(e){
			$.ajax({
				type: "POST",
				url: "ajax/create/validate_email.php",
				data: { email : $("#id_create_email").val()},
				success: function (rtndata) {
					if (rtndata.action == 1)
					{
						if (rtndata.data.valid)
						{
							labelajax("#id_create_email_status","label-success",rtndata.data.reason);
							valid_email = true;
						}
						else {
							labelajax("#id_create_email_status","label-danger",rtndata.data.reason);
							valid_email = false;
						}
					}
					else
					{
						labelajax("#id_create_email_status","label-danger","Server Error");
						valid_email = false;
					}
				},
				fail: function (rtndata) {
					labelajax("#id_create_email_status","label-danger","Connection Error");
					valid_email = false;
				}
			});
		});
		//password matching
		$('#id_create_password').on('input',function(e){
			if ($('#id_create_password').val().length >= 4) {
				labelajax("#id_create_password_status","label-success","OK");
				valid_password = true;
			}
			else {
				labelajax("#id_create_password_status","label-danger","Must be greater than 4 characters long");
				valid_password = false;
			}

			if ($('#id_create_password').val() == $('#id_create_password_confirm').val()) {
				labelajax("#id_create_password_confirm_status","label-success","Matches");
				valid_password_verify = true;
			}
			else {
				labelajax("#id_create_password_confirm_status","label-danger","Does NOT match");
				valid_password_verify = false;
			}
		});
		$('#id_create_password_confirm').on('input',function(e){
			if ($('#id_create_password').val() == $('#id_create_password_confirm').val()) {
				labelajax("#id_create_password_confirm_status","label-success","Matches");
				valid_password_verify = true;
			}
			else {
				labelajax("#id_create_password_confirm_status","label-danger","Does NOT match");
				valid_password_verify = false;
			}
		});
		//submit
		$("#id_create_createbtn").click(function(e) {
			if (valid_username && valid_email && valid_password && valid_password_verify) {
				$("#id_create_createbtn").prop('value',"Creating...");
				$("#id_create_createbtn").prop('disabled',true);
				$.ajax({
					type: "POST",
					url: "ajax/create/create_account.php",
					data: $("#id_create_form").serialize(),
					xhrFields: { withCredentials: true },
					success: function (rtndata) {
						if (rtndata.action == 1)
						{
							if (rtndata.data.sessionid != false)
							{
								$("#id_create_status").text('Success. Logged In. Taking you to homepage');
								window.location.href = "<?php echo $sitesettings['home_address'];?>";
							}
							else
							{
								$("#id_create_status").text('Server Error. Created? but not logged in.');
							}
						}//if (rtndata.action == 1)
						else
						{
							if (rtndata.data.username.valid) {
								labelajax("#id_create_username_status","label-success",rtndata.data.username.reason);
								valid_username = true;
							}
							else {
								labelajax("#id_create_username_status","label-danger" ,rtndata.data.username.reason);
								valid_username = false;
							}
							if (rtndata.data.email.valid) {
								labelajax("#id_create_email_status","label-success",rtndata.data.email.reason);
								valid_email = true;
							}
							else {
								labelajax("#id_create_email_status","label-danger" ,rtndata.data.email.reason);
								valid_email = false;
							}
							if (rtndata.data.password.valid) {
								labelajax("#id_create_password_status","label-success",rtndata.data.password.reason);
								valid_password = true;
							}
							else {
								labelajax("#id_create_password_status","label-danger" ,rtndata.data.password.reason);
								valid_password = false;
							}
							if (rtndata.data.password_confirm.valid) {
								labelajax("#id_create_password_confirm_status","label-success",rtndata.data.password_confirm.reason);
								valid_password_verify = true;
							}
							else {
								labelajax("#id_create_password_confirm_status","label-danger" ,rtndata.data.password_confirm.reason);
								valid_password_verify = false;
							}
						}//else of (rtndata.action == 1)
					},
					fail: function (rtndata) {
						$("#id_create_status").text("Connection Error");
					}
				});//ajax
				$("#id_create_password_confirm").val('');
				$("#id_create_createbtn").prop('value',"Create");
				$("#id_create_createbtn").prop('disabled',false);
			}//if everything valid
			else {
				$("#id_create_status").text('Please check all fields.');
			}
		});
	});
</script>
<form class="form-horizontal" id="id_create_form">
	<div class="form-group">
		<label class="control-label" for="id_create_username"><span class="glyphicon glyphicon-user"></span> Username</label>
		<input class="form-control" id="id_create_username" name="username" required>
		<span class="label" id="id_create_username_status"></span>
	</div>
	<div class="form-group">
		<label class="control-label" for="id_create_email"><span class="glyphicon glyphicon-envelope"></span> Email</label>
		<input class="form-control" type="email" id="id_create_email" name="email" required>
		<span class="label" id="id_create_email_status"></span>
	</div>
	<div class="form-group">
		<label class="control-label" for="id_create_password"><span class="glyphicon glyphicon-lock"></span> Password</label>
		<input class="form-control" id="id_create_password" name="password" type="password" required>
		<span class="label label-info" id="id_create_password_status"></span>
	</div>
	<div class="form-group">
		<label class="control-label" for="id_create_password_confirm"><span class="glyphicon glyphicon-lock"></span><span class="glyphicon glyphicon-lock"></span> Confirm Password</label>
		<input class="form-control" id="id_create_password_confirm" name="password_confirm" type="password" required>
		<span class="label" id="id_create_password_confirm_status"></span>
		<input type="hidden" name="device" value="web">
	</div>
</form>
<input class="btn btn-default" id="id_create_createbtn" value="Create" type="button">
<div class="ajax_response" id="id_create_status"></div>
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

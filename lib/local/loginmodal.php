<?php
/*
 * loginmodal.php
 */
require_once($id_ROOT.'/config/sitesettings.php');
?>
<div id="id_modal_login" class="id_modal">
<script>
$(document).ready(function () {
	$("#id_modal_login_close").click( function() {
		$("#id_modal_login_username").blur();
		$("#id_modal_login_password").blur();
		$("#id_modal_login").hide();
	});
	// https://stackoverflow.com/questions/1960240/jquery-ajax-submit-form
	// this is the id of the form
	$("#id_modal_login_submit").click(function(e) {
		$("#id_modal_login_submit").prop('value',"Logging in...");
		$("#id_modal_login_submit").prop('disabled',true);
		$.ajax({
				type: "POST",
				url: "<?php echo $sitesettings['api_address']?>/login.php",
				data: $("#id_modal_login_form").serialize(), // serializes the form's elements.
				xhrFields: { withCredentials: true },
				success: function(rtndata)
				{
					//console.log(data); // show response from the php script.
					if (rtndata.action == 1)
					{
						//console.log("success");
						location.reload();
					}
					else
					{
						console.log("Error:"+rtndata.action);
						$("#id_modal_login_status").text('' + rtndata.data);
					}
				},
				fail: function (rtndata) {
					$("#id_modal_login_status").text("Connection Error");
				}
			});
			$("#id_modal_login_submit").prop('value',"Login");
			$("#id_modal_login_submit").prop('disabled',false);
		e.preventDefault(); // avoid to execute the actual submit of the form.
	});
});
</script>
<div id="id_modal_login_content" class="id_modal_content">
	<input type="button" id="id_modal_login_close" class="id_modal_button id_modal_close" value="Close">
	<div id="banner">
		<a href="<?php echo $sitesettings['home_address']?>" target="_blank">ID</a>
	</div>
	<form id="id_modal_login_form">
		<div id="id_modal_login_username_row">
			<label for="id_modal_login_username">Username</label>
			<input id="id_modal_login_username" name="username" placeholder="Username" required />
		</div>
		<div id="id_modal_login_password_row">
			<label for="id_modal_login_password">Password</label>
			<input id="id_modal_login_password" name="password" placeholder="Password" type="password" required />
		</div>
		<input type="hidden" name="device" value="web">
		<div id="id_modal_login_buttons_row">
			<input type="submit" id="id_modal_login_submit" class="id_modal_button" value="Login">
			<del><a href="<?php echo $sitesettings['home_address']?>/forgotpass.php" target="_blank" alt="Password Recovery (new window)">Forgot Password</a></del>
			<a href="<?php echo $sitesettings['home_address']?>/ucp.php?mode=create" target="_blank" alt="Create an account (new window)">Create</a>
		</div>
	</form><!--id_modal_login_form-->
	<div id="id_modal_login_status" class="id_modal_message"></div>
</div><!--id_modal_login_content-->
</div><!--id_modal_login-->

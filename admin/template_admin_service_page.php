<?php
/**
 *  @file template_admin_serivce_page.php
 *  @brief service admin. intended for /admin/
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

require_once($ROOT.'/config/template_service_settings.php');
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Service Admin - <?php echo $sitesettings['title']?></title>
</head>
<body>
<script type="text/javascript">
var refreshtime = <?php echo $SERVICESETTINGS['log_rate'] ?>;
$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});
function tc()
{
	$.ajax({
		type: "POST",
		url: "<?php echo $sitesettings['address']?>/ajax/admin/admin_service_ajax.php?a=log",
		data: {'':''},
		success: function(rtndata)
		{
			//console.log(data); // show response from the php script.
			$('#log_view').html('');
			for (i = 0; i < rtndata.data.length; i++) {
				$('#log_view').append(rtndata.data[i]);
			} 
			if (document.getElementById('log_live').checked) { setTimeout(tc,refreshtime*1000); }
		},
		fail: function (rtndata) {
			$("#log_view").html("Connection Error");
		}
	});
}
$(function() {
	tc();
});
$(document).ready(function() {
	$('#btnupdate').click(function(){
		ui_actionBegin();
		$.get("<?php echo $sitesettings['address']?>/ajax/admin/admin_service_ajax.php?a=update", function(data, statusTxt){
			console.log(data);
		});
		ui_actionEnd();
	});
	$('#btnstart').click(function(){
		ui_actionBegin();
		$.get("<?php echo $sitesettings['address']?>/ajax/admin/admin_service_ajax.php?a=start", function(data, statusTxt){
			console.log(data);
		});
		ui_actionEnd();
	});
	$('#btnstop').click(function(){
		ui_actionBegin();
		$.get("<?php echo $sitesettings['address']?>/ajax/admin/admin_service_ajax.php?a=stop", function(data, statusTxt){
			console.log(data);
		});
		ui_actionEnd();
	});
	$('#btnsay').click(ui_btnsay);
	$('#log_live').change(function(){
		if (document.getElementById('log_live').checked)
		{
			tc();
		}
	});
	$("#txtsay").on('keyup', function (e) {
		if (e.keyCode == 13) {
			ui_btnsay();
		}
	});
});
function ui_btnsay()
{
	ui_actionBegin();
	$.post("<?php echo $sitesettings['address']?>/ajax/admin/admin_service_ajax.php?a=say",
	{
		msg: $("#txtsay").val()
	},
	function(data,status){
		if (status == "success")
		{
			//if msg doesnt say error, clear txtsay
			if (data.search("Sending command to world") != -1)
			{
				$("#txtsay").val("");
			}
		}
		//put data into a message
	});
	//clear textfield txtsay
	ui_actionEnd();
}
function ui_actionBegin()
{
	//disable buttons
	//document.getElementById("btnupdate").disabled = true;
	document.getElementById("btnstart").disabled = true;
	document.getElementById("btnstop").disabled = true;
	document.getElementById("txtsay").disabled = true;
	document.getElementById("btnsay").disabled = true;
	//show animation
	document.getElementById("loadingicon").style.visibility = 'visible';
}
function ui_actionEnd()
{
	//enable buttons
	//document.getElementById("btnupdate").disabled = false;
	document.getElementById("btnstart").disabled = false;
	document.getElementById("btnstop").disabled = false;
	document.getElementById("txtsay").disabled = false;
	document.getElementById("btnsay").disabled = false;
	//hide animation
	document.getElementById("loadingicon").style.visibility = 'hidden';
}
	//accordion========
	var acc = document.getElementsByClassName("accordion");
	var i;

	for (i = 0; i < acc.length; i++) {
		acc[i].onclick = function(){
			/* Toggle between adding and removing the "active" class,
			to highlight the button that controls the panel */
			this.classList.toggle("active");

			/* Toggle between hiding and showing the active panel */
			var panel = this.nextElementSibling;
			if (panel.style.display === "block") {
				panel.style.display = "none";
			} else {
				panel.style.display = "block";
			}
		}
	} 
</script>
<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong>Service Admin</strong></h1>
	<div id="mainwell" class="well">
	<?php if (id_account_getpermission($id_SESSION['id'],'service_admin')) { ?>
		<h1>Service Admin</h1>
		<ul>
			<li>Stop - </li>
			<li>Start - </li>
			<li>Update - </li>
			<li>Say - </li>
		</ul>
		<div>
			<span id="loadingicon" class="start-invisible glyphicon glyphicon-refresh spinning"></span>
			<div class="btn-group">
				<input type="button" class="btn btn-danger"  id="btnstop"   value="Stop">
				<input type="button" class="btn btn-warning" id="btnupdate" value="Update" DISABLED>
				<input type="button" class="btn btn-success" id="btnstart"  value="Start">
			</div>
			<div class="input-group">
				<input id="txtsay" type="text" class="form-control" placeholder="Say">
				<div class="input-group-btn">
					<button class="btn btn-default" id="btnsay">Say</button>
				</div>
			</div>
			<div id="server_status" style="font-weight: bold;"></div>
		</div>
		<div class="panel-group" id="log_accordion">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#log_accordion" href="#log_collapse">
						Log
					</a>
				</h4>
			</div>
			<div id="log_collapse" class="panel-collapse collapse in">
				<div class="panel-body">
					<div id="log_options">
						<span><label for="log_live">Live Log updates</label> <input type="checkbox" id="log_live" CHECKED></span>
						<span><label for="log_rate">Refresh Rate (sec)</label> <input type="number" id="log_rate" size="2" min="2" max="90" defaultvalue="<?= $SERVICESETTINGS['log_rate'] ?>" value="<?= $SERVICESETTINGS['log_rate'] ?>" READONLY DISABLED /></span>
						<span><label for="log_lines">Lines</label> <input type="number" id="log_lines" size="2" min="1" max="50" defaultValue="<?= $SERVICESETTINGS['log_lines'] ?>" value="<?= $SERVICESETTINGS['log_lines'] ?>" READONLY DISABLED /></span>
						<span>file size ~####kb</span>
					</div>
					<div id="log_view" class="logpre">
						Log...
					</div>
				</div><!--panel-body-->
			</div><!--log_collapse-->
		</div><!--log_accordion-->

<?php } else { ?>
	<p>You do not have permission here.</p>
<?php } ?>
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

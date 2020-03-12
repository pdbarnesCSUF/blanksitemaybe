<?php
use Tracy\Debugger;
require_once ('../vendor/tracy.php');
require_once('../include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "ID Admin - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "ID Admin Dashboard";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Admin - <?php echo $sitesettings['title']?></title>
        <script>$(function(){$("#sitenav-id_admin").addClass("active");});</script>
</head>
<body>
<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
<?php
if (id_account_getpermission($id_SESSION['id'],'id_admin'))
{
	$numusers = 0;
	$resultusers = $iddbPDO->query(   "SELECT	iduser_account.userid,username,profilename,datetime_created,active,status
									FROM iduser_account,iduser_profile
									WHERE iduser_account.userid=iduser_profile.userid
									ORDER BY userid;");
	if ($resultusers != false)
	{
		$numusers = $resultusers->rowCount();
	}
	//------
	$newestuser = id_account_getnewestuser();
	//------
	exec("git rev-parse --short HEAD",$output);
	$gitcommitshort = $output[0];
	$gitcommitlink = $sitesettings['gitcommitlinkbase'].$gitcommitshort;
	exec("git log --pretty=\"%s\" -1",$output);
	$gitcommitdesc = $output[1];
	exec("git symbolic-ref --short HEAD",$output);
	$gitbranchshort = $output[2];
	$gitbranchlink = $sitesettings['gitbranchlinkbase'].$gitbranchshort;
?>
        <h1><strong>Admin Page</strong></h1>
        <div id="mainwell" class="well">
		<h2>Dashboard</h2>
		<table>
			<tr><td>Sitestatus</td>	<td><?php echo $SITEstatus;?></td></tr>
			<tr><td>IDstatus</td>	<td><?php echo $IDstatus;?></td></tr>
			<tr><td>num users</td>	<td><?php echo $numusers;?></td></tr>
			<tr><td>newest member</td>	
				<td><a href="<?php echo $sitesettings['address']?>/admin/id_admin_user.php?userid=<?php echo $newestuser['userid'];?>"><?php echo $newestuser['username'];?></a> - <?php echo $newestuser['datetime_created'];?></td>
			</tr>
			<tr><td>some site options</td>	<td></td></tr>
			<tr><td>some id options</td>	<td></td></tr>
			<tr><td>GIT version</td>		<td><a href="<?php echo $gitcommitlink; ?>"><?php echo $gitcommitshort.' - '.$gitcommitdesc; ?></a></td></tr>
			<tr><td>GIT branch</td>		<td><a href="<?php echo $gitbranchlink; ?>"><?php echo $gitbranchshort; ?></a></td></tr>
		</table>
	</div><!--dashboard-->
	<div id="adminlog" class="well">
		<h2>Admin Log</h2>
		<table id="admintable" class="table table-condensed table-hover">
		<thead>
			<tr>
				<th>when</th>
				<th>who</th>
				<th>to who/what</th>
				<th>action</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Placeholder - in future release</td>
			</tr>
		</tbody>
		</table>
	</div>
	<div id="userlist" class="well">
		<h2>Users</h2>
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th>ID</th>
					<th>Username</th>
					<th>Profile Name</th>
					<th>Date Created</th>
				</tr>
			</thead>
				<?php
	if ($resultusers != false)
	{
		if ($numusers > 0)
		{
			echo "<tbody id=\"userlist_body\">".PHP_EOL;
			for ($i=0; $i < $numusers; $i++)
			{
				//get data
				$arrusers = $resultusers->fetch(PDO::FETCH_ASSOC); ///@todo fetchObject..or maybe even combined with a foreach loop to get rid of the $numrows var 
				echo "<tr>".PHP_EOL;
					
					echo "<td>".$arrusers['userid']."</td>".PHP_EOL;
					echo "<td><a href=\"".$sitesettings['address']."/admin/id_admin_user.php?userid=".$arrusers['userid']."\">".$arrusers['username']."</a></td>".PHP_EOL;
					echo "<td>".$arrusers['profilename']."</td>".PHP_EOL;
					echo "<td>".$arrusers['datetime_created']."</td>".PHP_EOL;
				echo "</tr>".PHP_EOL;
			}
			echo "</tbody>".PHP_EOL;
		}
		else
		{
			echo "<tr><td>No users?????</td><td></td><td></td><td></td></tr>".PHP_EOL;
		}
	}
	else
	{
		echo "<tr><td>DB error:VPL</td></tr>";
	}
	?>
		</table>
	</div><!--userlist-->
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

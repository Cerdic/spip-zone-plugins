<?php
/*
 * Created on Jul 16, 2009
 *
 * Owner: George
 */
session_start();
include('openinviter.php');
$oi=new OpenInviter();
$ers=array();
if (!$oi->settings['stats']) { echo 'Stats not enabled.';exit; }
$doLogin=false;
$hash=md5($oi->settings['stats_user'].$oi->settings['stats_password']);
if (empty($_SESSION['_oi_isLogged'])) $doLogin=true;
elseif($_SESSION['_oi_isLogged']!=$hash) $doLogin=true;
if ($doLogin)
	{
	if ($_SERVER['REQUEST_METHOD']=='POST')
		{
		if (empty($_POST['user_box'])) $ers['user']="User box is empty!";
		elseif($_POST['user_box']!=$oi->settings['stats_user']) $ers['user']="Invalid user/password!";
		if (empty($_POST['password_box'])) $ers['password']="Password box is empty!";
		elseif($_POST['password_box']!=$oi->settings['stats_password']) $ers['password']="Invalid user/password!";
		if (count($ers)==0) { $_SESSION['_oi_isLogged']=$hash;header("Location: stats.php");exit; }
		else echo ers($ers);
		}
	else { $_POST['user_box']=''; $_POST['password_box']=''; }
	echo showStyles();
	echo "<body><table align='center'><tr><td align='center'><form method='POST' action='?'>
		<table>
			<tr class='tableHeader'><td colspan='2' align='center'>Login</td></tr>
			<tr class='tableOddRow'><td><label>User:</label></td><td><input type='text' name='user_box' value='{$_POST['user_box']}'></td></tr>
			<tr class='tableEvenRow'><td><label>Password:</label></td><td><input type='password' name='password_box' value=''></td></tr>
			<tr class='tableFooter'><td colspan='2'><input type='submit' value='Login'></td></tr>
		</table>
	</form></td><tr><table>
	";
	exit;
	}	
echo showStyles();
if (isset($_GET['op']))
	if ($_GET['op']=='reset')
		{
		$oi->statsQuery("DELETE FROM oi_imports");
		$oi->statsQuery("DELETE FROM oi_messages");
		}

$plugins=$oi->getPlugins();
$import_stats=array();$messages_stats=array();
$res=$oi->statsQuery("SELECT COUNT(id) AS total_imports,SUM(contacts) AS total_contacts,service FROM oi_imports GROUP BY service ORDER BY total_imports DESC,total_contacts DESC");
while ($row=sqlite_fetch_array($res)) $import_stats[$row['service']]=$row;
$res=$oi->statsQuery("SELECT COUNT(id) AS total_sends,SUM(messages) AS total_messages,service FROM oi_messages GROUP BY service");
while ($row=sqlite_fetch_array($res)) $messages_stats[$row['service']]=$row;
echo "<center><h1 class='title'>OpenInviter Stats</h1></center>";
echo "<table class='table' align='center' cellspacing='0' cellpadding='0' width='500'>
	<tr class='tableHeader'><td colspan='5'>OpenInviter Statistics</td></tr>";
if (!empty($import_stats))
	{
	echo "<tr class='tableDesc'><td>Service</td><td>Users who imported contacts</td><td>Contacts imported</td><td>Users who sent messages</td><td>Messages sent</td></tr>";
	$total_imports=0;$total_contacts=0;$total_sends=0;$total_messages=0;$odd=true;
	foreach ($import_stats as $service=>$details)
		{
		$total_imports+=$details['total_imports'];
		$total_contacts+=$details['total_contacts'];
		echo "<tr class='".($odd?'tableOddRow':'tableEvenRow')."'><td><b>".(isset($plugins['email'][$service])?$plugins['email'][$service]['name']:(isset($plugins['social'][$service])?$plugins['social'][$service]['name']:$service))."</b></td><td align='center'>{$details['total_imports']}</td><td align='center'>{$details['total_contacts']}</td>";
		if (isset($messages_stats[$service]))
			{
			$total_sends+=$messages_stats[$service]['total_sends'];
			$total_messages+=$messages_stats[$service]['total_messages'];
			echo "<td align='center'>{$messages_stats[$service]['total_sends']}</td><td align='center'>{$messages_stats[$service]['total_messages']}</td>";
			}
		else echo "<td align='center'>-</td><td align='center'>-</td>";
		echo "</tr>";
		$odd=!$odd;
		}
	echo "<tr class='tableFooter'><td>Total</td><td>{$total_imports}</td><td>{$total_contacts}</td><td>{$total_sends}</td><td>{$total_messages}</td></tr>
	</table>
	<br><center><a href='?op=reset'>Reset statistics</a></center>";
	}
else
	echo "<tr class='tableOddRow'><td colspan='5' style='padding:25px;' align='center'>There are no statistics available yet</td></tr></table>";
echo "<br><center><a target='_blank' href='http://openinviter.com' title='Powered by OpenInviter.com'><img src='http://openinviter.com/images/banners/banner_blue_1.gif' alt='Powered by OpenInviter.com' style='border:none;'></a></center>";

function showStyles()
	{
	$contents="<style>
	.tableDesc{ color:#3d3d3d;font-family:Arial, Helvetica, sans-serif;font-weight:normal;font-size:12px;text-decoration:none; }
	.tableDesc td{ text-align:center; }
	.table{ border:1px solid #e1e1e1;padding:1px 1px 1px 1px;font-family:Arial, Helvetica, sans-serif;font-weight:normal;font-size:11px; }
	.table td { padding:5px; }
	.tableFooter{ height:30px;text-align:center;font-family:Arial, Helvetica, sans-serif;font-size:14px;color:#000000;font-weight:600;background-color:#C7DEE6; }
	.tableFooter td{ text-align:center; }
	.tableHeader{ background-color:#5fb52b;height:21px;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#FFFFFF;font-weight:bold;text-decoration:none;text-align:left;vertical-align:middle;padding-left:10px; }
	.tableHeader td{ text-align:center; }
	.tableOddRow{ background-color:#eaeaea;height:32px; }
	.tableEvenRow{ background-color:#f5f5f5;height:32px; }
	.title{ color:#89BDF6; } 
	body{ background-color:#F4F3EF; }
	a{ color:#FF7E00;font-family:Arial, Helvetica, sans-serif;font-weight:bold;font-size:14px; }
	</style>";
	return $contents;
	}

function ers($ers)
	{
	if (!empty($ers))
		{
		$contents="<table cellspacing='0' cellpadding='0' style='border:1px solid red;' align='center'><tr><td valign='middle' style='padding:3px' valign='middle'><img src='images/ers.gif'></td><td valign='middle' style='color:red;padding:5px;'>";
		foreach ($ers as $key=>$error)
			$contents.="{$error}<br >";
		$contents.="</td></tr></table><br >";
		return $contents;
		}
	}
?>
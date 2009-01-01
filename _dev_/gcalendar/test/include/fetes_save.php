<?php
include("config.pass.php");	
//paramètres de connexion
$tt=time();
$mois=date("m",$tt);
$jour=date("d",$tt);
$db = @mysql_pconnect($hostname, $username, $password);
@mysql_select_db($database, $db);
$query="select fete from gb_fetes where mois=$mois And jour=$jour";
$result = @mysql_query($query,$db);
if (@mysql_num_rows($result)!=0)
{
$row=@mysql_fetch_row($result);
print(" ".$row[0]."<br/>");
}
?>
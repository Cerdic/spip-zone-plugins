<?php
$coppermineDir="../../../coppermine/";
$coppermineConfigFile=$coppermineDir."include/config.inc.php";


if(file_exists($coppermineConfigFile))require_once $coppermineConfigFile;
else die("erreur");

$CONFIG['TABLE_PICTURES']   = $CONFIG['TABLE_PREFIX'].'pictures';
$CONFIG['TABLE_CONFIG']     = $CONFIG['TABLE_PREFIX'].'config';
$CONFIG['TABLE_ALBUMS']     = $CONFIG['TABLE_PREFIX'].'albums';

$CPG_DB_LINK_ID = @mysql_connect($CONFIG['dbserver'], $CONFIG['dbuser'], $CONFIG['dbpass']) or die("Erreur de connexion à la base Coppermine !<br /><br />Message MySQL : <b>" . mysql_error() . "</b>");
$db_selected = @mysql_select_db($CONFIG['dbname'], $CPG_DB_LINK_ID) or die ('Erreur de connexion à la base Coppermine : ' . mysql_error());


// On commence par recuperer les informations de configuration relatives aux vignettes
$query="SELECT * FROM ".$CONFIG['TABLE_CONFIG']." WHERE (name='fullpath') OR (name='thumb_pfx') OR (name='thumb_width')";
$results =  mysql_query($query);
while ($row = mysql_fetch_array($results)) {
    $CONFIG[$row['name']] = $row['value'];
} 

// On extrait un album public et non vide au hasard de la base
$query = "SELECT ".$CONFIG['TABLE_ALBUMS'].".aid,COUNT(pid) AS total FROM ".$CONFIG['TABLE_ALBUMS'].",".$CONFIG['TABLE_PICTURES'].
				 " WHERE ".$CONFIG['TABLE_ALBUMS'].".aid=".$CONFIG['TABLE_PICTURES'].".aid AND visibility='0'".
				 "GROUP BY ".$CONFIG['TABLE_ALBUMS'].".aid HAVING total>0".
				 " ORDER BY RAND() LIMIT 1";

$results =  mysql_query($query);
$row = mysql_fetch_array($results);
if ($row==NULL) return("");


// On extrait une image au hasard de l'album sélectionné
$query = "SELECT * FROM ".$CONFIG['TABLE_PICTURES']." WHERE aid='".$row['aid']."' ORDER BY RAND() LIMIT 1";
$results =  mysql_query($query);
$row = mysql_fetch_array($results);
if ($row==NULL) return("");

// Génération du code html d'affichage de la vignette
$data='<img src="/coppermine/'.$CONFIG['fullpath'].$row['filepath'].$CONFIG['thumb_pfx'].$row['filename'].'"/ >';
echo $data;
//$row->filepath . $CPG_THUMB . $row->filename . "\" border=\"0\" width=\"". $width. "\" height=\"" .$height. "\" alt=\"" . $row->filename . "\" />\n";

?>
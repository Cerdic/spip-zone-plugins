<?php 
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: config.php
 * 	Configuration file for the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;

$cheminEcrire = "../../../../../../../../config/";
if (defined("_ECRIRE_INC_VERSION")) return;
define("_ECRIRE_INC_VERSION", "1");
function spip_connect_db($host, $port, $login, $pass, $db) {
	global $fck_mysql_link;	// pour connexions multiples
	$fck_mysql_link = @mysql_connect($host, $login, $pass);
	mysql_select_db($db);
}
include ($cheminEcrire.'connect.php');


// SECURITY: You must explicitelly enable this "connector". (Set it to "true").
$Config['Enabled'] = true ;

// Path to user files relative to the document root.
// détermination du chemin de base par rapport à la racine du serveur
$dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
$i = 0;
while($dir_relatif_array[$i] != 'plugins') {
	$dir_relatif .= $dir_relatif_array[$i];
	$i++;
}
if($dir_relatif != '') $dir_relatif = "/".$dir_relatif;
$chemin_final = $dir_relatif."/IMG/";
$Config['UserFilesPath'] = $chemin_final;

// Fill the following value it you prefer to specify the absolute path for the
// user files directory. Usefull if you are using a virtual directory, symbolic
// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
// Attention: The above 'UserFilesPath' must point to the same directory.
$Config['UserFilesAbsolutePath'] = '' ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']	= array('php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

$Config['AllowedExtensions']['Media']	= array('swf','fla','jpg','gif','jpeg','png','avi','mpg','mpeg') ;
$Config['DeniedExtensions']['Media']	= array() ;

$Config['AllowedExtensions']['']	= array() ;
$Config['DeniedExtensions']['']	= array('php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi') ;

/*------------------------------------------------------------------------------*/
/* Directory and File Naming :-							*/
/*  -MaxDirNameLength	:: Maximum allowed length of a directory name		*/
/*  -DirNameAllowedChars :: Array of characters allowed in a directory name	*/
/*  -FileNameAllowedChars :: Array of characters allowed in a file name		*/
/*------------------------------------------------------------------------------*/

$Config['MaxDirNameLength']=25;

$Config['DirNameAllowedChars']=array();

	//Allow numbers
	for($i=48;$i<58;$i++) array_push($Config['DirNameAllowedChars'],chr($i));
	
	//Allow lowercase letters
	for($i=97;$i<123;$i++) array_push($Config['DirNameAllowedChars'],chr($i));
	
	//Allow uppercase letters
	for($i=65;$i<91;$i++) array_push($Config['DirNameAllowedChars'],chr($i));
	
	//Allow space,dash,underscore,dot
	array_push($Config['DirNameAllowedChars']," ","-","_",".");
	
$Config['FileNameAllowedChars']=$Config['DirNameAllowedChars'];
array_push($Config['FileNameAllowedChars'],')','(','[',']','~');

/*======================================================================================*/
/* Directory and File Listing								*/
/*--------------------------------------------------------------------------------------*/
/* 											*/
/*  -DirNameHidden	 :: Hidden directories						*/
/*  -FileNameHidden 	 :: Hidden files						*/
/*  											*/
/*  -FileNameUnrenamableInFolder  :: Répertoires dans lesquels les fichiers ne		*/
/*				     peuvent pas être renommés				*/
/*  -FileNameUnrenamable  :: Fichiers que l'on ne peut pas renommer			*/
/*  -FileNameUnrenamableWildcard  :: Fichiers que l'on ne peut pas renommer		*/
/*  				     avec joker, placer une étoile à la fin du nom	*/
/*  				     exemple : siteon*, tous les fichiers commencant par*/
/*  				     siteon, ne pourront pas être renommés		*/
/*  -DirNameUnrenamable  :: Répertoire 	que l'on ne peut pas renommer			*/
/*  -DirNameUnrenamableWildcard  :: Répertoire que l'on ne peut pas renommer		*/
/*  				     avec joker, placer une étoile à la fin du nom	*/
/*  											*/
/*  -FileNameUndeletableInFolder  :: Répertoires dans lesquels les fichiers ne		*/
/*				     peuvent pas être effacer				*/
/*  -FileNameUndeletable  :: Fichiers que l'on ne peut pas effacer			*/
/*  -FileNameUndeletableWildcard  :: Fichiers que l'on ne peut pas effacer		*/
/*  				     avec joker, placer une étoile à la fin du nom	*/
/*  				     exemple : icones*, tous les rep. commencant par	*/
/*  				     icones, ne pourront pas être effacer		*/
/*  -DirNameUndeletable  :: Répertoire 	que l'on ne peut pas effacer			*/
/*  -DirNameUndeletableWildcard  :: Répertoire que l'on ne peut pas effacer		*/
/*  				     avec joker, placer une étoile à la fin du nom	*/
/*--------------------------------------------------------------------------------------*/


$Config['DeleteOk'] = true;
$Config['RenameOk'] = true;

// Fichier cachés
$Config['DirNameHidden'] = array('');
$Config['FileNameHidden'] = array('');

// Fichiers que l'on ne peut pas renommer
$Config['FileNameUnrenamable'] = array('');
$Config['FileNameUnrenamableWildcard'] = array('siteon*','siteoff*','arton*','artoff*','rubon*','ruboff*','breveon*','breveoff*');
//$Config['FileNameUnrenamableInFolder'] = array('/gif/','/jpg/','/pdf/','/icones/','/icones_barre/');
//$Config['DirNameUnrenamable'] = array('File','Flash','Image','Media','pdf','jpg','gif');
$Config['FileNameUnrenamableInFolder'] = array ('/icones/','/icones_barre/');
$Config['DirNameUnrenamable'] = array ('File','Flash','Image','Media','icones','icones_barre');
//$typeDocs = spip_query_db("SELECT extension FROM spip_types_documents");
global $fck_mysql_link;
$typeDocs = mysql_query("SELECT extension FROM spip_types_documents", $fck_mysql_link);
while ($row = mysql_fetch_array($typeDocs)) {
	array_push ($Config['FileNameUnrenamableInFolder'], '/'.$row['extension'].'/');
	array_push ($Config['DirNameUnrenamable'], $row['extension']);
}
mysql_close($fck_mysql_link);

$Config['DirNameUnrenamableWildcard'] = array('');

// Fichiers que l'on ne peut pas effacer
$Config['FileNameUndeletable'] = array('');
$Config['FileNameUndeletableWildcard'] = $Config['FileNameUnrenamableWildcard'];
$Config['FileNameUndeletableInFolder'] = $Config['FileNameUnrenamableInFolder'];
$Config['DirNameUndeletable'] = $Config['DirNameUnrenamable'];
$Config['DirNameUndeletableWildcard'] = $Config['DirNameUnrenamableWildcard'];

/*==============================================================================*/

?>
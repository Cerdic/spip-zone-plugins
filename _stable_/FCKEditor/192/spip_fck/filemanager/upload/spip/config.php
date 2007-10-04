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
 * 	Configuration file for the PHP File Uploader.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;

// SECURITY: You must explicitelly enable this "uploader". 
$Config['Enabled'] = true ;

// détermination du chemin de base par rapport à la racine du serveur
  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\',/*'*/'/',realpath(dirname(__FILE__))));
  define('_DIR_RELATIF_PLUGIN_FCKEDITOR',str_replace('../','',(_DIR_PLUGINS.end($p))));
    
  $dir_relatif_array = split('/', $_SERVER["PHP_SELF"]);
  $i = 0;
  while($dir_relatif_array[$i] != 'plugins') 
    {
  	 $chemin_final .= $dir_relatif_array[$i]."/";
  	 $i++;
    }
    $chemin_final .="IMG/";
    
$Config['UserFilesPath'] = $chemin_final ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']	= array('php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

$Config['AllowedExtensions']['']	= array() ;
$Config['DeniedExtensions']['']		= array('php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi') ;

?>
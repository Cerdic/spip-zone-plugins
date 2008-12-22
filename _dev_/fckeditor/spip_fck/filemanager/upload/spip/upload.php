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
 * File Name: upload.php
 * 	This is the "File Uploader" for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

require('config.php') ;
require('util.php') ;

// This is the function that sends the results of the uploading process.
function SendResults( $errorNumber, $fileUrl = '', $fileName = '', $customMsg = '' )
{
	echo '<script type="text/javascript">' ;
	echo 'window.parent.OnUploadCompleted(' . $errorNumber . ',"' . str_replace( '"', '\\"', $fileUrl ) . '","' . str_replace( '"', '\\"', $fileName ) . '", "' . str_replace( '"', '\\"', $customMsg ) . '") ;' ;
	echo '</script>' ;
	exit ;
}

// Check if this uploader has been enabled.
if ( !$Config['Enabled'] )
	SendResults( '1', '', '', 'This file uploader is disabled. Please check the "editor/filemanager/upload/php/config.php" file' ) ;

// Check if the file has been correctly uploaded.
if ( !isset( $_FILES['NewFile'] ) || is_null( $_FILES['NewFile']['tmp_name'] ) || $_FILES['NewFile']['name'] == '' )
	SendResults( '202' ) ;

// Check for error
if(isset($_FILES['NewFile']['error']) && !empty($_FILES['NewFile']['error']))
{
	switch($_FILES['NewFile']['error'])
	{
		case 1:
			SendResults('1','','',"Upload error: " . $_FILES['NewFile']['name'] . " exceeds the the size set on the webserver");
			break;

		case 2:
			SendResults('1','','',"Upload error: " . $_FILES['NewFile']['name'] . " exceeds the the size set by the upload script");
			break;

		case 3:
			SendResults('1','','',"Upload error: The uploaded file was only partially uploaded");
			break;

		case 4:
			SendResults('1','','',"Upload error: No file was uploaded");
			break;

		case 6:
			SendResults('1','','',"Upload error: Missing a temporary folder");
			break;

		case 7:
			SendResults('1','','',"Upload error: Failed to write file to disk");
			break;
	}
}

// Get the posted file.
$oFile = $_FILES['NewFile'] ;

// Get the uploaded file name and extension.
$sFileName = $oFile['name'] ;
$sOriginalFileName = $sFileName ;
$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
$sExtension = strtolower( $sExtension ) ;

// The the file type (from the QueryString, by default 'File').
$sType = isset( $_GET['Type'] ) ? $_GET['Type'] : 'File' ;

// Check if it is an allowed type.
if ( !in_array( $sType, array('File','Image','Flash','Media') ) )
    SendResults( 1, '', '', 'Invalid type specified' ) ;

// Get the allowed and denied extensions arrays.
$arAllowed	= $Config['AllowedExtensions'][$sType] ;
$arDenied	= $Config['DeniedExtensions'][$sType] ;

// Check if it is an allowed extension.
if ( ( count($arAllowed) > 0 && !in_array( $sExtension, $arAllowed ) ) || ( count($arDenied) > 0 && in_array( $sExtension, $arDenied ) ) )
	SendResults( '202' ) ;

$sErrorNumber	= '0' ;
$sFileUrl		= '' ;

// Initializes the counter used to rename the file, if another one with the same name already exists.
$iCounter = 0 ;

// The the target directory.
if ( isset( $Config['UserFilesAbsolutePath'] ) )
	$sServerDir = $Config['UserFilesAbsolutePath'] ;
else 
	$sServerDir = GetRootPath() . $Config["UserFilesPath"] ;

while ( true )
{
	// Compose the file path.
	$sFilePath = $sServerDir . $sType . '/' . $sFileName ;

	// If a file with that name already exists.
	if ( is_file( $sFilePath ) )
	{
		$iCounter++ ;
		$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
		$sErrorNumber = '201' ;
	}
	else
	{
		move_uploaded_file( $oFile['tmp_name'], $sFilePath ) ;

		if ( is_file( $sFilePath ) )
		{
			$oldumask = umask(0) ;
			chmod( $sFilePath, 0777 ) ;
			umask( $oldumask ) ;
		}
		
		$sFileUrl = $Config["UserFilesPath"] . $sType . '/' . $sFileName ;

		break ;
	}
}

SendResults( $sErrorNumber, $sFileUrl, $sFileName ) ;
?>
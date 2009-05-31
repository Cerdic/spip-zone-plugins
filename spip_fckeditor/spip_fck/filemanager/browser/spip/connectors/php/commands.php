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
 * File Name: commands.php
 * 	This is the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

function GetFolders( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

	// Array that will hold the folders names.
	$aFolders	= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
	}

	closedir( $oCurrentFolder ) ;

	// Open the "Folders" node.
	echo "<Folders>" ;
	
	natcasesort( $aFolders ) ;
	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	// Close the "Folders" node.
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
	global $Config;
	
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

	// Arrays that will hold the folders and files names.
	$aFolders	= array() ;
	$aFiles		= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' && !in_array($sFile, $Config['DirNameHidden']) && !in_array($sFile, $Config['FileNameHidden']))
		{
			if ( is_dir( $sServerDir . $sFile ) ) {
				$rename = "1";
				if( in_array_wildcard($sFile, $Config['DirNameUnrenamable'], $Config['DirNameUnrenamableWildcard']) ) $rename = "0";
				$delete = "1";
				if( in_array_wildcard($sFile, $Config['DirNameUndeletable'], $Config['DirNameUndeletableWildcard']) ) $delete = "0";
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" rename="' . $rename . '" delete="' . $delete . '" />' ;
			}
			else
			{
				$iFileSize = filesize( $sServerDir . $sFile ) ;
				if ( $iFileSize > 0 )
				{
					$iFileSize = round( $iFileSize / 1024 ) ;
					if ( $iFileSize < 1 ) $iFileSize = 1 ;
				}

				$rename = "1";
				if( in_array($currentFolder, $Config['FileNameUnrenamableInFolder']) ) $rename = "0";
				elseif( in_array_wildcard($sFile, $Config['FileNameUnrenamable'], $Config['FileNameUnrenamableWildcard']) ) $rename = "0";
				$delete = "1";
				if( in_array($currentFolder, $Config['FileNameUndeletableInFolder']) ) $delete = "0";
				elseif( in_array_wildcard($sFile, $Config['FileNameUndeletable'], $Config['FileNameUndeletableWildcard']) ) $delete = "0";
				$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" rename="' . $rename . '" delete="' . $delete . '" />' ;
			}
		}
	}

	// Send the folders
	natcasesort( $aFolders ) ;
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	echo '</Folders>' ;

	// Send the files
	natcasesort( $aFiles ) ;
	echo '<Files>' ;

	foreach ( $aFiles as $sFiles )
		echo $sFiles ;

	echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
	$sErrorNumber	= '0' ;
	$sErrorMsg		= '' ;

	if ( isset( $_GET['NewFolderName'] ) )
	{
		$sNewFolderName = $_GET['NewFolderName'] ;

		if ( strpos( $sNewFolderName, '..' ) !== FALSE )
			$sErrorNumber = '102' ;		// Invalid folder name.
		else
		{
			// Map the virtual path to the local server path of the current folder.
			$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

			if ( is_writable( $sServerDir ) )
			{
				$sServerDir .= $sNewFolderName ;

				$sErrorMsg = CreateServerFolder( $sServerDir ) ;

				switch ( $sErrorMsg )
				{
					case '' :
						$sErrorNumber = '0' ;
						break ;
					case 'Invalid argument' :
					case 'No such file or directory' :
						$sErrorNumber = '102' ;		// Path too long.
						break ;
					default :
						$sErrorNumber = '110' ;
						break ;
				}
			}
			else
				$sErrorNumber = '103' ;
		}
	}
	else
		$sErrorNumber = '102' ;

	// Create the "Error" node.
	echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder )
{
	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
	{
		$oFile = $_FILES['NewFile'] ;

		// Map the virtual path to the local server path.
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

		// Get the uploaded file name.
		$sFileName = $oFile['name'] ;
		$sOriginalFileName = $sFileName ;
		$sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
		$sExtension = strtolower( $sExtension ) ;

		global $Config ;

		$arAllowed	= $Config['AllowedExtensions'][$resourceType] ;
		$arDenied	= $Config['DeniedExtensions'][$resourceType] ;

		if ( ( count($arAllowed) == 0 || in_array( $sExtension, $arAllowed ) ) && ( count($arDenied) == 0 || !in_array( $sExtension, $arDenied ) ) )
		{
			$iCounter = 0 ;

			while ( true )
			{
				$sFilePath = $sServerDir . $sFileName ;

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

					break ;
				}
			}
		}
		else
			$sErrorNumber = '202' ;
	}
	else
		$sErrorNumber = '202' ;

	echo '<script type="text/javascript">' ;
	echo 'window.parent.frames["frmUpload"].OnUploadCompleted(' . $sErrorNumber . ',"' . str_replace( '"', '\\"', $sFileName ) . '") ;' ;
	echo '</script>' ;

	exit ;
}

function DeleteFile( $resourceType, $currentFolder, $sFileName )
{
	global $Config ;
	$result1 = false;

	$delete = true;
	if( in_array($currentFolder, $Config['FileNameUndeletableInFolder']) ) $delete = false;
	elseif( in_array_wildcard($sFileName, $Config['FileNameUndeletable'], $Config['FileNameUndeletableWildcard']) ) $delete = false;
		
	if($delete && $Config['DeleteOk']) {
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;
		$sFilePath = $sServerDir . $sFileName;
		$result1 = @unlink( $sFilePath );
		
		if ($result1) $err_no = 0;
		else $err_no = 302;
	} else {
		$err_no = 302;
	}

	echo '<Error number="'.$err_no.'" />';
}

function RenameFile( $resourceType, $currentFolder, $sFileName, $nFileName )
{
	global $Config ;
	$result1 = false;
	
	$rename = true;
	if( in_array($currentFolder, $Config['FileNameUnrenamableInFolder']) ) $rename = false;
	elseif( in_array_wildcard($sFileName, $Config['FileNameUnrenamable'], $Config['FileNameUnrenamableWildcard']) ) $rename = false;

	if($rename && $Config['RenameOk']) {
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;
		$sNameChecked = '';
		for ($i=0; $i<strlen($nFileName); $i++) {
			if (in_array($nFileName[$i],$Config['FileNameAllowedChars'])) $sNameChecked.=$nFileName[$i];
		}
		$sNewName=str_replace( array("..","/"), "", $sNameChecked);
	
		if ($sNewName!='') {
			$nameValid = false;
			$lastdot = strrpos($sNewName,".");
	
			if ($lastdot!==false) {
				$ext = substr($sNewName,($lastdot+1));
				$fname = substr($sNewName,0,$lastdot);
				if (in_array(strtolower($ext),$Config['DeniedExtensions'][$resourceType])) {
					// ok c'est autorisé
					$nameValid = true;
				} elseif (!in_array(strtolower($ext),$Config['DeniedExtensions'][$resourceType])) {
					// pas dans la liste des autorisé, on check les interdits
					$nameValid = true;
				}
			}
	
			if ($nameValid) {			
				$result1 = @rename($sServerDir . $sFileName, $sServerDir . $sNewName);
			} else {
				$result1 = false;
			}
		}
		
		if ($result1) $err_no = 0;
		else $err_no = 502;
	} else {
		$err_no = 502;
	}
	
	echo '<Error number="'.$err_no.'" />';
}

function DeleteFolder( $resourceType, $currentFolder, $sFolderName )
{
	global $Config;
	$delete = true;
	if( in_array_wildcard($sFolderName, $Config['DirNameUndeletable'], $Config['DirNameUndeletableWildcard']) ) $delete = false;
	
	if($delete && $Config['DeleteOk']) {
		$result1 = false;
		$dir = $currentFolder.$sFolderName;
		$sServerDir = ServerMapFolder( $resourceType, $dir ) ;
		$result1 = removeDir($sServerDir);
	
		if ($result1) $err_no = 0;
		else $err_no = 402;
	} else {
		$err_no = 402;
	}

	echo '<Error number="'.$err_no.'" />';
}

function RenameFolder( $resourceType, $currentFolder, $sFolderName, $nFolderName )
{
	global $Config ;

	$rename = true;
	if( in_array_wildcard($sFolderName, $Config['DirNameUnrenamable'], $Config['DirNameUnrenamableWildcard']) ) $rename = false;

	if($rename && $Config['RenameOk']) {
		$result1 = false;
		//$dir = $currentFolder.$sFolderName;
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;
		$sNameChecked = '';
		for ($i=0; $i<strlen($nFolderName); $i++) {
			if (in_array($nFolderName[$i],$Config['DirNameAllowedChars'])) $sNameChecked.=$nFolderName[$i];
		}
		$sNewName=str_replace( array("..","/"), "", $sNameChecked);
		
		$result1 = @rename($sServerDir . $sFolderName, $sServerDir . $sNewName);
		
		if ($result1) $err_no = 0;
		else $err_no = 602;
	} else {
		$err_no = 602;
	}

	echo '<Error number="'.$err_no.'" />';

}
?>
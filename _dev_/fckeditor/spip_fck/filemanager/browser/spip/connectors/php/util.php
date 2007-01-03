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
 * File Name: util.php
 * 	This is the File Manager Connector for ASP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

function RemoveFromStart( $sourceString, $charToRemove )
{
	$sPattern = '|^' . $charToRemove . '+|' ;
	return preg_replace( $sPattern, '', $sourceString ) ;
}

function RemoveFromEnd( $sourceString, $charToRemove )
{
	$sPattern = '|' . $charToRemove . '+$|' ;
	return preg_replace( $sPattern, '', $sourceString ) ;
}

function ConvertToXmlAttribute( $value )
{
	return utf8_encode( htmlspecialchars( $value ) ) ;
}

function in_array_wildcard($search, $array, $wildcard) {
	if(in_array($search, $array)) {
		return true;
	} else {
		$found = false;
		foreach($wildcard as $item) {
			$item = substr($item, 0, strpos($item, '*'));
			if(strstr($search, $item)) {
				$found = true;
				break;
			}
			else $found = false;
		}
		return $found;
	}
}

function removeDir($dir) {
	$dh=@opendir($dir);
	if ($dh) {
		while ($entry=readdir($dh)) {
			if (($entry!=".")&&($entry!="..")) {
				if (is_dir($dir.'/'.$entry)) {
					removeDir($dir.'/'.$entry);	
				} else {
					if (!unlink($dir.'/'.$entry)) return false;
				}
			}
		}	
		closedir($dh);
		return rmdir($dir);
	} else {
		return false;
	}
}
?>
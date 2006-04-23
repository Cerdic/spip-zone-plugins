<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: updateLangFiles.php,v 1.4 2006/01/13 19:14:04 matthieu_ Exp $

error_reporting(E_ALL);

define('LANGS_PATH', '../../langs/');
define('ORIGINAL_FILE', 'en-utf-8.php');
define('ORIGINAL_FILE_PATH', LANGS_PATH . 'en-utf-8.php');
define('PATH_DESTINATION', LANGS_PATH . 'afterUpdate/');

function copyArrayInFile( $file, &$a)
{
	$contenu = '';
	foreach($a as $l) $contenu.=$l;
	file_put_contents($file, $contenu);
}

function getFileInArray( $file)
{
	$f = fopen( $file, 'r');
	while (!feof($f))
		$return[] = fgets($f, 4096);
	return $return;
}

function getLangFiles()
{
	$handle = opendir( LANGS_PATH );
	while ($file = readdir($handle)) 
	{
		print("<br>To analyse : ". $file);
		if (strpos($file, '-utf-8.php') && $file != ORIGINAL_FILE)
		{
			print(" YES ");
			$return[] = LANGS_PATH . $file;
		}
	}
	if(sizeof($return) == 0)
		print("No lang file detected.");
	return $return;
}

// Tests
//======
//var_dump( getFileInArray('./en-utf-8.php') );
// $a = array('toto' => 'titi', 'tata' => 1 );
// copyArrayInFile( 'test.php', $a );

function searchForStringInArray( $stringToSearch, $old)
{
	// go throught new from currentLine
	$stop = sizeof($old);
	//print("search for $stringToSearch...");
	for($i = 0;$i < $stop; $i++)
	{
		if(substr_count($old[$i], $stringToSearch) == 1)
		{
		//	print("found!<br>");
			return $old[$i];
		}
	}
	//print("not found!<br>");
	return false;
	// if ereg string
	// return whole line
	// else false
}

// opens original files and save in array
$english = getFileInArray( ORIGINAL_FILE_PATH );

$all = getLangFiles();

$all = array('../../langs/cz-utf-8.php'); //, '../../langs/si-utf-8.php');
foreach($all as $file)
{
	print($file."<br><br>");
	$fileDestination = PATH_DESTINATION. substr($file, strrpos( $file, '/') + 1) ;
	print("<br>".$fileDestination);

	$new = $english;
	$old = getFileInArray( $file );
	
	// algo
	// go througt $new file
	$currentLine = 0;
	foreach($new as $newLine)
	{
		$stringToSearch = '';
		if(ereg("] =", $newLine))
		{
			$stringToSearch = substr( $newLine, 0, strpos( $newLine, '='));
		//	print($stringToSearch . "<br>");
		}
		elseif(ereg("=>", $newLine))
		{
			$stringToSearch = substr( $newLine, 1, strpos( $newLine, '=>'));
		//	print($stringToSearch . "<br>");
		}	

		if( !empty($stringToSearch) 
				&& $oldLineWhichIsOk = searchForStringInArray( $stringToSearch, $old))
		{
			$new[$currentLine] = $oldLineWhichIsOk;
		}
		$currentLine++;
	}
	flush();
	// foreach '] =
	// search the string before = in the $old file
	// if found, copy the old string in the new one

	// TODO: Countries copy
	// saves new file in $fileDestination
	copyArrayInFile( $fileDestination, $new );
}

?>
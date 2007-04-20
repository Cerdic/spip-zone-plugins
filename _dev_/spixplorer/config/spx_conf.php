<?php
//------------------------------------------------------------------------------
// Configuration Variables
	
	// login to use QuiXplorer: (true/false)
	$GLOBALS['spx']["require_login"] = false;
	
	// language: (en, de, es, fr, nl, ru)
//	$GLOBALS['spx']["language"] = "en";
	
	// the filename of the QuiXplorer script: (you rarely need to change this)
	$GLOBALS['spx']["script_name"] =
		"http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	
	// allow Zip, Tar, TGz -> Only (experimental) Zip-support
	$GLOBALS['spx']["zip"] = false;	//function_exists("gzcompress");
	$GLOBALS['spx']["tar"] = false;
	$GLOBALS['spx']["tgz"] = false;
	
	// QuiXplorer version:
	$GLOBALS['spx']["version"] = "2.3";
//------------------------------------------------------------------------------
// Global User Variables (used when $require_login==false)
	
	// the home directory for the filemanager: (use '/', not '\' or '\\', no trailing '/')
	$GLOBALS['spx']["home_dir"] = ".";
	
	// the url corresponding with the home directory: (no trailing '/')
	$GLOBALS['spx']["home_url"] = ".";
	
	// show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
	$GLOBALS['spx']["show_hidden"] = true;
	
	// filenames not allowed to access: (uses PCRE regex syntax)
	$GLOBALS['spx']["no_access"] = "^\.ht";
	
	// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
	$GLOBALS['spx']["permissions"] = 7;
//------------------------------------------------------------------------------
/* NOTE:
	Users can be defined by using the Admin-section,
	or in the file ".config/.htusers.php".
	For more information about PCRE Regex Syntax,
	go to http://www.php.net/pcre.pattern.syntax
*/
//------------------------------------------------------------------------------
?>

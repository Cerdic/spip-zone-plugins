<?php

/*
 * @file
 *
 * @author 
 * @see funcsv2.php from phpBTTrackerMod
 */

// Even if you're missing PHP 4.3.0, the MHASH extension might be of use.
// Someone was kind enought to email this code snippit in.
if (function_exists('mhash') && (!function_exists('sha1')) && 
defined('MHASH_SHA1'))
{
	function sha1($str)
	{
		return bin2hex(mhash(MHASH_SHA1,$str));
	}
}


function hex2bin ($input, $assume_safe=true)
{
	if ($assume_safe !== true && ! ((strlen($input) % 2) === 0 || preg_match ('/^[0-9a-f]+$/i', $input)))
		return "";
	return pack('H*', $input );
}

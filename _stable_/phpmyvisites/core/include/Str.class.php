<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Str.class.php,v 1.4 2005/10/07 00:38:21 matthieu_ Exp $



/**
 * Class used to copy class Date method get() behaviour
 */
class Str
{
	/**
	 * @var string
	 */
	var $str;
	
	/**
	 * Constructor
	 */
	function Str($str)
	{
		$this->str = (string)$str;
	}
	
	/**
	 * Returns property value
	 */
	function get()
	{
		return $this->str;
	}
}
?>
<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: PmvConfig.class.php,v 1.13 2005/11/13 02:31:16 matthieu_ Exp $


/**
 * config class
 *
 * @author  Matthieu Aubry <matthieu@phpmyvisites.net>
 * @author  xavier Lembo <xav@elix-dev.com>
 * @since Thu Sep 01 21:07:21 CEST 2005
 * @version $Id: PmvConfig.class.php,v 1.13 2005/11/13 02:31:16 matthieu_ Exp $
 * @package phpmyvisites.v2
 *
 */

require_once INCLUDE_PATH."/core/include/commonDB.php";
		
class PmvConfig
{
	var $url; // config file
	var $content; // config file content array
	
	function PmvConfig()
	{
		$this->url = _PHPMV_DIR_CONFIG . "/config.php";
		$this->content = array();
		
		if(!@is_file($this->url))
        {
			//trigger_error("Unable to load base config file, can't continue...", E_USER_WARNING);
        }
		else
		{
			include $this->url;
			
			if (!is_array($config))
	        {
				trigger_error('Unattended config file format, please verify or delete your configuration file', E_USER_WARNING);
	        }
			else
			{
				$this->content = $config;
				$this->defineAsConstant( $this->content );
			}
		}
		
		$this->defineTables();
	}
    
	/**
     * Singleton
     */
    function &getInstance()
    {
        static $instance;
        if (!isset($instance)){
            $c = __CLASS__;
            $instance = new $c();
        }
		return $instance;
    }
	
	function defineTables()
	{
		if(defined('DB_TABLES_PREFIX'))
		{
			$db =& Db::getInstance();
			$tables = $db->getAllTablesList();
			
			foreach($tables as $name)
			{
				define('T_' . strtoupper($name), DB_TABLES_PREFIX . $name);
			}
		}
	}
	
	function defineAsConstant( $a_vars )
	{       
		foreach ($a_vars as $constName => $constValue) 
		{
			@define(strtoupper($constName), $constValue);
		}
	}
	
	function update( $a_varValue )
	{
		$this->content = array_merge( $this->content, $a_varValue );
	}
	
	function write()
	{
		saveConfigFile( $this->url, $this->content, "config");
	}
}
?>
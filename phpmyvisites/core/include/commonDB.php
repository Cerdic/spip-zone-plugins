<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: commonDB.php,v 1.47 2005/12/24 14:51:21 matthieu_ Exp $


// connnexion BDD

/*define('DB_HOST', 'localhost'); 
define('DB_LOGIN', 'root');
define('DB_PASSWORD', 'nintendo');
define('DB_NAME', 'v2');
*/
class Db
{
	var $host;
	var $login;
	var $password;
	var $name;
	var $state;
	var $allInstalled;
	var $all = array(
			'a_category',
			'a_config',
			'a_file',
			'a_keyword',
			'a_newsletter',
			'a_page',
			'a_partner_name',
			'a_partner_url',
			'a_provider',
			'a_resolution',
			'a_search_engine',
			'a_site',
			'a_vars_name',
			'a_vars_value',
			'archives',
			'category',
			'groups',
			'ip_ignore',
			'link_vp',
			'link_vpv',
			'newsletter',
			'page',
			'page_md5url',
			'page_url',
			'path',
			'query_log',
			'site',
			'site_partner',
			'site_partner_url',
			'site_url',
			'users',
			'users_link_groups',
			'vars',
			'version',
			'visit',
		);
		
	function Db()
	{
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
	
	function connect()
	{
		$this->host = DB_HOST;
		$this->login = DB_LOGIN;
		$this->password = DB_PASSWORD;
		$this->name = DB_NAME;
		$this->init();
	}
	
	function init()
	{
		$this->connection = @mysql_connect(
							$this->host,
							$this->login,
							$this->password
						);
	
		
		if(!$this->connection) 
		{ 
			$this->state = false;
			$GLOBALS['header_error_message_tpl'] = "'".$this->host ."' 
							database connection error! <br/>".mysql_error() ;
			
		} 
		else
		{
			
			// sélection BDD
			$selection = @mysql_select_db( $this->name, $this->connection);
			if(!$selection) 
			{ 
				$this->state = false;
				$GLOBALS['header_error_message_tpl'] = "'". $this->name ."' database selection error! <br/>".mysql_error() ; 
				
			}
			else
			{
				$this->state = true;
			}
		}
		return true;
	}
	
	function close()
	{
		mysql_close($this->connection);
	}
	
	function isReady()
	{
		return $this->state;
	}
	
	function getAllTablesList()
	{
		return $this->all;
	}
	
	function areAllTablesInstalled()
	{
		return sizeof($this->getAllInstalledTables()) === sizeof($this->getAllTablesList());
	}
	
	function getAllInstalledTables()
	{
		if( !defined('DB_TABLES_PREFIX') ) 
			return array();
			
		if(!isset($this->allInstalled) )
		{
			foreach($this->all as $name)
			{
				$prefixedTables[] = DB_TABLES_PREFIX . $name;
			}

			$r = query('SHOW TABLES');
			
			while($l = mysql_fetch_row($r))
			{
				$name = $l[0];
				
				if(in_array($name, $prefixedTables))
				{ 
					$this->allInstalled[] = $l[0];
				}
			}
		}
		return $this->allInstalled;
	}
	
	function eraseExistingTables()
	{
		foreach($this->allInstalled as $name)
		{
			$r = query("DROP TABLE `$name`");
		}
	}
	
	function createAllTables()
	{
		//print("create tables!");
		include INCLUDE_PATH . "/core/include/installSql.php";

		foreach($create as $sqlCode)
		{
			
			$r = query($sqlCode);
		}
		
		$this->updateVersion();
	}
	
	function updateVersion()
	{
		$r = query("UPDATE ".T_VERSION."
					SET `version` = '".PHPMV_VERSION."' 
					LIMIT 1");
	}
	
	function setVersion( $version )
	{
		$r = query("DELETE FROM ".T_VERSION);
		$r = query("INSERT INTO ".T_VERSION." (version)
					VALUES ('".$version."')");
	}
	
	function getVersion()
	{
		if(!isset($this->version))
		{
			$this->loadVersion();
		}
		return $this->version;
	}
	
	function loadVersion()
	{
		$r = query("SELECT version
					FROM ".T_VERSION);
		$rr = mysql_fetch_assoc($r);
		
		$return = $rr['version'];
		$this->version = empty($return)
								? '2.0beta1'
								: $return;
	}
}


/**
 * performs a query to the database and manage errors
 * 
 * @param string $query SQL Query
 * @param int $line line of the file where the query is called
 * 
 * @return resource mysql_query
 */
 // TODO3 jarter line !!
function query($query)
{
	$db =& Db::getInstance();
	if($db->isReady())
	{
		@$GLOBALS['query_log'][$query]++;
		if(!isset($GLOBALS['query_count']))
		{
			$GLOBALS['query_count'] = 0;
		}
		
		if(!isset($GLOBALS['total_time_query']))
		{
				$GLOBALS['total_time_query'] = 0;
		}
		$GLOBALS['query_count']++;
		
		$a_d = debug_backtrace();
		$beg = getMicrotime();
		if(PRINT_QUERY)
		{
			//printDebug($a_d);
			printDebug("<hr><i>line  : ".$a_d[0]['line']." in file ".$a_d[0]['file']."</i><br>".$query."<br>");
		}	
		
		$r = mysql_query($query);
		
		$end = getMicrotime()-$beg;
			$res = getMicrotime()-$GLOBALS['time_start'];
		$GLOBALS['total_time_query'] += $end;
		
		if(PRINT_QUERY)
		{
			if($end>TIME_SLOW_QUERY)
			{
				$GLOBALS['slow_queries'][] = $query."<br><b>$end s</b>";
			}
			printDebug("Total time : <b>$res</b> | Query Time <b>".substr($end, 0, 6)."</b> sec<br><br>");
		}
		
		if($r)
		{
			return $r;
		}
		else
		{		
			print("Error line  : ".$a_d[0]['line']." in file ".$a_d[0]['file']." : <b>".mysql_error()."</b>");
			print("<br><br>Query : ".$query);
			exit();
			return;
		}
	}
	return false;
}

// $arr =
// field name => value

// $idField = idfieldname
function updateLine($table, $a_fieldNamesValues, $idFieldName)
{
	$values = array();
	foreach($a_fieldNamesValues as $key => $val)
	{                
		if ((isset($key) && isset($val)) && (!empty($key) && !empty($val))) 
		{
			$values[] = "$key = '$val'";
		}
	}
	if ( !count($values) ) 
	{
		return false;
	}
	$query = "UPDATE $table 
				SET ".implode(", ", $values)." 
				WHERE $idFieldName = '".$a_fieldNamesValues[$idFieldName]."' 
				LIMIT 1";
	return query( $query );
}

function insertLine( $table, $a_fieldNames, $a_values )
{
	$r = query("INSERT INTO $table (".implode(", ", $a_fieldNames).")
				VALUES ('".implode("','", $a_values)."')
				");
	return mysql_insert_id();
}

?>
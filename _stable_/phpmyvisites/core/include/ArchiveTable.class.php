<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ArchiveTable.class.php,v 1.16 2006/01/04 22:32:46 matthieu_ Exp $



/**
 * Class that manages with database tables a_*
 * It records all datas, getId associated with Names, Name from Id, etc.
 */
class ArchiveTable
{
	/**
	 * @var string suffix of the table
	 */
	var $tableName; // suffix of a_* tables
	
	/**
	 * @var array contains NameToId relation
	 */
	var $nameToId;
	
	/**
	 * @var array contains idToName relation
	 */
	var $idToName;
	
	var $arrayAlreadyLoaded;
	/**
	 * Constructor
	 * 
	 * @param string $type suffix of the table
	 */
	function ArchiveTable($type)
	{
		$this->tableName = DB_TABLES_PREFIX.'a_'.$type;
		$this->nameToId = array();
		$this->idToName = array();
		$this->arrayAlreadyLoaded = array();
	}

	/**
	 * returns name associated to int id
	 * 
	 * @param int|array $id
	 * 
	 * @return string 
	 */
	function getName($id)
	{
		if($id == -1)
		{
			return "default";
		}
		if(!isset($this->idToName[$id]))
		{
			$this->loadName($id);
		}
		
		return stripslashes($this->idToName[$id]);
	}
	
	function loadName($id)
	{
		if(is_array($id))
		{
			foreach($id as $eachId)
			{
				$this->idToName[$eachId] = 'default';
			}
			sort($id);
			/*
			$md5i = md5(serialize($id));
			if(!isset($this->arrayAlreadyLoaded[$md5i]))
			{
				$this->arrayAlreadyLoaded[$md5i] = 1;
				*/
			$r = query("SELECT name, id " .
				" FROM ".$this->tableName.
				" WHERE id IN (".implode(',',$id).")"
				);
			//}
		}
		else
		{
			if(empty($id))
			{
				$this->idToName[$id] = 'default';
			}
			else
			{
				$r = query("SELECT name, id 
					 FROM ".$this->tableName."
					 WHERE id = $id");
			}
		}
		
		if(isset($r) && mysql_num_rows($r) != 0)
		{
			while($l = mysql_fetch_assoc($r))
			{
				$this->nameToId[$l['name']] = $l['id'];
				$this->idToName[$l['id']] = $l['name'];
			}
		}		
	}
	
	/**
	 * returns id associated to string name
	 * 
	 * @param string $name
	 * 
	 * @return id
	 */
	function getId($name)
	{
		if(empty($name))
		{
			return -1;
		}
		if(isset($this->nameToId[$name]))
		{
			return $this->nameToId[$name];
		}
		else
		{
			$nameToSave = databaseEscape($name);
			$r = query("SELECT id
						FROM ".$this->tableName."
						WHERE name = '$nameToSave'
						LIMIT 1");
			if(mysql_num_rows($r) == 0)
			{
				return $this->save($name);
			}
			else
			{
				$l = mysql_fetch_assoc($r);
				$this->nameToId[$name] = $l['id']; 
				$this->idToName[$l['id']] = $name; 
				//printDebug("$name is id ".$l['id']);
				return $this->getId($name);
			}
		}
	}

	/**
	 * Saves the name in the table
	 * Called by this->getId when asked id<->name doesn't exist yet
	 * 
	 * @param string $name
	 * 
	 * @return id of the row inserted
	 */
	function save($name)
	{
		$name = databaseEscape(stripslashes($name));
		$r = query("INSERT INTO ".$this->tableName." (name)
					VALUES ('$name')");
		$i = mysql_insert_id();
		//print("$i <br>");
		return $i;
	}
}
?>
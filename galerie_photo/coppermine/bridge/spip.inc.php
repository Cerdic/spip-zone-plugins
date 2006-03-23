<?php
/*************************
  Coppermine Photo Gallery
  ************************
  Copyright (c) 2003-2006 Coppermine Dev Team
  v1.1 originally written by Gregory DEMAR

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.
  ********************************************
  Coppermine version: 1.4.4
  $Source:  $
  $Revision: 0.1 $
  $Author: Philippe Drouot $
  $Date: 2006/02/20  $
**********************************************/

if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

// Switch that allows overriding the bridge manager with hard-coded values
define('USE_BRIDGEMGR', 1);

require_once 'bridge/udb_base.inc.php';

class cpg_udb extends core_udb {

	function cpg_udb()
	{
		global $BRIDGE;
		
		if (!USE_BRIDGEMGR) {

			$this->boardurl = 'http://localhost/';
			include('../ecrire/inc/utils.php');	
			include('../ecrire/inc/session.php');

		} else {			
			include($BRIDGE['relative_path_to_config_file'] . 'inc/utils.php');			
			include($BRIDGE['relative_path_to_config_file'] . 'inc/cookie.php');			
			include($BRIDGE['relative_path_to_config_file'] . 'inc/session.php');

			$this->boardurl =  $BRIDGE['full_forum_url_default'];
			$this->use_post_based_groups = 0; //$BRIDGE['use_post_based_groups'];
			
		}
		
		$this->spip_version = $spip_version;

    $this->multigroups = 1;

		$this->group_overrride = 1; //!$this->use_post_based_groups;

		// Retrieve Spip database connexion settings
		$fp = @fopen($BRIDGE['relative_path_to_config_file']."inc_connect.php", 'r' ) or die("Can't find spip database connexion settings, please verify spip relative path and inc_connect.php spip's file.");
		$cont = "";    
		 while( !feof( $fp ) ) {
		     $buf = trim(fgets( $fp, 4096 )) ;
		     $cont .= $buf;
		 }		
		ereg("spip_connect_db\((.*)\)",$cont,$regs);
		$spipConfig_db=explode(",",str_replace("'","",$regs[1]));

		// Database connection settings
		$this->db = array(
			'name' => $spipConfig_db[4],
			'host' => $spipConfig_db[0],
			'user' => $spipConfig_db[2],
			'password' => $spipConfig_db[3],
			'prefix' => ''
		);
		
		// Board table names
		$this->table = array(
			'users' => 'spip_auteurs',
			'groups' => 'spip_auteurs',
			'usergroups' => 'spip_auteurs'
		);

		// Derived full table names
		$this->usertable = '`' . $this->db['name'] . '`.' . $this->db['prefix'] . $this->table['users'];
		$this->groupstable =  '`' . $this->db['name'] . '`.' . $this->db['prefix'] . $this->table['groups'];
		$this->usergroupstable = '`' . $this->db['name'] . '`.' . $this->db['prefix'] . $this->table['usergroups'];
		
		// Table field names
		$this->field = array(
			'username' => 'login', // name of 'username' field in users table
			'user_id' => 'id_auteur', // name of 'id' field in users table
			'password' => 'login', // name of 'password' field in users table
			'email' => 'email', // name of 'email' field in users table
			'regdate' => "''", // name of 'registered' field in users table
			'lastvisit' => 'en_ligne', // last time user logged in
			'active' => "''", // is user account active?
			'location' => "''", // name of 'location' field in users table
			'website' => 'url_site', // name of 'website' field in users table
			'usertbl_group_id' => 'statut', // name of 'group id' field in users table
			'grouptbl_group_id' => 'statut', // name of 'group id' field in groups table
			'grouptbl_group_name' => "''" // name of 'group name' field in groups table
		);
		
		// Pages to redirect to
		$this->page = array(
			'register' => '/spip.php?action=inscription&focus=nom_inscription&mode=redac',
			'editusers' => '/ecrire/?exec=auteurs',
			'edituserprofile' => '/ecrire/?exec=auteur_infos&id_auteur='
		);

		// Group ids - admin and guest only.
		$this->admingroups = array(1);
		$this->guestgroup = 3;
		
		// Connect to db
		$this->connect();
		
	}


	// definition of how to extract an id and password hash from a cookie
	function cookie_extraction()
	{
	   return false;
	    
	}



	// definition of actions required to convert a password from user database form to cookie form
	function udb_hash_db($password)
	{
		return $password; // unused
	}
	
	function login_page()
	{
		$url=urlencode("/coppermine/".$_GET['referer']);
		$this->redirect("/spip.php?page=login&url=$url");
	}

	function logout_page()
	{
		global $USER_DATA;
		
        $this->redirect("/spip.php?action=cookie&logout=". $USER_DATA['user_name']);
	}

	function get_groups($row)
	{
		$group_id="";
		if ($row['group_id']=='0minirezo') $group_id=array(1); //'Administrators';
		if ($row['group_id']=='1comite') $group_id=array(2); //'Registered';
		
		
		return $group_id;
	}	
	
	function session_extraction()	{
		global $BRIDGE;

		if ($id_session = $_COOKIE['spip_session']) {
			
			// First, we extract 'alea_ephemere' from spip database
			$sql = "SELECT valeur FROM spip_meta WHERE nom='alea_ephemere'";
			$result = cpg_db_query($sql,$this->link_id) or die("Bridge coppermine-spip : problème de connexion à la base spip");
			$row= mysql_fetch_assoc($result) or die("Bridge coppermine-spip : problème de connexion à la base spip");			
			$alea=$row['valeur'];			
			
			// Include session file			
			if (ereg("^([0-9]+_)", $id_session, $regs)) $id_auteur = $regs[1];
			$fichier_session=$BRIDGE['relative_path_to_config_file']. 'data/' . 'session_'.$id_auteur.md5($id_session.' '.$alea). '.php';				
			if (@file_exists($fichier_session)) include($fichier_session);
			else return(FALSE);			
			return array($GLOBALS['auteur_session']['id_auteur'], $GLOBALS['auteur_session']['login']);
									
		}
		
	}

	function view_users() {}
	function view_profile() {}
	
	function get_users($options = array()) {
    	global $CONFIG;
		
		// Copy UDB fields and config variables (just to make it easier to read)
    $f =& $this->field;
		$C =& $CONFIG;
		
		// Sort codes - global this in usermgr.php in 1.5
    $sort_codes = array('name_a' => 'user_name ASC',
                        'name_d' => 'user_name DESC',
                        'group_a' => 'group_name ASC',
                        'group_d' => 'group_name DESC',
                        'reg_a' => 'user_regdate ASC',
                        'reg_d' => 'user_regdate DESC',
                        'pic_a' => 'pic_count ASC',
                        'pic_d' => 'pic_count DESC',
                        'disku_a' => 'disk_usage ASC',
                        'disku_d' => 'disk_usage DESC',
                        'lv_a' => 'user_lastvisit ASC',
                        'lv_d' => 'user_lastvisit DESC',
                       );
        
		$sql = "SELECT group_id, group_name, group_quota FROM {$C['TABLE_USERGROUPS']}";
		$result = cpg_db_query($sql);		
		$groups = $quotas = array();
	
		while ($row = mysql_fetch_assoc($result)) {
			$groups[$row['group_id']] = $row['group_name'];
			$quotas[$row['group_id']]= $row['group_quota'];
		}
		
		if (in_array($options['sort'], array('group_a', 'group_d', 'pic_a', 'pic_d', 'disku_a', 'disku_d'))){
			
			$sort = '';
			list($this->sortfield, $this->sortdir) = explode(' ', $sort_codes[$options['sort']]);
			$this->adv_sort = true;
			
		} else {
			
			$sort = "ORDER BY " . $sort_codes[$options['sort']];
			$this->adv_sort = false;
		}

		// Build WHERE clause, if this is a username search
        if ($options['search']) {
            $options['search'] = 'AND u.'.$f['username'].' LIKE "'.$options['search'].'" ';
        }
		
		// Main array to hold our user data
		$userlist = array();
		
		// These sorting methods need the cpg pics table, do that first
		if (in_array($options['sort'], array('pic_a', 'pic_d', 'disku_a', 'disku_d'))){
			
			$sql = "SELECT owner_id, COUNT(pid) as pic_count, ROUND(SUM(total_filesize)/1024) as disk_usage FROM {$C['TABLE_PICTURES']} WHERE owner_id <> 0 GROUP BY owner_id ORDER BY {$sort_codes[$options['sort']]} LIMIT {$options['lower_limit']}, {$options['users_per_page']}";
			
			$result = cpg_db_query($sql);
			
			// If no records, return empty value
			if (!mysql_num_rows($result)) {
				return array();
			}
		 
			while ($row = mysql_fetch_assoc($result)) $userlist[$row['owner_id']] = $row;
			mysql_free_result($result);

			$user_list_string = implode(', ', array_keys($userlist));

			$sql = "SELECT u.{$f['user_id']} as user_id,  {$f['usertbl_group_id']} as statut, {$f['username']} as user_name, {$f['email']} as user_email, {$f['regdate']} as user_regdate, {$f['lastvisit']} as user_lastvisit ".
               "FROM {$this->usertable} AS u ".
               "WHERE u.{$f['user_id']} IN ($user_list_string) GROUP BY u.{$f['user_id']}";
		
			$result = cpg_db_query($sql, $this->link_id);
		
			// If no records, return empty value
			if (!mysql_num_rows($result)) {
				return array();
			}
		
			while ($row = mysql_fetch_assoc($result)) $userlist[$row['user_id']] = array_merge($userlist[$row['user_id']], $row);
			mysql_free_result($result);

		} else {
		
			$sql = "SELECT u.{$f['user_id']} as user_id,  {$f['usertbl_group_id']} as statut, {$f['username']} as user_name, {$f['email']} as user_email, {$f['regdate']} as user_regdate, {$f['lastvisit']} as user_lastvisit, 0 as pic_count ".
               "FROM {$this->usertable} AS u ".
               "WHERE u.{$f['user_id']} > 0 " . $options['search'].
               "GROUP BY u.{$f['user_id']} " . $sort . 
			   " LIMIT {$options['lower_limit']}, {$options['users_per_page']}";
		 
			$result = cpg_db_query($sql, $this->link_id);
		
			// If no records, return empty value
			if (!mysql_num_rows($result)) {
				return array();
			}
		
			while ($row = mysql_fetch_assoc($result)) $userlist[$row['user_id']] = $row;
			mysql_free_result($result);
			
			$user_list_string = implode(', ', array_keys($userlist));
		
			$sql = "SELECT owner_id, COUNT(pid) as pic_count, ROUND(SUM(total_filesize)/1024) as disk_usage FROM {$C['TABLE_PICTURES']} WHERE owner_id IN ($user_list_string) GROUP BY owner_id";

			$result = cpg_db_query($sql);

			while ($owner = mysql_fetch_assoc($result)) $userlist[$owner['owner_id']] = array_merge($userlist[$owner['owner_id']], $owner);
		}
		
		foreach ($userlist as $uid => $user){
			
			if ($userlist[$uid]['statut']=='0minirezo') {
				$userlist[$uid]['group_name']='Administrators';
				$userlist[$uid]['group_quota'] = $quotas[1];
			}
			if ($userlist[$uid]['statut']=='1comite') {
				$userlist[$uid]['group_name']='Registered';
				$userlist[$uid]['group_quota'] = $quotas[2];
			}
				
		}
		
		foreach ($userlist as $uid => $user) if (!isset($user['user_name'])) unset($userlist[$uid]);

		if ($this->adv_sort) usort($userlist, array('core_udb', 'adv_sort'));

        return $userlist;
    }


}

// and go !
$cpg_udb = new cpg_udb;
?>

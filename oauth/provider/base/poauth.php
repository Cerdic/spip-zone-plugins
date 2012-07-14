<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function poauth_declarer_tables_principales($tables_principales){

    # Table holding consumer key/secret combos an user issued to consumers. 
    $osr_field = array(
	    'osr_id' => "INT(11) NOT NULL AUTO_INCREMENT",
	    'id_auteur' => "BIGINT(21)",
	    'osr_consumer_key' => "VARCHAR(255) binary not null",
	    'osr_consumer_secret' => "VARCHAR(255) binary not null",
	    'osr_enabled' => "tinyint(1) not null default '1'",
	    'osr_status' => "VARCHAR(255) binary not null",
	    'osr_requester_name' => "VARCHAR(255) binary not null",
	    'osr_requester_email' => "VARCHAR(255) binary not null",
	    'osr_callback_uri' => "VARCHAR(255) binary not null",
	    'osr_application_uri' => "VARCHAR(255) binary not null",
	    'osr_application_title' => "VARCHAR(255) not null",
	    'osr_application_descr' => "TEXT not null",
	    'osr_application_notes' => "TEXT not null",	
	    'osr_application_type' => "VARCHAR(255) binary not null",
	    'osr_application_commercial' => "tinyint(1) not null default '0'",
	    'osr_issue_date' => "DATETIME not null",
	    'osr_timestamp' => "TIMESTAMP not null default current_TIMESTAMP"
    );

    $osr_key = array(
        'PRIMARY KEY osr_id' => "osr_id",
        'UNIQUE KEY osr_consummer_key' =>"osr_consumer_key",
        'KEY id_auteur' => "id_auteur"    
    );
    
    $osr_join = array(
    );

    $tables_principales['spip_oauth_server_registry'] =  array(
        'field' => &$osr_field, 
        'key' => &$osr_key, 
        'join'=>&$osr_join
    );
    
    
    # Nonce used by a certain consumer, every used nonce should be unique, this prevents
    # replaying attacks.  We need to store all TIMESTAMP/nonce combinations for the
    # maximum TIMESTAMP received.
    $osn_field = array(
        "osn_id" => "INT(11) not null auto_increment",
        "osn_consumer_key" => "VARCHAR(64) binary not null",
        "osn_token" =>"VARCHAR(64) binary not null",
        "osn_timestamp" => "TIMESTAMP not null",
        "osn_nonce" => "VARCHAR(80) binary not null"    
    );
    
    $osn_key = array(
        "PRIMARY KEY" => "osn_id",
        "UNIQUE KEY" => "osn_consumer_key, osn_token, osn_timestamp, osn_nonce"        
    );
    
    $osn_join = array(
    );

    $tables_principales['spip_oauth_server_nonce'] =  array(
        'field' => &$osn_field, 
        'key' => &$osn_key, 
        'join'=>&$osn_join
    );

    # Table used to sign requests sent to a server by the consumer
    # The key is defined for a particular user, when the user id is null then this
    # is the default authentication for communication with the remote server
    # only one type of token per user/server combo
    $ost_field = array(
        "ost_id" => "int(11) not null auto_increment",
        "ost_osr_id_ref" =>"int(11) not null",
        "id_auteur" =>"BIGINT(11) not null",
        "ost_token" => "VARCHAR(64) binary not null",
        "ost_token_secret" => "VARCHAR(64) binary not null",
        "ost_token_type" => "enum('request','access')",
        "ost_authorized" => "tinyint(1) not null default '0'",
	    "ost_referrer_host" => "VARCHAR(128) not null",
        "ost_timestamp" => "TIMESTAMP not null default CURRENT_TIMESTAMP"
    );
    
    $ost_key = array(
        "PRIMARY KEY" => "ost_id",
        "UNIQUE KEY" => "ost_token",
        "KEY" => "ost_osr_id_ref",
        "KEY" => "ost_osr_id_ref"
    );
    
    $ost_join = array(
    );

    $tables_principales['spip_oauth_server_token'] =  array(
        'field' => &$ost_field, 
        'key' => &$ost_key, 
        'join'=>&$ost_join
    );



		
	return $tables_principales;
}


function poauth_declarer_tables_interfaces($interface) {
    #Nommer correctement le noms des tables pour les boucles
    $interface['table_des_tables']['oauth_server_registry']='oauth_server_registry';
    $interface['table_des_tables']['oauth_server_nonce']='oauth_server_nonce';
    $interface['table_des_tables']['oauth_server_token']='oauth_server_token';
    #lier les tables
    $interface['tables_jointures']['auteurs'][] = 'oauth_server_registry';
    $interface['tables_jointures']['auteurs'][] = 'oauth_server_token';
    return $interface;
}
?>

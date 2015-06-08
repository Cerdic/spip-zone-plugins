<?php
function clevermail_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['cm_lists']='cm_lists';
	$interface['table_des_tables']['cm_lists_subscribers']='cm_lists_subscribers';
	$interface['table_des_tables']['cm_pending']='cm_pending';
	$interface['table_des_tables']['cm_posts']='cm_posts';
	$interface['table_des_tables']['cm_posts_done']='cm_posts_done';
	$interface['table_des_tables']['cm_posts_links']='cm_posts_links';
	$interface['table_des_tables']['cm_posts_queued']='cm_posts_queued';
	$interface['table_des_tables']['cm_settings']='cm_settings';
	$interface['table_des_tables']['cm_subscribers']='cm_subscribers';
	return $interface;
}

function clevermail_declarer_tables_objets_sql($tables) {
	// spip_cm_lists
	$tables["spip_cm_lists"]=array(
	
		'principale' => "oui",
		'field'=> array(
			"lst_id" => "INT(11) NOT NULL auto_increment",
			"lst_name" => "VARCHAR(255) NOT NULL",
			"lst_comment" => "TEXT NOT NULL",
			"lst_moderation" => "VARCHAR(10) NOT NULL",
			"lst_moderator_email" => "VARCHAR(255) NOT NULL",
			"lst_subscribe_subject" => "VARCHAR(255) NOT NULL",
			"lst_subscribe_text" => "TEXT NOT NULL",
			//"lst_subject" => "VARCHAR(255) NOT NULL",
			"lst_unsubscribe_subject" => "VARCHAR(255) NOT NULL",
			"lst_unsubscribe_text" => "TEXT NOT NULL",
			"lst_subject_tag" => "TINYINT(1) NOT NULL default '1'",
			"lst_url_html" => "VARCHAR(255) NOT NULL",
			"lst_url_text" => "VARCHAR(255) NOT NULL",
			"lst_auto_mode" => "ENUM('none', 'day', 'week', 'month') DEFAULT 'none'",
			"lst_auto_hour" => "TINYINT(2) NOT NULL default '8'",
			"lst_auto_week_day" => "TINYINT(1) NOT NULL default '1'", // 0 = dimanche
			"lst_auto_week_days" => "VARCHAR(13) NOT NULL default '1'", // concatenation numeros des jours, 0 = dimanche
			"lst_auto_month_day" => "TINYINT(2) NOT NULL default '1'",
			"lst_auto_subscribers" => "VARCHAR(255) NOT NULL",
			"lst_auto_subscribers_mode" => "TINYINT(1) NOT NULL default '1'",
			"lst_auto_subscribers_updated" => "int(11) NOT NULL default '0'"
		),
		'key' => array(
			"PRIMARY KEY" => "lst_id"
		),
		'titre' => "lst_name AS titre, '' AS lang",
	);

// spip_cm_lists_subscribers
	$tables["spip_cm_lists_subscribers"]=array(
		'principale' => "non",
		'field'=> array(	  "lst_id" => "INT(11) NOT NULL",
			  "sub_id" => "BIGINT(20) NOT NULL",
			  "lsr_mode" => "TINYINT(1) NOT NULL",
			  "lsr_id" => "VARCHAR(32) NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "lst_id, sub_id",
			"KEY lst_id" => "lst_id")
		);
	
	// spip_cm_pending
	$tables["spip_cm_pending"]=array(
		'principale' => "oui",
		'field'=> array(
			  "lst_id" => "int(11) NOT NULL",
			  "sub_id" => "bigint(20) NOT NULL",
			  "pnd_action" => "varchar(15) NOT NULL",
			  "pnd_mode" => "tinyint(1) NOT NULL",
			  "pnd_action_date" => "int(11) NOT NULL",
			  "pnd_action_id" => "varchar(32) NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "lst_id, sub_id")
		);

	// spip_cm_posts
	$tables["spip_cm_posts"]=array(
		'principale' => "oui",
		'field'=> array(
			  "pst_id" => "bigint(20) NOT NULL auto_increment",
			  "lst_id" => "int(11) NOT NULL",
			  "pst_date_create" => "int(11) NOT NULL",
			  "pst_date_update" => "int(11) DEFAULT 0 NOT NULL",
			  "pst_date_sent" => "int(11) DEFAULT 0 NOT NULL",
			  "pst_subject" => "varchar(255) DEFAULT '' NOT NULL",
			  "pst_html" => "longtext DEFAULT '' NOT NULL",
			  "pst_text" => "longtext DEFAULT '' NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "pst_id"),
		'url_voir'=>'clevermail_post_edit'
		);

	// spip_cm_posts_done
	$tables["spip_cm_posts_done"]=array(
		'principale' => "oui",
		'field'=> array(
			  "pst_id" => "bigint(20) NOT NULL",
			  "sub_id" => "bigint(20) NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "pst_id, sub_id")
		);





	// spip_cm_posts_links

	$tables["spip_cm_posts_links"]=array(
		'principale' => "oui",
		'field'=> array(
			  "lnk_id" => "bigint(20) NOT NULL",
			  "pst_id" => "bigint(20) NOT NULL",
			  "lnk_name" => "varchar(255) NOT NULL",
			  "lnk_url" => "text NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "lnk_id")
		);

// spip_cm_posts_queued
	$tables["spip_cm_posts_queued"]=array(
		'principale' => "oui",
		'field'=> array(
		  "pst_id" => "bigint(20) NOT NULL",
		  "sub_id" => "bigint(20) NOT NULL",
		  "psq_date" => "int(11) NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "pst_id,sub_id")
		);



	// spip_cm_settings
	
	$tables["spip_cm_settings"]=array(
		'principale' => "oui",
		'field'=> array(
		  "set_name" => "varchar(15) NOT NULL",
		  "set_value" => "varchar(255) NOT NULL"
			),
		'key' => array(
			//"PRIMARY KEY" => "set_name"
			)
		);


	// spip_cm_subscribers
	
	$tables["spip_cm_subscribers"]=array(
		'principale' => "oui",
		'field'=> array(
			  "sub_id" => "bigint(20) NOT NULL auto_increment",
			  "sub_email" => "varchar(255) NOT NULL",
			  "sub_profile" => "varchar(32) DEFAULT '' NOT NULL"
			),
		'key' => array(
			"PRIMARY KEY" => "sub_id",
			  "KEY sub_profile" => "sub_profile",
			  "KEY sub_email" => "sub_email")
		);

return $tables;
}

?>

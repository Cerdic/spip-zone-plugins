<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Interfaces de la table forum pour le compilateur
 *
 * @param array $interfaces
 * @return array
 */
function forum_declarer_tables_interfaces($interfaces){
	
	$interfaces['table_des_tables']['forums']='forum';
	
	$interfaces['exceptions_des_tables']['forums']['date']='date_heure';
	$interfaces['exceptions_des_tables']['forums']['nom']='auteur';
	$interfaces['exceptions_des_tables']['forums']['email']='email_auteur';
	
	$interfaces['table_titre']['forums']= "titre, '' AS lang";
	
	$interfaces['table_date']['forums']='date_heure';
	
	$interfaces['tables_jointures']['spip_forum'][]= 'mots_forum';
	$interfaces['tables_jointures']['spip_forum'][]= 'mots';
	$interfaces['tables_jointures']['spip_forum'][]= 'documents_liens';
	
	$interfaces['tables_jointures']['spip_mots'][]= 'mots_forum';
	
	$interfaces['table_des_traitements']['PARAMETRES_FORUM'][]= 'htmlspecialchars(%s)';
	$interfaces['table_des_traitements']['TEXTE']['forums']= "safehtml("._TRAITEMENT_RACCOURCIS.")";
	$interfaces['table_des_traitements']['TITRE']['forums']= "safehtml("._TRAITEMENT_TYPO.")";
	$interfaces['table_des_traitements']['NOTES']['forums']= "safehtml("._TRAITEMENT_RACCOURCIS.")";
	$interfaces['table_des_traitements']['NOM_SITE']['forums']=  "safehtml("._TRAITEMENT_TYPO.")";
	$interfaces['table_des_traitements']['URL_SITE']['forums']= 'safehtml(vider_url(%s))';
	$interfaces['table_des_traitements']['AUTEUR']['forums']= 'safehtml(vider_url(%s))';
	$interfaces['table_des_traitements']['EMAIL_AUTEUR']['forums']= 'safehtml(vider_url(%s))';
	
	return $interfaces;
}

/**
 * Table principale spip_forum
 *
 * @param array $tables_principales
 * @return array
 */
function forum_declarer_tables_principales($tables_principales){
	
	$spip_forum = array(
			"id_forum"	=> "bigint(21) NOT NULL",
			"id_parent"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_thread"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_article"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_breve"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"date_heure"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_thread"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"texte"	=> "mediumtext DEFAULT '' NOT NULL",
			"auteur"	=> "text DEFAULT '' NOT NULL",
			"email_auteur"	=> "text DEFAULT '' NOT NULL",
			"nom_site"	=> "text DEFAULT '' NOT NULL",
			"url_site"	=> "text DEFAULT '' NOT NULL",
			"statut"	=> "varchar(8) DEFAULT '0' NOT NULL",
			"ip"	=> "varchar(16) DEFAULT '' NOT NULL",
			"maj"	=> "TIMESTAMP",
			"id_auteur"	=> "bigint DEFAULT '0' NOT NULL",
			"id_message"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_syndic"	=> "bigint(21) DEFAULT '0' NOT NULL");
	
	$spip_forum_key = array(
			"PRIMARY KEY"	=> "id_forum",
			"KEY id_auteur"	=> "id_auteur",
			"KEY id_parent"	=> "id_parent",
			"KEY id_thread"	=> "id_thread",
			"KEY optimal" => "statut,id_parent,id_article,date_heure,id_breve,id_syndic,id_rubrique");
	
	$spip_forum_join = array(
			"id_forum"=>"id_forum",
			"id_parent"=>"id_parent",
			"id_article"=>"id_article",
			"id_breve"=>"id_breve",
			"id_message"=>"id_message",
			"id_syndic"=>"id_syndic",
			"id_rubrique"=>"id_rubrique");
			
	$tables_principales['spip_forum'] =
		array('field' => &$spip_forum,	'key' => &$spip_forum_key, 'join' => &$spip_forum_join);

	return $tables_principales;
}

/**
 * Tables de jointures mots_forums
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function forum_declarer_tables_auxiliaires($tables_auxiliaires){

	$spip_mots_forum = array(
			"id_mot"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_forum"	=> "bigint(21) DEFAULT '0' NOT NULL");
	
	$spip_mots_forum_key = array(
			"PRIMARY KEY"	=> "id_forum, id_mot",
			"KEY id_mot"	=> "id_mot");
	$tables_auxiliaires['spip_mots_forum'] = array(
		'field' => &$spip_mots_forum,
		'key' => &$spip_mots_forum_key);

	return $tables_auxiliaires;
}

?>
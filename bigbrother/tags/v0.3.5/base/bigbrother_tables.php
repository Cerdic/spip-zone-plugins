<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

if (!defined("_ECRIRE_INC_VERSION")) return;

function bigbrother_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['journal'] = 'journal';
	$interface['table_des_tables']['visites_auteurs']='visites_auteurs';
	$interface['table_des_tables']['visites_articles_auteurs']='visites_articles_auteurs';

	$interface['tables_jointures']['spip_auteurs'][] = 'journal';
	$interface['tables_jointures']['spip_articles'][] = 'journal';
	$interface['tables_jointures']['spip_rubriques'][] = 'journal';
	$interface['tables_jointures']['spip_mots'][] = 'journal';

	$interface['table_date']['journal'] = 'date';
	$interface['table_date']['visites_auteurs'] = 'date';
	$interface['table_date']['visites_articles_auteurs'] = 'date_debut';

	return $interface;

}

function bigbrother_declarer_tables_principales($tables_principales){

	$spip_journal_champs = array(
		'id_journal' => "bigint(21) NOT NULL",
		'id_auteur' => "VARCHAR (25) DEFAULT '' NOT NULL",
		'action' => "text NOT NULL DEFAULT ''",
		'objet' => "VARCHAR (25) DEFAULT '' NOT NULL",
		'id_objet' => "bigint(21) NOT NULL",
		'infos' => "text NOT NULL DEFAULT ''",
		'date' => 'datetime default "0000-00-00 00:00" not null'
	);

	$spip_journal_cles = array(
		"PRIMARY KEY" 	=> "id_journal"
	);

	$tables_principales['spip_journal'] = array(
		'field' => &$spip_journal_champs,
		'key' => &$spip_journal_cles
	);

	// Table enregistrant les visites de ceux qui ont un compte
	$spip_visites_auteurs_champs = array(
		'date' => 'datetime default "0000-00-00 00:00" not null',
		'id_auteur' => 'bigint(21) not null'
	);
	$spip_visites_auteurs_cles = array(
		'KEY id_auteur' => 'id_auteur',
		'PRIMARY KEY' => 'date, id_auteur'
	);
	$tables_principales['spip_visites_auteurs'] = array(
		'field' => &$spip_visites_auteurs_champs,
		'key' => &$spip_visites_auteurs_cles
	);

	// Table enregistrant le temps passé sur un article
	$spip_visites_articles_auteurs_champs = array(
		'id_auteur' => 'bigint(21) not null',
		'id_article' => 'bigint(21) not null',
		'date_debut' => 'datetime default "0000-00-00 00:00" not null',
		'date_fin' => 'datetime default "0000-00-00 00:00" not null'
	);
	$spip_visites_articles_auteurs_cles = array(
		'KEY id_auteur' => 'id_auteur',
		'KEY id_article' => 'id_article',
		'PRIMARY KEY' => 'date_debut, id_auteur, id_article'
	);
	$tables_principales['spip_visites_articles_auteurs'] = array(
		'field' => &$spip_visites_articles_auteurs_champs,
		'key' => &$spip_visites_articles_auteurs_cles
	);

	return $tables_principales;

}

?>

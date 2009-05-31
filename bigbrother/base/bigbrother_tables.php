<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#


function bigbrother_declarer_tables_interfaces($interface){

	$interface['table_des_tables']['visites_auteurs']='visites_auteurs';
	$interface['table_des_tables']['visites_articles_auteurs']='visites_articles_auteurs';
	
	$interface['table_date']['visites_auteurs'] = 'date';
	$interface['table_date']['visites_articles_auteurs'] = 'date_debut';
	
	return $interface;

}

function bigbrother_declarer_tables_principales($tables_principales){

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

<?php

// Declaration des tables pourles nouveaux objets de Relecture:
// - relecture : table spip_relectures
// - commentaire : table spip_commentaires
//
function relecture_declarer_tables_objets_sql($tables) {
	include_spip('inc/config');
	
	$tables['spip_relectures'] = array(
		// Base de donnees
		'table_objet'			=> 'relectures',
		'type'					=> 'relecture',
		'field'					=> array(
			"id_relecture"	=> "bigint(21) NOT NULL",
			"periode_debut"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"periode_fin"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"relecteurs"	=> "text DEFAULT '' NOT NULL", //tableau serialise des id d'auteurs
			"description"	=> "text DEFAULT '' NOT NULL",
			"id_article"	=> "bigint(21) NOT NULL",
			"rev_ouverture" => "bigint(21) NOT NULL",
			"article_descr"	=> "text DEFAULT '' NOT NULL",
			"article_chapo"	=> "mediumtext DEFAULT '' NOT NULL",
			"article_texte"	=> "longtext DEFAULT '' NOT NULL",
			"article_ps"	=> "mediumtext DEFAULT '' NOT NULL",
			"statut"		=> "varchar(10) DEFAULT '' NOT NULL",
			"date_cloture"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"rev_cloture"	=> "bigint(21) NOT NULL",
			"maj"			=> "timestamp"),
		'key'					=> array(
			"PRIMARY KEY"	=> "id_relecture",
			"KEY id_article"	=> "id_article"),
		'principale'			=> 'oui',

		// Titre, date et gestion du statut
		'titre'					=> "id_relecture AS titre, '' AS lang",
		
		// Edition, affichage et recherche
		'page'					=> 'relecture',
		'url_voir'				=> '',
		'editable'				=> 'non',
		'champs_editables'		=> array(),
		'rechercher_champs'		=> array(),
		'rechercher_jointures'	=> array(),
		'icone_objet'			=> 'relecture',
		
		// Textes standard
		'texte_retour' 			=> '',
		'texte_modifier' 		=> '',
		'texte_creer' 			=> '',
		'texte_creer_associer' 	=> '',
		'texte_signale_edition' => '',
		'texte_objet' 			=> 'relecture:titre_relecture',
		'texte_objets' 			=> 'relecture:titre_relectures',
		'info_aucun_objet'		=> 'relecture:info_aucun_relecture',
		'info_1_objet' 			=> 'relecture:info_1_relecture',
		'info_nb_objets' 		=> 'relecture:info_nb_relectures',
		'texte_logo_objet' 		=> '',
	);

	$tables['spip_commentaires'] = array(
		// Base de donnees
		'table_objet'			=> 'commentaires',
		'type'					=> 'commentaire',
		'field'					=> array(
			"id_commentaire"=> "bigint(21) NOT NULL",
			"id_relecture"	=> "bigint(21) NOT NULL",
			"id_emetteur"	=> "bigint(21) NOT NULL",
			"repere"		=> "varchar(32) DEFAULT '' NOT NULL",
			"texte"			=> "text DEFAULT '' NOT NULL",
			"reponse"		=> "text DEFAULT '' NOT NULL",
			"date_crea" 	=> "bigint(21) NOT NULL",
			"date_modif" 	=> "bigint(21) NOT NULL",
			"date_cloture"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"statut"		=> "varchar(10) DEFAULT '' NOT NULL",
			"maj"			=> "timestamp"),
		'key'					=> array(
			"PRIMARY KEY"	=> "id_commentaire",
			"KEY id_article"	=> "id_relecture"),
		'principale'			=> 'oui',

		// Titre, date et gestion du statut
		'titre'					=> "id_commentaire AS titre, '' AS lang",

		// Edition, affichage et recherche
		'page'					=> '',
		'url_voir'				=> '',
		'editable'				=> 'non',
		'champs_editables'		=> array(),
		'rechercher_champs'		=> array(),
		'rechercher_jointures'	=> array(),
		'icone_objet'			=> '',

		// Textes standard
		'texte_retour' 			=> '',
		'texte_modifier' 		=> '',
		'texte_creer' 			=> '',
		'texte_creer_associer' 	=> '',
		'texte_signale_edition' => '',
		'texte_objet' 			=> 'relecture:titre_commentaire',
		'texte_objets' 			=> 'relecture:titre_commentaires',
		'info_aucun_objet'		=> 'relecture:info_aucun_commentaire',
		'info_1_objet' 			=> 'relecture:info_1_commentaire',
		'info_nb_objets' 		=> 'relecture:info_nb_commentaires',
		'texte_logo_objet' 		=> '',
	);

	return $tables;
}


function relecture_declarer_tables_auxiliaires($tables_auxiliaires) {

	return $tables_auxiliaires;
}


function relecture_declarer_tables_interfaces($interface) {
	// Les tables : permet d'appeler une boucle avec le *type* de la table uniquement
 	$interface['table_des_tables']['relectures'] = 'relectures';
	$interface['table_des_tables']['commentaires'] = 'commentaires';

	// Les traitements
	// - table spip_relectures
	$interface['table_des_traitements']['DESCRIPTION']['relectures'] = _TRAITEMENT_RACCOURCIS;
	// - table spip_commentaires
	$interface['table_des_traitements']['TEXTE']['commentaires'] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['REPONSE']['commentaires'] = _TRAITEMENT_RACCOURCIS;

	return $interface;
}

?>

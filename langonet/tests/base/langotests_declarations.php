<?php

// Declaration des tables pourles nouveaux objets de Relecture:
// - relecture : table spip_relectures
// - commentaire : table spip_commentaires
// Les relecteurs sont inseres dans la table spip_auteurs_liens
//
function langotests_declarer_tables_objets_sql($tables) {
	include_spip('inc/config');
	
	$tables['spip_langotests'] = array(
		// Base de donnees
		'table_objet'			=> 'langotests',
		'type'					=> 'langotest',
		'field'					=> array(
			"id_langotest"		=> "bigint(21) NOT NULL",
			"date_1"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"date_2" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"description"		=> "text DEFAULT '' NOT NULL",
			"statut"			=> "varchar(10) DEFAULT '' NOT NULL",
			"date_3"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"maj"				=> "timestamp"),
		'key'					=> array(
			"PRIMARY KEY"	=> "id_langotest"),
		'principale'			=> 'oui',

		// Titre, date et gestion du statut
		'titre'				=> '',
		'date' 				=> 'date_1', // Pour le formulaire dater uniquement
		'texte_changer_statut' => 'langotests:texte_instituer',
		'aide_changer_statut' => '',
		'statut_titres' => array(
			'ok' => 'langotests:titre_langotest_ok',
			'nok' => 'langotests:titre_langotest_nok'
		),
		'statut_textes_instituer' => 	array(
			'ok' => 'langotests:texte_langotest_ok',
			'nok' => 'langotests:texte_langotest_nok'
		),
		'statut_images' => array(
			'ok'=>'puce-preparer-8.png',
			'nok'=>'puce-publier-8.png',
		),
	);

	return $tables;
}


function langotests_declarer_tables_interfaces($interface) {
	// Les tables : permet d'appeler une boucle avec le *type* de la table uniquement
 	$interface['table_des_tables']['langotests'] = 'langotests';

	return $interface;
}

?>

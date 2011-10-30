<?php
include_spip('base/create');



function sel_upgrade($nom_meta_base_version, $version_cible){

	$spip_branche_principale = substr($GLOBALS[spip_version_branche], 0, 1);

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
	$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	if ($spip_branche_principale==2) {
		
		if ($current_version=="0.0") {
			creer_base();
			$vue_offreurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_offreur",
				"nom"
				),
				"spip_auteurs"
			);
			$vue_demandeurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_demandeur",
				"nom"
				),
				"spip_auteurs"
			);
			$vue_valideurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_valideur",
				"nom"
				),
				"spip_auteurs"
			);
			sql_create_view(spip_offreurs,$vue_offreurs);
			sql_create_view(spip_demandeurs,$vue_demandeurs);
			sql_create_view(spip_valideurs,$vue_valideurs);
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible);

			// Fourniture de quelques SELs pour démarrer (juste pour l'exemple, supprimer ces lignes par la suite)
			$qqs_sels = 'spip_sels';
			sql_insertq_multi($qqs_sels, array(
				array(
					id_sel => '0',
					nom => 'JEU',
					adresse1 => 'le monde !',
					adresse2 => '',
					code_postal => '00000',
					ville => '',
					pays => '',
					email => '',
					nom_unite => ''
				),

				array(
					id_sel => '1',
					nom => 'SEL de Paris',
					adresse1 => '1/3 rue Frédérick Lemaître',
					adresse2 => 'BP41',
					code_postal => '75020',
					ville => 'PARIS',
					pays => 'France',
					email => 'contact@seldeparis.org',
					nom_unite => 'piaf'
				),
				array(
					id_sel => '2',
					nom => 'SEL de Loire',
					adresse1 => '-',
					adresse2 => '',
					code_postal => '37000',
					ville => 'TOURS',
					pays => 'France',
					email => 'contact@seldeloire.org',
					nom_unite => 'unité'
				)
			));
			
			$qqs_orgs = 'spip_organisations';
			sql_insertq_multi($qqs_orgs, array(
				array(
					id_organisations => '1',
					nom => 'Route des SEL',
					description => 'La route des SEL'
				),
				array(
					id_organisations => '2',
					nom => 'Selidaire',
					description => 'Selidaire'
				),
				array(
					id_organisations => '3',
					nom => 'le MES',
					description => 'Acteurs de l\'Econimie Sociale et Solidaire'
				),
			));
		}
			
		// if (version_compare($current_version,"0.2","<")) {
			// mise à jour de la structure des tables
			// maj_tables('spip_xxx');
			// ecrire_meta($nom_meta_base_version,$current_version="0.2");
		// }
		
	}
	
	if ($spip_branche_principale==3) {	
		$maj = array();
		$maj['create'] = array( 	// pour première installation
			array('maj_tables', array('spip_auteurs','spip_sels','spip_organisations','spip_annonces','spip_echanges','spip_themes','spip_correspondances','spip_parametres','spip_themes_annonces')),
			
			// debug :
			// array('maj_tables', array('spip_sels'))
		);		

		if ($current_version=="0.0") {		
			$vue_offreurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_offreur",
				"nom"
				),
				"spip_auteurs"
			);
			$vue_demandeurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_demandeur",
				"nom"
				),
				"spip_auteurs"
			);
			$vue_valideurs = sql_get_select(
				$champs = array(
				"id_auteur AS id_valideur",
				"nom"
				),
				"spip_auteurs"
			);
			sql_create_view(spip_offreurs,$vue_offreurs);
			sql_create_view(spip_demandeurs,$vue_demandeurs);
			sql_create_view(spip_valideurs,$vue_valideurs);
			
			// Fourniture de quelques SELs pour démarrer (juste pour l'exemple, supprimer ces lignes par la suite)
			sql_insertq_multi($dpt, array(
				array(
					id_sel => '0',
					nom => 'JEU',
					adresse1 => 'le monde !',
					adresse2 => '',
					code_postal => '00000',
					ville => '',
					pays => '',
					email => '',
					nom_unite => ''
				),

				array(
					id_sel => '1',
					nom => 'SEL de Paris',
					adresse1 => '1/3 rue Frédérick Lemaître',
					adresse2 => 'BP41',
					code_postal => '75020',
					ville => 'PARIS',
					pays => 'France',
					email => 'contact@seldeparis.org',
					nom_unite => 'piaf'
				),
				array(
					id_sel => '2',
					nom => 'SEL de Loire',
					adresse1 => '-',
					adresse2 => '',
					code_postal => '37000',
					ville => 'TOURS',
					pays => 'France',
					email => 'contact@seldeloire.org',
					nom_unite => 'unité'
				)
			));

			$qqs_orgs = 'spip_organisations';
			sql_insertq_multi($qqs_orgs, array(
				array(
					id_organisations => '1',
					nom => 'Route des SEL',
					description => 'La route des SEL'
				),
				array(
					id_organisations => '2',
					nom => 'Selidaire',
					description => 'Selidaire'
				),
				array(
					id_organisations => '3',
					nom => 'le MES',
					description => 'Acteurs de l\'Econimie Sociale et Solidaire'
				),
			));
			
		}
		
		// $maj['1.1.0'] = array(...)    // pour mises à jour
		include_spip('base/upgrade');
		maj_plugin($nom_meta_base_version, $version_cible, $maj);	
	}
	
}		

	
function sel_vider_tables($nom_meta_base_version) {
	// sql_alter("TABLE spip_xxx DROP champ");
	// pour supprimer un champ du plugin sur une table de base de spip, auparaveant ajouté par ce plugin
	
	sql_alter("TABLE spip_auteurs DROP adresse1, DROP adresse2, DROP code_postal, DROP ville, DROP pays, DROP tel1, DROP tel2, DROP commentaires");
	sql_drop_table("spip_sels");
	sql_drop_table("spip_organisations");
	sql_drop_table("spip_annonces");
	sql_drop_table("spip_echanges");
	sql_drop_table("spip_themes");
	sql_drop_table("spip_parametres");
	sql_drop_table("spip_themes_annonces");
	sql_drop_table("spip_correspondances");
	// pour supprimer une table entière appartenant au plugin qu'on supprime
	
	sql_drop_view("spip_offreurs");
	sql_drop_view("spip_demandeurs");
	sql_drop_view("spip_valideurs");
	// pour supprimer une vue appartenant au plugin qu'on supprime
	
	effacer_meta($nom_meta_base_version);
	// suppression des informations du plugin dans la table meta
}


?>
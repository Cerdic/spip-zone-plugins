<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

// Installation et mise à jour
function dictionnaires_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			// Valeurs de config par défaut
			ecrire_meta('dictionnaires', serialize(array(
				'remplacer_premier_defaut' => 'on',
				'remplacer_premier_abbr' => 'on',
			)));

			// Migration depuis F&T si présent
			dictionnaires_migrer_acronymes();
			
			echo "Installation du plugin dictionnaires<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		if (version_compare($version_actuelle,$version_cible='0.2.0','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			// On ajoute un champ pour choisir le type par défaut dans un dictionnaire
			maj_tables('spip_dictionnaires');
			
			// On change la version
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		if (version_compare($version_actuelle,$version_cible='0.3.0','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			// On ajoute un champ pour forcer la prise en compte de la casse pour une définition
			maj_tables('spip_definitions');
			
			// On change la version
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	}

}

function dictionnaires_migrer_acronymes(){
	// Si F&T est actif là tout de suite et qu'il y a une table d'acronymes
	if (
		_DIR_PLUGIN_FORMS
		and include_spip('base/forms_base_api')
		and count($liste=Forms_liste_tables('acronymes_sigles'))
	){
		include_spip('forms_fonctions');
		$id_form = intval(reset($liste));
		spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut='publie'");
		$acronymes = sql_allfetsel('id_donnee, statut, date', 'spip_forms_donnees', 'id_form = '.$id_form);
		if ($acronymes and is_array($acronymes)){
			// On commence par créer un dictionnaire pour l'importation
			include_spip('action/editer_dictionnaire');
			if ($id_dictionnaire = insert_dictionnaire()){
				// On lui met des champs par défaut
				dictionnaire_set($id_dictionnaire, array(
					'titre' => _T('dictionnaire:importer_acronymes_titre'),
					'actif' => 1,
					'descriptif' => _T('dictionnaire:importer_acronymes_descriptif'),
					'type_defaut' => 'abbr',
				));
				
				// On parcourt ensuite les acronymes à importer pour récupérer leurs infos
				foreach ($acronymes as $acronyme){
					if ($titre = trim(str_replace("." , "", forms_calcule_les_valeurs('forms_donnees_champs', $acronyme['id_donnee'], 'ligne_1', $id_form,' ', true)))){
						$definition = array(
							'id_dictionnaire' => $id_dictionnaire,
							'titre' => $titre,
							'texte' => forms_calcule_les_valeurs('forms_donnees_champs', $acronyme['id_donnee'], 'texte_1', $id_form,' ', true),
							'type' => 'abbr',
							'casse' => 1,
							'date' => $acronyme['date'],
							'statut' => ($acronyme['statut'] == 'publie') ? 'publie' : 'prop',
							'lang' => forms_calcule_les_valeurs('forms_donnees_champs', $acronyme['id_donnee'], 'select_2', $id_form,' ', true)
						);
						
						// On crée la définition dans la base SANS calculer le cache
						include_spip('action/editer_definition');
						if ($id_definition = insert_definition()){
							definition_set($id_definition, $definition, false);
						}
					}
				}
				
				// On calcule le cache des définitions une seule fois à la fin
				include_spip('inc/dictionnaires');
				dictionnaires_lister_definitions(true);
			}
		}
	}
}

// Désinstallation
function dictionnaires_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_dictionnaires');
	sql_drop_table('spip_definitions');
	sql_drop_table('spip_definitions_liens');
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);

}

?>

<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Dictionnaire\Installation
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables dictionnaires et définitions...
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function dictionnaires_upgrade($nom_meta_base_version, $version_cible) {

	include_spip('inc/config');
	include_spip('base/create');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_dictionnaires', 'spip_definitions', 'spip_definitions_liens')),
		array('ecrire_config', 'dictionnaires/remplacer_premier_defaut', 'on'),
		array('ecrire_config', 'dictionnaires/remplacer_premier_abbr', 'on'),
		array('dictionnaires_migrer_acronymes'),
	);

	$maj['0.2.0'] = array(array('maj_tables', 'spip_dictionnaires'));
	$maj['0.3.0'] = array(array('maj_tables', 'spip_definitions'));

	// deplacer les statuts du dictionnaires de 'actif' a 'statut'
	$maj['0.4.0'] = array(
		array('maj_tables', 'spip_dictionnaires'),
		array('sql_update', 'spip_dictionnaires', array('statut'=>'actif'), 'actif=1'),
		array('sql_update', 'spip_dictionnaires', array('statut'=>'inactif'), 'actif=0'),
		array('sql_alter', 'TABLE spip_dictionnaires DROP COLUMN actif'),
	);
	// pas de not null sans integer pour sqlite
	$maj['0.4.1'] = array(
		array('sql_alter', 'TABLE spip_definitions CHANGE COLUMN id_dictionnaire id_dictionnaire bigint(21) not null default 0')
	);
	// Ajout du champ url_extense dans la table spip_definitions
	$maj['0.4.2'] = array(array('maj_tables', 'spip_definitions'));
	
	// Ajout du champ id_trad sur les définitions
	$maj['0.4.3'] = array(array('maj_tables', 'spip_definitions'));
	// Ajouter les langues sur les définitions anciennes
	$maj['0.4.4'] = array(array('definitions_langues'));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function definitions_langues(){
	sql_updateq('spip_definitions',array('lang'=>$GLOBALS['meta']['langue_site']),'lang = ""');
	
}
/**
 * Migre les acronymes du plugins Forms & Tables (s'il est actif)
 * dans ce plugin.
**/
function dictionnaires_migrer_acronymes(){
	// Si F&T contient une table d'acronymes
	if (count($liste = sql_fetsel('id_form', 'spip_forms', "type_form='acronymes_sigles'"))) {
		$id_form = intval(reset($liste));
		$acronymes = sql_allfetsel('id_donnee, statut, date', 'spip_forms_donnees', 'id_form = '.$id_form);
		if ($acronymes and is_array($acronymes)){
			// On commence par créer un dictionnaire pour l'importation
			include_spip('action/editer_dictionnaire');
			if ($id_dictionnaire = insert_dictionnaire()){
				// On lui met des champs par défaut
				autoriser_exception('modifier', 'dictionnaire', $id_dictionnaire);
				dictionnaire_set($id_dictionnaire, array(
					'titre' => _T('dictionnaire:importer_acronymes_titre'),
					'statut' => 'actif',
					'descriptif' => _T('dictionnaire:importer_acronymes_descriptif'),
					'type_defaut' => 'abbr',
				));
				autoriser_exception('modifier', 'dictionnaire', $id_dictionnaire, false);
				
				// On parcourt ensuite les acronymes à importer pour récupérer leurs infos
				foreach ($acronymes as $acronyme){
					if ($titre = trim(str_replace("." , "", reset(sql_fetsel("valeur", "spip_forms_donnees_champs", array('id_donnee='.$acronyme['id_donnee'], "champ='ligne_1'")))))){
						$lang_select = reset(sql_fetsel("valeur", "spip_forms_donnees_champs", array('id_donnee='.$acronyme['id_donnee'], "champ='select_2'")));
						$lang = reset(sql_fetsel("titre", "spip_forms_champs_choix", array("champ='select_2'", "choix='$lang_select'")));
						$definition = array(
							'id_dictionnaire' => $id_dictionnaire,
							'titre' => $titre,
							'texte' => reset(sql_fetsel("valeur", "spip_forms_donnees_champs", array('id_donnee='.$acronyme['id_donnee'], "champ='texte_1'"))),
							'type' => 'abbr',
							'casse' => 1,
							'date' => $acronyme['date'],
							'statut' => ($acronyme['statut'] == 'publie') ? 'publie' : 'prop',
							'lang' => $lang
						);
						
						// On crée la définition dans la base SANS calculer le cache
						include_spip('action/editer_definition');
						if ($id_definition = insert_definition()){
							// 0 et pas $id_dictionnaire 
							// car insert_definition a utilisé les valeurs par défaut
							autoriser_exception('publierdans', 'dictionnaire', 0);
							autoriser_exception('modifier', 'definition', $id_definition);
							definition_set($id_definition, $definition, false);
							autoriser_exception('modifier', 'definition', $id_definition, false);
							autoriser_exception('publierdans', 'dictionnaire', 0, false);
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

/**
 * Désinstallation/suppression des tables dictionnaires et definitions
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function dictionnaires_vider_tables($nom_meta_base_version){

	include_spip('base/abstract_sql');

	// On efface les tables du plugin
	sql_drop_table('spip_dictionnaires');
	sql_drop_table('spip_definitions');
	sql_drop_table('spip_definitions_liens');

	// Effacer les configurations
	effacer_meta('dictionnaires');

	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);

}

?>

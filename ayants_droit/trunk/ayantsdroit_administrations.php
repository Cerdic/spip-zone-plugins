<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Ayants droit
 *
 * @plugin     Ayants droit
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Ayantsdroit\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Ayants droit.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function ayantsdroit_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_droits_ayants', 'spip_droits_contrats', 'spip_droits_contrats_liens')),
	);
	
	// Ajout du champ "montant" dans les contrats
	$maj['1.1.0'] = array(
		array('maj_tables', array('spip_droits_contrats')),
	);
	
	// Ajout des champs "interlocuteur" et "credits" pour les ayants droit
	$maj['1.2.0'] = array(
		array('maj_tables', array('spip_droits_ayants')),
	);
	
	// Migration en table de liens
	$maj['1.3.0'] = array(
		array('maj_tables', array('spip_droits_contrats_liens')),
		array('ayantsdroit_maj_1_3_0'),
		array('sql_alter', 'table spip_droits_contrats drop column objet'),
		array('sql_alter', 'table spip_droits_contrats drop column id_objet'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Déplacer tous les liens internes dans la table de liens
function ayantsdroit_maj_1_3_0() {
	if ($contrats = sql_allfetsel('id_droits_contrat, objet, id_objet', 'spip_droits_contrats')) {
		include_spip('action/editer_liens');
		
		foreach ($contrats as $contrat) {
			if ($objet = $contrat['objet'] and $id_objet = $contrat['id_objet']) {
				objet_associer(
					array('droits_contrat' => $contrat['id_droits_contrat']),
					array($objet => $id_objet)
				);
			}
		}
	}
}


/**
 * Fonction de désinstallation du plugin Ayants droit.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function ayantsdroit_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_droits_ayants");
	sql_drop_table("spip_droits_contrats");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('droits_ayant', 'droits_contrat')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('droits_ayant', 'droits_contrat')));
	sql_delete("spip_forum",                 sql_in("objet", array('droits_ayant', 'droits_contrat')));

	effacer_meta($nom_meta_base_version);
}

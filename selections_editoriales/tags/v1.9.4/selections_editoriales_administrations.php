<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Sélections éditoriales
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Sélections éditoriales.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function selections_editoriales_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_selections', 'spip_selections_liens', 'spip_selections_contenus')),
	);

	// Ajout d'un champ pour ajouter des classes CSS à un contenu sélectionné
	$maj['1.1.0'] = array(
		array('maj_tables', array('spip_selections_contenus')),
	);

	// Ajouter un vrai champ "rang" et le peupler
	$maj['1.4.0'] = array(
		array('maj_tables', array('spip_selections_contenus')),
		array('selections_editoriales_maj_1_4_0'),
	);

	// Ajouter les champs objet/id_objet et les peupler
	$maj['1.5.0'] = array(
		array('maj_tables', array('spip_selections_contenus')),
		array('selections_editoriales_maj_1_5_0'),
	);

	// Ajouter un champ "css" sur les selections
	$maj['1.5.1'] = array(
		array('maj_tables', array('spip_selections')),
	);

	// Ajouter un index sur le champ id_selection de la table spip_selections_contenus
	$maj['1.7.10'] = array(
		array('maj_tables', array('spip_selections_contenus'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Peupler le nouveau vrai champ "rang"
function selections_editoriales_maj_1_4_0() {
	// On cherche toutes les sélections
	if ($selections = sql_allfetsel('id_selection', 'spip_selections')) {
		include_spip('inc/filtres');

		foreach ($selections as $selection) {
			$id_selection = intval($selection['id_selection']);

			// On cherche tous les contenus, déjà classés dans le bon ordre
			if ($contenus = sql_allfetsel(
				'id_selections_contenu, titre, 0+titre as num',
				'spip_selections_contenus',
				'id_selection = '.$id_selection,
				'',
				'num,titre'
			)) {
				$rang = 1;
				foreach ($contenus as $contenu) {
					$id_selections_contenu = intval($contenu['id_selections_contenu']);

					// On met à jour le rang et le titre sans l'ancien numéro
					sql_updateq(
						'spip_selections_contenus',
						array(
							'rang' => $rang,
							'titre' => supprimer_numero($contenu['titre']),
						),
						'id_selections_contenu = '.$id_selections_contenu
					);

					$rang++;
				}
			}
		}
	}
}

// Peupler les objet/id_objet pour les contenus déjà là
function selections_editoriales_maj_1_5_0() {
	// On cherche tous les contenus, peu importe la sélection
	if ($contenus = sql_allfetsel('id_selections_contenu, url', 'spip_selections_contenus')) {
		include_spip('inc/lien');
		include_spip('base/objets');

		foreach ($contenus as $contenu) {
			$trouve = typer_raccourci($contenu['url']);
			@list($objet, , $id_objet, , $args, , $ancre) = $trouve;

			if ($objet and $id_objet and $objet = objet_type(table_objet($objet))) {
				sql_updateq(
					'spip_selections_contenus',
					array(
						'objet' => $objet,
						'id_objet' => $id_objet,
					),
					'id_selections_contenu = '.$contenu['id_selections_contenu']
				);
			}
		}
	}
}

/**
 * Fonction de désinstallation du plugin Sélections éditoriales.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function selections_editoriales_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_selections');
	sql_drop_table('spip_selections_liens');
	sql_drop_table('spip_selections_contenus');

	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('selection', 'selections_contenu')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('selection', 'selections_contenu')));
	sql_delete('spip_forum', sql_in('objet', array('selection', 'selections_contenu')));

	effacer_meta($nom_meta_base_version);
	effacer_meta('selections_editoriales');
}

<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le schéma comprend des tables et des variables de configuration propres au plugin.
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 * @param string $version_cible
 * 		Version du schéma de données en fin d'upgrade
 *
 * @return void
 */
function noizetier_layout_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Configurations par défaut
	$config = array(
		'inclure_css_public' => 'on',
	);

	// 1ère installation
	$maj['create'] = array(
		array('ecrire_config', 'noizetier_layout', $config),
	);

	// schéma 0.2.0 : renommages
	$maj['0.2.0'] = array(
		array('noizetier_layout_maj_020'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Suppression de l'ensemble du schéma de données propre au plugin, c'est-à-dire
 * les tables et les variables de configuration.
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 *
 * @return void
 */
function noizetier_layout_vider_tables($nom_meta_base_version) {

	// On efface la version enregistrée du schéma des données du plugin
	effacer_meta($nom_meta_base_version);
	// On efface la configuration du plugin
	effacer_meta('noizetier_layout');
}

/**
 * Mise à jour vers le schema 0.2.0
 *
 * Mettre à jour les noisettes suite à divers renommages.
 *
 * @return void
 */
function noizetier_layout_maj_020() {
	include_spip('base/abstract_sql');
	if ($noisettes = sql_allfetsel('*', 'spip_noisettes')) {
		foreach ($noisettes as $noisette) {
			$set = array();
			$type_ancien = 'conteneur_row';
			$type_nouveau = 'conteneur';
			// type_noisette : `conteneur_row` → `conteneur`
			if ($noisette['type_noisette'] == $type_ancien) {
				$set['type_noisette'] = $type_nouveau;
			}
			// id_conteneur : `conteneur_row` → `conteneur`
			if (
				$id_conteneur = str_replace($type_ancien, $type_nouveau, $noisette['id_conteneur'])
				and $id_conteneur != $noisette['id_conteneur']
			) {
				$set['id_conteneur'] = $id_conteneur;
			}
			// conteneur : `conteneur_row` → `conteneur`
			if (
				$conteneur = unserialize($noisette['conteneur'])
				and $conteneur['type_noisette'] == $type_ancien
			) {
				$conteneur['type_noisette'] = $type_nouveau;
				$set['conteneur'] = serialize($conteneur);
			}
			// parametres : `css_container` → `css_grid_container` etc.
			if ($parametres = unserialize($noisette['parametres'])) {
				$map = array(
					'css_container' => 'css_grid_container',
					'css_row'       => 'css_grid_row',
					'css_column'    => 'css_grid_column',
				);
				foreach ($map as $avant => $apres) {
					if (isset($parametres[$avant])) {
						$parametres[$apres] = $parametres[$avant];
						unset($parametres[$avant]);
					}
				}
				$parametres = serialize($parametres);
				if ($parametres != $noisette['parametres']) {
					$set['parametres'] = $parametres;
				}
			}
			// màj
			if ($set) {
				sql_updateq(
					'spip_noisettes',
					$set,
					'id_noisette='.intval($noisette['id_noisette'])
				);
			}
		}
	}
	// Invalider le cache des types de noisettes
	include_spip('inc/ncore_type_noisette');
	type_noisette_charger('noizetier', true);
}

<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Prix objets par périodes
 *
 * @plugin     Prix objets par périodes
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Prix_objets_periodes\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Prix objets par périodes.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function prix_objets_periodes_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();


	$maj['create'] = array(array('maj_tables', array('spip_po_periodes')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_po_periodes')));
	$maj['1.0.2'] = array(array('maj_tables', array('spip_po_periodes')));
	$maj['1.1.0'] = array(
		array('pop_upgrade', '1.1.0'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Prix objets par périodes.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function prix_objets_periodes_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table('spip_xx');
	# sql_drop_table('spip_xx_liens');

	sql_drop_table('spip_po_periodes');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('po_periode')));
	sql_delete('spip_mots_liens', sql_in('objet', array('po_periode')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('po_periode')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('po_periode')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('po_periode')));
	sql_delete('spip_forum', sql_in('objet', array('po_periode')));

	effacer_meta($nom_meta_base_version);
}

/**
 * Actualise la bd
 *
 * @param string $version_cible
 *  la version de la bd
 */
function pop_upgrade($version_cible) {

	// Les périodes sont gérés par le plugin périodes dorénavant.
	if ($version_cible == '1.1.0') {
		$sql = sql_select('*', 'spip_po_periodes');
		// Alimenter la bd spip_periodes avec les contenus de spip_po_periodes.
		while ($row = sql_fetch($sql)) {
			$id_po_periode = $row['id_po_periode'];
			unset($row['id_po_periode']);
			unset($row['maj']);
			unset($row['statut']);

			$id_periode = sql_insertq('spip_periodes', $row);

			// Adapter spip_prix_objets
			sql_updateq(
				'spip_prix_objets',
				array(
					'extension' => 'periode',
					'id_extension' => $id_periode,
				),
				'extension LIKE ' . sql_quote('po_periode') . ' AND id_extension=' . $id_po_periode
			);
		}

		// Effacer la table spip_po_periodes.
		sql_drop_table('spip_po_periodes');
	}
}

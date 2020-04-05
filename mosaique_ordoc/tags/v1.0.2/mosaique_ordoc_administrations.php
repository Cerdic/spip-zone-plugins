<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Migration de Mosaïque vers Ordoc
 *
 * @plugin     Migration de Mosaïque vers Ordoc
 * @copyright  2017
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Mosaique_ordoc\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Migration de Mosaïque vers Ordoc.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function mosaique_ordoc_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('mosaique_ordoc_migration'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction qui migre les données du plugin Migration de Mosaïque vers Ordoc.
 *
 * @return void
**/

function mosaique_ordoc_migration() {
	// etape 1 : on récupère tous les articles avec des mosaiques
	$mosaiques = array();
	if ($resultats = sql_allfetsel('id_article, mosaique', 'spip_articles', 'mosaique != ""')) {
		foreach ($resultats as $r) {
			$mosaiques[$r['id_article']] = $r['mosaique'];
		}
	}

	// etape 2: on injecte les données de mosaique dans ordoc
	foreach ($mosaiques as $id_article => $mosaique) {
		$mosaique_items = explode(",", $mosaique);
		$i = 1;
		foreach ($mosaique_items as $mosaique_item) {
			$where = array(
				'id_document = '.intval($mosaique_item),
				'id_objet = '.intval($id_article),
				'objet = "article"',
			);
			sql_updateq('spip_documents_liens', array('rang_lien' => $i), $where);
			$i++;
		}
	}
}


/**
 * Fonction de désinstallation du plugin Migration de Mosaïque vers Ordoc.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function mosaique_ordoc_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}

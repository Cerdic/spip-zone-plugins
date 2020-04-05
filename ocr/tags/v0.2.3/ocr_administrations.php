<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin ocr
 *
 * @plugin     ocr
 * @copyright  2013
 * @author     Sylvain Lesage
 * @licence    GNU/GPL
 * @package    SPIP\Ocr\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin ocr.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function ocr_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	/**
	 * Première installation
	 * On ajoute les champs spécifiques à spip_documents
	 * On crée la première configuration
	 */
	$maj['create'] = array(
		array('maj_tables', array('spip_documents')),
	);

	$maj['0.2'] = array(
		array('maj_tables', array('spip_documents')),
	);

	$maj['0.3'] = array(
		array('maj_tables', array('spip_documents')),
	);

	// Forcer l'analyse de tous les documents (changement avec doc2img)
	$maj['0.4'] = array(
		array('ocr_reinitialiser_totalement_document'),
	);

	// Forcer l'analyse de tous les documents (changement de format du contenu du champ "ocr")
	$maj['0.5'] = array(
		array('ocr_reinitialiser_totalement_document'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function ocr_reinitialiser_totalement_document() {
	sql_updateq("spip_documents", array('ocr' => '', 'ocr_analyse' => 'non'));
}

/**
 * Fonction de désinstallation du plugin ocr.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function ocr_vider_tables($nom_meta_base_version) {

	effacer_meta('ocr');
	effacer_meta($nom_meta_base_version);
}

?>

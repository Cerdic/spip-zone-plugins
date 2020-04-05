<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Ordre des documents
 *
 * @plugin     Ordre des documents
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Ordoc\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Ordre des documents.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function ordoc_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_documents_liens')));
	$maj['1.1.0'] = array(array('sql_alter', 'TABLE spip_documents_liens CHANGE ordre rang_lien int(4) DEFAULT \'0\' NOT NULL'));
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Ordre des documents.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function ordoc_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_documents_liens DROP COLUMN rang_lien');
	effacer_meta($nom_meta_base_version);
}

/**
 * Ajout d'un champ ordre sur les liens de documents.
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 * @return array
 */
function ordoc_declarer_tables_auxiliaires($tables) {
	$tables['spip_documents_liens']['field']['rang_lien'] = "int(4) DEFAULT '0' NOT NULL";
	return $tables;
}

/**
 * Ajout de l'icone de déplacement d'un document
 *
 * @pipeline document_desc_actions
 * @param array $tables
 * @return array
 */
function ordoc_document_desc_actions($flux) {
	if (
		!empty($flux['args']['objet'])
		and !empty($flux['args']['id_objet'])
		and !empty($flux['args']['position'])
		and $objet = $flux['args']['objet']
		and $id_objet = $flux['args']['id_objet']
		and $flux['args']['position'] == 'document_desc'
		and autoriser('associerdocuments', $objet, $id_objet)
	) {
		include_spip('inc/filtres');
		$deplacer =
			'<span class="ordoc-deplacer">'
			. filtrer('balise_img', chemin_image('deplacer-16.png'), _T('ordoc:deplacer_document'))
			. '</span>' . "\n";
		$flux['data'] .= $deplacer;
	}
	return $flux;
}

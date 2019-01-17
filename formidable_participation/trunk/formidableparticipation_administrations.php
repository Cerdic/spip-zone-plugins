<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Formulaires de participation
 *
 * @plugin     Formulaires d'participation
 * @licence    GNU/GPL
 * @package    SPIP\Formidableparticipation\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Formulaires de participation.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function formidableparticipation_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['install'] = array();
	$maj['1.1.0'] = array(
		array('formidableparticipation_upgrade_1_1_0')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Met à jour vers le schema 1.1.0 des traitements participations.
 * A savoir, prend les traitements de participation existants et ajoute automatiquement
 * participation_auto = 'variable'
 * evenement_type = 'fixe'
 * Ceci permet de ne pas casser les évènements
**/
function formidableparticipation_upgrade_1_1_0() {
	include_spip('inc/sql');
	$res = sql_select('id_formulaire,traitements', 'spip_formulaires');
	while ($row = sql_fetch($res)) {
		$traitements = unserialize($row['traitements']);
		if (isset($traitements['participation'])) {
			$participation = &$traitements['participation'];
			if (!isset($participation['participation_auto'])) {
				$participation['participation_auto'] = 'variable';
			}
			if (!isset($participation['evenement_type'])) {
				$participation['evenement_type'] = 'fixe';
			}
			$traitements = serialize($traitements);
			sql_updateq('spip_formulaires',array('traitements' => $traitements), 'id_formulaire='.$row['id_formulaire']);
		}
	}
}

/**
 * Fonction de désinstallation du plugin Formulaires de participation.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function formidableparticipation_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}

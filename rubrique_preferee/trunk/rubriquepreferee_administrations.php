<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function rubriquepreferee_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	cextras_api_upgrade(rubriquepreferee_declarer_champs_extras(), $maj['create']);	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param float $nom_meta_base_version
 */
function rubriquepreferee_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(rubriquepreferee_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}

/**
 * Declare le champ extra rubrique preferee
 *
 * @param array $champs
 * @return array le tableau des champs à déclarer
 */
function rubriquepreferee_declarer_champs_extras($champs = array()){
	$champs['spip_auteurs']['rubrique_preferee'] = array(
		'saisie' => 'selecteur_rubrique',
		'options' => array(
			'nom' => 'rubrique_preferee',
			'label' => _T('rubriquepreferee:titre'),
			'explication' => _T('rubriquepreferee:explication'),
			'obligatoire' => false,
			'rechercher' => false,
			'sql' => "varchar(255) NOT NULL DEFAULT ''"
		));
	return $champs;
}

?>

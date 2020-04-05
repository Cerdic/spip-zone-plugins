<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function rubriquepreferee_upgrade($nom_meta_base_version, $version_cible){

	$current_version = "0.0";
	if (
		(!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version]) != $version_cible)
	){
		if (version_compare($current_version,'0.0','=')){
			$champs = rubriquepreferee_declarer_champs_extras();
			// C'est le plugin Champs Extras qui ecrit le meta
			if(! installer_champs_extras($champs, $nom_meta_base_version, $current_version='0.1')) {
				echo _T('rubriquepreferee:msg_erreur_installation')."<br/>";
			}
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param float $nom_meta_base_version
 */
function rubriquepreferee_vider_tables($nom_meta_base_version) {

	$champs = rubriquepreferee_declarer_champs_extras();
	// C'est le plugin Champs Extras qui supprime le meta
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}


/**
 * Declare le champ extra rubrique preferee
 *
 * @param array $champs
 * @return array le tableau des champs à déclarer
 */
function rubriquepreferee_declarer_champs_extras($champs = array()){

	$champs[] = new ChampExtra(array(
		'table' => 'auteurs',
		'champ' => 'rubrique_preferee',
		'label' => 'rubriquepreferee:titre',
		'precisions' => 'rubriquepreferee:explication',
		'obligatoire' => false,
		'rechercher' => false,
		'type' => 'selecteur_rubrique',
		'sql' => "varchar(255) NOT NULL DEFAULT ''",
		'saisie_externe' => true,
	));
	return $champs;
}

?>

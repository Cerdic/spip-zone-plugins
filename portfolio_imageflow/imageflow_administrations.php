<?php
/**
 * Plugin imageflow pour Spip 3
 * Licence GPL
 */

// $LastChangedRevision: 22476 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2008-09-10 18:11:48 +0200 (mer, 10 sep 2008) $
// Creation Paquet Spip 3 : 2013-05-13

include_spip('base/abstract_sql');
include_spip('inc/utils');
include_spip('inc/imageflow_api_globales');
include_spip('inc/imageflow_api_prive');

function imageflow_init( ) {

			if(($config_error = imageflow_php_gd_versions_ok()) === true) {
				if(!($result = isset($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES]))) {
					// cree les preferences par defaut
					$result = imageflow_set_all_preferences();
					imageflow_log("CREATE meta:" . _IMAGEFLOW_META_PREFERENCES);
				}
				if(!$result) {
					// nota: SPIP ne filtre pas le resultat. Si retour en erreur,
					// la case a cocher du plugin sera quand meme cochee
					imageflow_log("PLEASE REINSTALL PLUGIN");
				}
				else {
					// invite de configuration si installation OK
					echo(_T('imageflow:imageflow_aide_install'
						, array('url_config' => generer_url_ecrire("imageflow_configure"))
						));
				}
				imageflow_log("INSTALL:", $result);
			}
			else {
				echo( imageflow_boite_alerte(
					_T('imageflow:portfolio_imageflow')
					, "<strong>\n" . _T('forum_titre_erreur') . "</strong><br />"
						. _T('imageflow:'.$config_error)
					));
			}

			return($result);
}

function imageflow_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('imageflow_init', array())
	);

	include_spip('base/upgrade');
	maj_plugin( $nom_meta_base_version, $version_cible, $maj);
}


function imageflow_vider_tables( $nom_meta_base_version) {

	effacer_meta(_IMAGEFLOW_META_PREFERENCES);
	imageflow_log("DELETE meta");

	// recharge les metas en cache
	imageflow_ecrire_metas();

	return(true);
}

?>

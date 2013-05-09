<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin paypal
 *
 * @plugin     Paniers
 * @copyright  2013
 * @author     Les Développements Durables, Matthieu Marcillaud
 * @licence    GPL v3
 * @package    SPIP\Paypal\Installation
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin paypal.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function paypal_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('base/abstract_sql');
	include_spip('inc/config');

	// Première installation
	// Options de configuration
	$maj['create'] = array(
		array('ecrire_config', 'paypal',
			array(
				'environnement' => 'test',
				'currency_code' => 'EUR',
				'account_prod' => '',
				'account_test' => ''
			)
		)
	);

	// Maj 0.1 : on essaye de récupérer la config mal rangée si elle existe
	$maj['0.1'] = array(
		array('paypal_maj_01')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin paypal.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function paypal_vider_tables($nom_meta_base_version){

	// On efface la version entregistrée
	include_spip('inc/config');
	effacer_meta($nom_meta_base_version);
	effacer_config('paypal');

}

/**
 * Fonction maj 0.1 : on essaye de récupérer la config mal rangée si elle existe
 *
 * @return void
**/
function paypal_maj_01 (){
	include_spip('inc/config');
	// config: "paypal_api_prod" devient "account_prod"
	$old_config = lire_config('paypal_api_prod') ;
	if (is_array($old_config) && $old_config['account']!='') {
		$config['account_prod'] = $old_config['account'];
	}
	// config: "paypal_api_test" devient "account_test"
	$old_config = lire_config('paypal_api_test') ;
	if (is_array($old_config) && $old_config['account']!='') {
		$config['account_test'] = $old_config['account'];
	}
	unset($config['api']) ;
	unset($config['soumission']) ;
	unset($config['tax']) ;
	effacer_config('paypal_api_prod');
	effacer_config('paypal_api_test');
}

?>

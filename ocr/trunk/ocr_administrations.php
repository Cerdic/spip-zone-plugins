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
		array('ocr_creer_config')
	);

	$maj['0.2'] = array(
		array('maj_tables', array('spip_documents')),
		array('ocr_creer_config'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de génération de configuration à l'installation
 * 
 * Si pas de configuration enregistrée, ajoute une configuration par défaut :
 * -* intervalle de 600s entre les lancements de CRON
 * -* 5 fichiers analysés par CRON
 * -* binaire de reconnaissance des caractères : /usr/bin/tesseract
 * -* options du binaire : -fra (modèle de langue : français)
 * 
 */
function ocr_creer_config(){
	include_spip('inc/config');
    if(!is_array(lire_config('ocr'))){
        $cfg = array(
            "intervalle_cron" => "600",
        	"nb_docs" => "5",
            "ocr_bin" => "/usr/bin/tesseract",
            "ocr_opt" => "-fra"
        );
		ecrire_config('ocr',$cfg);
    }
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

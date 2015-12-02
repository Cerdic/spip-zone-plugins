<?php
/**
 * Fonctions du plugin Composer
 *
 * @plugin     Composer
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Composer\Fonctions
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/composer_json');

/**
 * Calculer et générer le json de composer
 *
 * @uses Composer_JSON::get_json()
 * @uses ecrire_fichier
 * 
 * @pipeline_appel preparer_composer_json
 * @return bool True si écriture OK.
**/
function composer_generer_json() {
	$Composer = pipeline('preparer_composer_json', new Composer_JSON());
	$json = $Composer->get_json();
	sous_repertoire(_DIR_COMPOSER);
	$ok = ecrire_fichier(_FILE_COMPOSER_JSON, $json);
	return $ok;
}



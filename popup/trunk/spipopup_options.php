<?php
/**
 * @name 		JavascriptPopup_options
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 *
 * @todo gerer le retour : fermer la popup ou NON (option en +)
 * @todo plusieurs popups ?
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors','1'); error_reporting(E_ALL);
	
/**
 * Test de la nouveaute SPIP 2.1 : etendre l'aide de SPIP (ici pour l'aide du plugin)
 */
if (isset($GLOBALS['help_server']) && is_array($GLOBALS['help_server']))
	$GLOBALS['help_server'][] = url_de_base(1).str_replace("../", "", _DIR_PLUGIN_SPIPOPUP)."aide/";

/**
 * Liste des valeurs modifiables
 */
$GLOBALS['spipopup_data'] = array(
	'popup_skel','popup_titre','popup_width','popup_height', 'popup_options'
);

/**
 * Valeurs par defaut (modifiees par CFG)
 */
define('POPUP_SKEL_DEFAUT', 'popup_defaut.html'); // necessaire
define('POPUP_TITRE_DEFAUT', 'popup'); 						// necessaire
define('POPUP_WIDTH_DEFAUT', '620'); 							// necessaire
define('POPUP_HEIGHT_DEFAUT', '640'); 						// necessaire
define('POPUP_OPTIONS_DEFAUT', '');

/**
 * Renvoie la configuration courante du plugin (defaut + Config)
 *
 * On definit chaque entree de $GLOBALS['spipopup_data'] du genre :
 *     define VALEUR = X
 * ou X est :
 * -* la valeur utilisateur (provenant d'un meta edite sous CFG)
 * -* sinon la valeur par defaut definie ci-dessus, nommee
 *     VALEUR_DEFAULT
 * -* sinon nada
 */
function spipopup_config () {

	// On recupere la config courante (via CFG s'il est la)
	$conf = function_exists('lire_config') ? lire_config('spipopup') : null;
	if(is_null($conf)) $conf = false;

	// On boucle sur $GLOBALS['spipopup_data']
	foreach ( $GLOBALS['spipopup_data'] as $data ) {
		$definedata = strtoupper($data);
		$definedata_defaut = $definedata.'_DEFAUT';

		if (
			!defined("$definedata")
		) {
			eval("
				\$valeur = 
					(\$conf AND isset(\$conf[\$data]) AND strlen(\$conf[\$data])) ? 
						\$conf[\$data] : $definedata_defaut;
			");
			// On securise les squelettes (page=mlk pour mlk.html)
			$valeur = str_replace('.html','',$valeur);
			// On definit la constante
			eval("
				define('$definedata','$valeur');
			");
		}

	}

	// On retourne quoiqu'il arrive
	return;
}

?>
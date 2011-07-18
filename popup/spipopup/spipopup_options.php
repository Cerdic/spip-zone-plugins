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
 * Valeurs par defaut (modifiees par CFG)
 */
define('POPUP_SKEL_DEFAUT', 'popup_defaut.html');
define('POPUP_TITRE_DEFAUT', 'popup');
define('POPUP_WIDTH_DEFAUT', '620');
define('POPUP_HEIGHT_DEFAUT', '640');
$GLOBALS['spipopup_datas'] = array('popup_skel','popup_titre','popup_width','popup_height');

/**
 * Renvoie la configuration courante du plugin (defaut + Config)
 */
function spipopup_config(){
	$conf = function_exists('lire_config') ? lire_config('spipopup') : null;
	if(is_null($conf)) $conf = false;
	foreach($GLOBALS['spipopup_datas'] as $data){
		$definedata = strtoupper($data);
		$definedata_defaut = $definedata.'_DEFAUT';
		if(!defined("$definedata")){
			eval("\$valeur = (\$conf AND isset(\$conf[\$data]) AND strlen(\$conf[\$data])) ? \$conf[\$data] : $definedata_defaut;");
			// On securise les squelettes (page=mlk pour mlk.html)
			$valeur = str_replace('.html','',$valeur);
			eval("define('$definedata','$valeur');");
		}
	}
}

?>
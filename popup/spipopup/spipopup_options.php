<?php
/**
 * @name 		JavascriptPopup_options
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 *
 * @todo gerer le retour : fermer la popup ou NON (option en +)
 * @todo plusieurs popups ?
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
	
define('POPUP_SKEL_DEFAUT', 'popup_defaut.html');
define('POPUP_TITRE_DEFAUT', 'popup');
define('POPUP_WIDTH_DEFAUT', '620');
define('POPUP_HEIGHT_DEFAUT', '640');
$GLOBALS['spipopup_datas'] = array('popup_skel','popup_titre','popup_width','popup_height');

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
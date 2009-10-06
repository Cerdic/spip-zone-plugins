<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GFC_LOGIN_BUTTON($p) {
    return calculer_balise_dynamique($p, 'GFC_LOGIN_BUTTON', array());
}

function balise_GFC_LOGIN_BUTTON_dyn($url_retour = '') {
	if(function_exists('lire_config')){
		$cookie_name = lire_config('gfc/cookie_name') ? lire_config('gfc/cookie_name') : _GFC_COOKIE_NAME;
	}else{
		$cookie_name = _GFC_COOKIE_NAME;
	}
	//don't show button if already logged in, based on the Cookie check
	
	if(!isset($_COOKIE[$cookie_name]) OR !isset($GLOBAL['visiteur_session']['id_auteur'])){
		if($url_retour != '') $_SESSION["gfc"]["login_redirect"] = $url_retour;
		else $_SESSION["gfc"]["login_redirect"] = self();
			//display google friend login template
		return array('formulaires/gfc_login_button', 0, array());
	}
}

?>

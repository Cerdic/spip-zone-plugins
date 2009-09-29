<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GFC_LOGIN_BUTTON($p) {
    return calculer_balise_dynamique($p, 'GFC_LOGIN_BUTTON', array());
}

function balise_GFC_LOGIN_BUTTON_dyn($url_retour = '') {
	//don't show button if already logged in, based on the Cookie check
	if(isset($_COOKIE[$GLOBALS['gfc']['cookie_name']])) return;
	
	if($url_retour != '') $_SESSION["gfc"]["login_redirect"] = $url_retour;
	else $_SESSION["gfc"]["login_redirect"] = self();
	//display google friend login template
	return array('formulaires/gfc_login_button', 0, array( 
	));
}

?>

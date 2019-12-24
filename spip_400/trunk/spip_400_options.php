<?php
/**
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors','1'); error_reporting(E_ALL);

//
// Reprise de 'self' standard, sans rien enlever
//
function spip_400_self($amp = '&', $root = true) {
//	$url = nettoyer_uri();
	$url = $GLOBALS['REQUEST_URI'];

	// ajouter le cas echeant les variables _POST['id_...']
	foreach ($_POST as $v => $c)
		if (substr($v,0,3) == 'id_')
			$url = parametre_url($url, $v, $c, '&');

	// eviter les hacks
	$url = htmlspecialchars($url);

	// &amp; ?
	if ($amp != '&amp;')
		$url = str_replace('&amp;', $amp, $url);

	// Si ca demarre par / => vide
	$url = preg_replace(',^/(.*)?$,', '\1', $url);
	if (!preg_match(',^\w+:,', $url)) {
		$url = url_de_base().$url;
	}

	return $url;
}

/**
 * Ecriture en log specifique des erreurs HTTP
 */
function spip_400_log($code=400, $url=null) {

	$infos_log = array( $url );
	if (isset($GLOBALS["visiteur_session"]) && isset($GLOBALS["visiteur_session"]['id_auteur'])) {
		$infos_log[] = "User=[".$GLOBALS["visiteur_session"]['nom'].' - '.$GLOBALS["visiteur_session"]['email']." - stat. '".$GLOBALS["visiteur_session"]['statut']."']";
	}
	if (isset($_SERVER['HTTP_REFERER']))	
		$infos_log[] = "Referer=[".$_SERVER['HTTP_REFERER']."]";
	if (isset($_COOKIE['spip_session'])) {
		$infos_log[] = "Sess=[".$_COOKIE['spip_session']."]";
	}

	spip_log("[ERROR HTTP $code] ".join(' ', $infos_log), 'spip_error'._LOG_ERREUR);
	return;
}

?>
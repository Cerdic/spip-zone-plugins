<?php

/*
 * Filtre recaptcha pour proteger les emails
 * Auteur: (c) fil@rezo.net 2007
 * Licence GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://recaptcha.googlecode.com/files/recaptcha-php-1.11.zip
function get_recaptcha_keys($force = false) {
	if ($force
	OR !isset($GLOBALS['meta']['recaptcha_keys'])
	OR !is_array($keys = @unserialize($GLOBALS['meta']['recaptcha_keys']))) {

		spip_log('chargement d\'une cle API recaptcha');
		include_spip('inc/distant');
		include_spip('inc/texte');
		include_spip('inc/filtres');
		$u = translitteration(textebrut(recuperer_page('http://www.google.com/recaptcha/mailhide/apikey')));
		if(!preg_match(',Cl([^p]+)publique:(.*),i', $u, $r1) // La page retourne des accents qui (pour l'instant) sont errones, donc ne passent pas la translitteration
		OR !preg_match(',Cl([^p]+)prive:(.*),i', $u, $r2)) {
			spip_log('erreur recaptcha n\'a pas donne de cle : '.$u);
			return false;
		}

		$keys = array(
			'public' => trim($r1[2]),
			'private' => trim($r2[2])
		);
		
		ecrire_meta('recaptcha_keys', serialize($keys));
		ecrire_metas();
	} 
	
	return $keys;
}

function filtre_antispamrecaptcha($email) {
	if (strpos($email, '@') === false)
		return;

	if (!$keys = get_recaptcha_keys())
		return $email;

	require_once _DIR_PLUGIN_RECAPTCHA.'recaptchalib.php';
	return recaptcha_mailhide_html ($keys['public'], $keys['private'], $email);
#	return recaptcha_mailhide_url ($keys['public'], $keys['private'], $email);
}

// Si la fonction antispam existe deja, on ne fournit que antispam_recaptcha
if (!function_exists('filtre_antispam')) {
	function filtre_antispam($email) {
		return filtre_antispamrecaptcha($email);
	}
} else spip_log('filtre_antispam deja definie');

?>

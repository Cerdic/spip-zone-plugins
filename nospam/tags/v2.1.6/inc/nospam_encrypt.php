<?php
/**
 * Plugin No-SPAM
 * (c) 2008-2019 Cedric Morin Yterium&Nursit
 * Licence GPL
 *
 */



if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calculer une cle prive utilisee pour encoder les names des formulaires
 * pour etre non predictible,
 * on utilise un jeton fourni, le path du fichier qui depend du serveur et le secret_du_site()
 *
 * @param $jeton
 * @return string
 */
function nospam_private_key($jeton) {
	static $private_key;

	if (is_null($private_key)) {
		if (!function_exists('secret_du_site')) {
			include_spip('inc/securiser_action');
		}

		$private_key = $jeton . ":" . __FILE__ . ":" . secret_du_site();

		if (function_exists('sha1'))
			$private_key = sha1($private_key);
		else
			$private_key = md5($private_key);
		$private_key = pack("H*", $private_key);
	}

	return $private_key;
}

/**
 * Encoder un name avec un jeton
 * les names que l'on encode sont prefixes de xx_ avant encodage pour permettre une verif apres decodage
 * le name encode est lui prefixe de x_, pour eviter d'essayer de decoder brutalement tous les name
 *
 * @param string $name
 * @param string $jeton
 * @return string
 */
function nospam_name_encode($name, $jeton = "") {
	static $private_key = array();
	static $encoded = array();
	if (isset($encoded[$jeton][$name])) {
		return $encoded[$jeton][$name];
	}

	if (!$name) {
		return $name;
	}

	if (!isset($private_key[$jeton])) {
		$private_key[$jeton] = nospam_private_key($jeton);
		if (!function_exists('_xor'))
			include_spip("inc/filtres");
	}

	$cname = _xor("xx_$name", $private_key[$jeton]);
	$cname = base64_encode($cname);
	$cname = "x_" . rtrim(strtr(base64_encode($cname), '+/', '-_'), '=');
	return $encoded[$jeton][$name] = $cname;
}


/**
 * Decoder un name a partir d'un jeton
 *
 * @param string $name
 * @param string $jeton
 * @return string
 */
function nospam_name_decode($name, $jeton = "") {
	static $private_key = array();
	static $decoded = array();

	if (isset($decoded[$jeton][$name])) {
		return $decoded[$jeton][$name];
	}

	if (!$name) return $name;
	if (strncmp($name, "x_", 2) !== 0) return $name;
	if (!isset($private_key[$jeton])) {
		$private_key[$jeton] = nospam_private_key($jeton);
		if (!function_exists('_xor'))
			include_spip("inc/filtres");
	}

	$cname = substr($name, 2);
	$cname = base64_decode(str_pad(strtr($cname, '-_', '+/'), strlen($cname) % 4, '=', STR_PAD_RIGHT));
	$cname = base64_decode($cname);
	$cname = _xor($cname, $private_key[$jeton]);
	// si ce n'etait pas un name encode, on retourne le name d'origine
	if (strncmp($cname, "xx_", 3) !== 0) {
		return $decoded[$jeton][$name] = $name;
	}

	return $decoded[$jeton][$name] = substr($cname, 3);
}


/**
 * encrypter tous les names d'un form
 * @param string $form
 * @param bool $preserve_sessions_name
 *   si false les name commencant par session_ seront aussi encode
 * @param null|string $jeton
 * @param string $isbot
 * @return mixed
 */
function nospam_encrypt_form_names($form, $preserve_sessions_name = true, $jeton = null, $isbot = false) {
	// recuperer toutes les balises input, textarea, select
	$balises = array_merge(extraire_balises($form, 'input'));
	foreach ($balises as $k => $b) {
		if (in_array(extraire_attribut($b, "type"), array("hidden", "file")))
			unset($balises[$k]);
	}
	$balises = array_merge($balises,
		extraire_balises($form, 'textarea'),
		extraire_balises($form, 'select'));

	if (is_null($jeton)) {
		$jeton = "";
		if (preg_match(",<input type='hidden' name='_jeton' value='([^>]*)' />,Uims", $form, $m))
			$jeton = $m[1];
	}

	foreach ($balises as $k => $b) {
		if ($name = extraire_attribut($b, "name")
			AND (!$preserve_sessions_name or strncmp($name, "session_", 8) !== 0)) {
			// cas des truc[chose] : on ne brouille que truc
			$crypted_name = explode("[", $name);
			$crypted_name[0] = nospam_name_encode($crypted_name[0], $jeton);
			$crypted_name = implode("[", $crypted_name);
			$b_e = inserer_attribut($b, "name", $crypted_name);
			$form = str_replace($b, $b_e, $form);
		}
	}

	if ($isbot) {
		$form = str_replace(nospam_encrypt_html_hidden(), nospam_encrypt_html_checkbox(), $form);
	}

	return $form;
}

function nospam_encrypt_html_hidden() {
	return "<input type='hidden' name='_nospam_encrypt' value='1' />";
}
function nospam_encrypt_html_checkbox() {
	return "<label class='check_if_nobot'><input type='checkbox' name='_nospam_encrypt' value='1' /> " . _T('nospam:label_je_ne_suis_pas_un_robot') . "</label>";
}

/**
 * Verifier/preparer les valeurs sur un formulaire crypte
 * injecte un _encrypt en hidden, et gere le cas particulier ou l'on cohabite avec les fonctions autosave
 * @param $valeurs
 * @param $args
 * @return array
 */
function nospam_encrypt_check_valeurs($valeurs, $args) {
	$valeurs['_hidden'] .= nospam_encrypt_html_hidden();
	// recuperer les autosave encryptes si possible
	if (is_array($valeurs)
		AND isset($valeurs['_autosave_id'])
		AND $cle_autosave = $valeurs['_autosave_id']
		AND include_spip("inc/cvt_autosave")
		AND function_exists("autosave_clean_value")) {

		$je_suis_poste = $args['je_suis_poste'];

		$cle_autosave = serialize($cle_autosave);
		$cle_autosave = $args['form'] . "_" . md5($cle_autosave);

		// si on a un backup en session et qu'on est au premier chargement, non poste
		// on restitue les donnees
		if (isset($GLOBALS['visiteur_session']['session_autosave_' . $cle_autosave])
			AND !$je_suis_poste) {
			parse_str($GLOBALS['visiteur_session']['session_autosave_' . $cle_autosave], $vars);
			if (isset($vars['_jeton'])
				AND $jeton = $vars['_jeton']) {
				foreach ($vars as $name => $val) {
					if (($dname = nospam_name_decode($name, $jeton)) !== $name
						AND isset($valeurs[$dname]))
						$valeurs[$dname] = (is_string($val) ? autosave_clean_value($val) : array_map('autosave_clean_value', $val));
				}
			}
		}
	}

	return $valeurs;
}

/**
 * Decrypter les name d'un POST
 * n'agit qu'une seule fois
 * @param $form
 * @return bool|string
 *   string : message d'erreur
 *   bool : indique si des names ont ete decodes ou non
 */
function nospam_encrypt_decrypt_post($form) {
	static $deja = false;
	if ($deja) {
		return false;
	}

	// si l'encrypt a ete active depuis l'affichage initial de ce form, on rebalance l'erreur technique
	// pour reforcer un POST
	// si pas de _nospam_encrypt poste, on refuse la saisie => erreur
	if (!_request('_nospam_encrypt')) {
		spip_log("SPAM_ENCRYPT_NAME active mais _nospam_encrypt manquant sur formulaire $form", 'nospam' . _LOG_INFO_IMPORTANTE);
		$ua = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined');
		spip_log("Suspect SPAMMEUR (_nospam_encrypt) UA:$ua POST:" . json_encode($_POST), 'nospam_suspects' . _LOG_INFO_IMPORTANTE);
		return _T('nospam:erreur_jeton');
	}
	if (!$jeton = _request('_jeton')){
		spip_log("SPAM_ENCRYPT_NAME active mais _jeton manquant sur formulaire $form", 'nospam' . _LOG_INFO_IMPORTANTE);
		$ua = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined');
		spip_log("Suspect SPAMMEUR (_jeton) UA:$ua POST:" . json_encode($_POST), 'nospam_suspects' . _LOG_INFO_IMPORTANTE);
		return _T('nospam:erreur_jeton');
	}

	$deja = true;
	$re_verifier = false;
	foreach ($_POST as $k => $v) {
		$kd = nospam_name_decode($k, $jeton);
		if ($kd !== $k) {
			set_request($kd, $v);
			$re_verifier = true;
		}
	}

	if ($re_verifier){
		return true;
	}

	return false;
}
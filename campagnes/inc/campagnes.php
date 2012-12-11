<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Permet de récupérer les infos nécessaires aux enregistrements des vues et clics.
 *
 * @return Renvoie un tableau avec les informations utiles
 */
function campagnes_recuperer_infos_visiteur(){
	// Le visiteur a-t-il un cookie
	$nom_cookie = $GLOBALS['cookie_prefix'].'_campagne';
	$cookie = $_COOKIE[$nom_cookie];
	
	// S'il n'y a pas de cookie on en crée un et on l'enregistre
	if (!$cookie) {
		include_spip("inc/acces");
		include_spip("inc/cookie");
		$cookie = creer_uniqid();
		// Expiration dans 30 jours
		spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time() + 30 * 24 * 3600);
	}
	
	// Le visiteur est-il un auteur ?
	include_spip('inc/session');
	if (!$id_auteur = session_get('id_auteur')) $id_auteur = 0;
	
	return array(
		'id_auteur' => $id_auteur,
		'cookie' => $cookie,
		'ip' => $GLOBALS['ip']
	);
}

/**
 * Recuperer les champs date_xx, verifier leur coherence et les reformater
 *
 * @param string $suffixe
 * @param array $erreurs
 * @return int
 */
function campagnes_verifier_date_saisie($suffixe, &$erreurs){
	include_spip('inc/filtres');
	$date = _request("date_$suffixe");
	$date = recup_date($date);
	if (!$date)
		return '';
	$ret = null;
	if (!$ret = mktime(0, 0, 0, $date[1], $date[2], $date[0]))
		$erreurs["date_$suffixe"] = _T('campagne:erreur_date');
	if ($ret){
		if (trim(_request("date_$suffixe") !== ($d=date('d/m/Y', $ret)))){
			$erreurs["date_$suffixe"] = _T('campagne:erreur_date_corrigee');
			set_request("date_$suffixe", $d);
		}
	}
	return $ret;
}

/*
 * Teste quel statut devra avoir une publicité, suivant son statut actuel et ses restrictions de publication
 *
 * @param int $id_campagne
 * @return string Retourne le nouveau statut à instituer
 */
function campagnes_tester_publication(){
	
}

?>

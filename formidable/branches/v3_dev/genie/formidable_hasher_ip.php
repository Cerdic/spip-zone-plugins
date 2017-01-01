<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
/**
* CNIL -- Informatique et libertes
*
* masquer le numero IP des vieilles réponses
* date de reference = 4 mois
* definir a 0 pour desactiver
* même valeur par défaut que pour les forums
**/
function genie_formidable_hasher_ip_dist($t){
	if (!defined('_CNIL_PERIODE')) {
		define('_CNIL_PERIODE', 3600*24*31*4);
	}
	
	if (_CNIL_PERIODE) {
		$critere_cnil = 'date<"'.date('Y-m-d', time()-_CNIL_PERIODE).'"'
			. ' AND statut != "spam"'
			. ' AND (ip LIKE "%.%" OR ip LIKE "%:%")'; # ipv4 ou ipv6
		$c = sql_countsel('spip_formulaires_reponses', $critere_cnil);
		if ($c>0) {
			spip_log("CNIL: masquer IP de $c réponses anciennes à formidable", "formidable");
			sql_update('spip_formulaires_reponses', array('ip' => 'MD5(ip)'), $critere_cnil);
			return $c;
		}
	}
	return 0;
}

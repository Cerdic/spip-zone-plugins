<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function facteur_affiche_password_masque($pass){
	$l = strlen($pass);
	if ($l<=8){
		return str_pad('',$l,'*');
	}
	$e = intval(ceil($l/10));
	$mid = str_pad('',$l-2*$e,'*');
	if (strlen($mid)>8){
		$mid = '***...***';
	}
	return substr($pass,0,$e) . $mid . substr($pass,-$e);
}

/**
 * Un filtre pour transformer les retour ligne texte en br si besoin (si pas autobr actif)
 *
 * @param string $texte
 * @return string
 */
function facteur_nl2br_si_pas_autobr($texte){
	if (_AUTOBR) return $texte;
	include_spip("inc/filtres");
	$texte = post_autobr($texte);
	return $texte;
}



/**
 * voir inc/facteur_mail_wrap_to_html
 *
 * @param string $texte_ou_html
 * @return string
 */
function facteur_email_wrap_to_html($texte_ou_html){

	$facteur_mail_wrap_to_html = charger_fonction('facteur_mail_wrap_to_html', 'inc');
	return $facteur_mail_wrap_to_html($texte_ou_html);
}

/**
 * voir inc/facteur_convertir_styles_inline
 *
 * @param string $body
 * @return string
 */
function facteur_convertir_styles_inline($body){

	$facteur_convertir_styles_inline = charger_fonction('facteur_convertir_styles_inline', 'inc');
	return $facteur_convertir_styles_inline($body);
}


/**
 * voir inc/facteur_mail_html2text
 * @param string $html
 * @return string
 */
function facteur_mail_html2text($html){

	$facteur_mail_html2text = charger_fonction('facteur_mail_html2text', 'inc');
	return $facteur_mail_html2text($html);
}


/**
 * Insertion dans le pipeline formulaire_fond (SPIP)
 *
 * On indique dans le formulaire de configuration de l'identité du site
 * que facteur surchargera l'email configuré ici pour envoyer les emails
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifé
 */
function facteur_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'configurer_identite'
	  and include_spip('inc/config')
	  and lire_config('facteur/adresse_envoi') === 'oui'
	  and strlen($email = lire_config('facteur/adresse_envoi_email', '')) ) {
		$url = generer_url_ecrire('configurer_facteur');
		$ajout = '<p class="notice" style="margin-top:0">'._T('facteur:message_identite_email', array('url' => $url, 'email' => $email)).'</p>';
		if (preg_match(",<(div|li) [^>]*class=[\"']editer editer_email_webmaster.*>,Uims", $flux['data'], $match)) {
			$p = strpos($flux['data'], $match[0]);
			$p = strpos($flux['data'], "<input", $p);
			$p = strpos($flux['data'], "</".$match[1], $p);
			$flux['data'] = substr_replace($flux['data'], $ajout, $p, 0);
		}
	}
	return $flux;
}

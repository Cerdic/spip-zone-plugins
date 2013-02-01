<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification du formulaire de configuration
 * Vérifie les données numériques
 * @param unknown_type $cfg
 */
function cfg_config_mediaspip_player_verifier(&$cfg){
	$numeriques = array('video_largeur_embed','video_hauteur_embed');
	foreach($numeriques as $numerique){
		if($cfg->val[$numerique] && !ctype_digit($cfg->val[$numerique])){
			$erreur[$numerique] = _T('mediaspip_player:erreur_valeur_int');
		}
		if(!$erreur[$numerique] && $cfg->val[$numerique] && ($cfg->val[$numerique] > 2000)){
			$erreur[$numerique] = _T('mediaspip_player:erreur_valeur_int_inf',array('nb'=>'2000'));
		}
	}
	if(!$erreur['video_largeur_embed'] && $cfg->val['video_largeur_embed'] && ($cfg->val['video_largeur_embed'] < 200)){
		$erreur['video_largeur_embed'] = _T('mediaspip_player:erreur_valeur_int_sup',array('nb'=>'200'));
	}
	return $erreur;
}

/**
 * Fonction de post-traitement du formulaire de configuration
 * Vide le cache JS
 * @param unknown_type $cfg
 */
function cfg_config_mediaspip_player_post_traiter(&$cfg){
	include_spip('inc/invalideur');
	$rep_js = _DIR_VAR.'cache-js/';
	purger_repertoire($rep_js);
}

?>
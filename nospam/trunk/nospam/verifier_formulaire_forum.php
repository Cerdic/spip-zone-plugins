<?php

/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Verification supplementaire antispam sur le formulaire_forum
 *
 * @param array $flux
 * @return array
 */
function nospam_verifier_formulaire_forum_dist($flux){
	$form = $flux['args']['form'];
	if (!isset($flux['data']['texte'])
		AND $GLOBALS['meta']['forums_texte'] == 'oui'){

		$texte = _request('texte');
		include_spip("inc/nospam");
		// regarder si il y a du contenu en dehors des liens !
		$caracteres = compter_caracteres_utiles($texte);
		$min_length = (defined('_FORUM_LONGUEUR_MINI') ? _FORUM_LONGUEUR_MINI : 10);
		if ($caracteres < $min_length){
			$flux['data']['texte'] = _T('forum_attention_dix_caracteres');
		}

		// regarder si il y a du contenu cache
		if (!isset($flux['data']['texte'])){
			$infos = analyser_spams($texte);
			if (isset($infos['contenu_cache']) AND $infos['contenu_cache']){
				$flux['data']['texte'] = _T('nospam:erreur_attributs_html_interdits');
			}
		}

		// regarder si il y a des liens deja references par des spammeurs
		if (!isset($flux['data']['texte'])
		  AND isset($infos['liens'])
		  AND count($infos['liens'])){


			if ($h = rechercher_presence_liens_spammes($infos['liens'],_SPAM_URL_MAX_OCCURENCES,'spip_forum',array('texte'))){
				spip_log("Refus message de forum qui contient un lien vers $h","nospam");
				$flux['data']['texte'] = _T('nospam:erreur_url_deja_spammee');
			}
		}

		// on prend en compte la checkbox de confirmation
		// si le flag en session est bien leve
		if (_request('notabuse')){
			session_start();
			if ($_SESSION['notabuse_check']){
				unset($_SESSION['notabuse_check']);
				$_SESSION['notabuse_checked'] = true;
				// on leve une globale pour la fin de ce hit, a toute fin utile (puisque plus rien en $_SESSION)
				$GLOBALS['notabuse_checked'] = true;
			}
		}
		if (!count($flux['data'])){
			if (nospam_check_ip_status($GLOBALS['ip'])!=='ok'){
				session_start();
				if ($_SESSION['notabuse_checked']){
					// ok on retire de la session le check qui ne sert qu'une fois
					unset($_SESSION['notabuse_checked']);
					// et on laisse passer
				}
				else {
					$flux['data']['texte'] = _T('nospam:info_ip_suspecte')."<br />
					<span class='choix'>
					<input type='checkbox' name='notabuse' value='1' id='notabuse'/> <label for='notabuse'>"
					._T('nospam:label_message_licite')."</label>
					</span>";
					$_SESSION['notabuse_check'] = true;
					spip_log("notabuse_check sur IP ".$GLOBALS['ip'],"nospam");
				}
			}
		}


		// si il y a une erreur, pas de previsu, on reste bloque a la premiere etape
		if (isset($flux['data']['texte'])){
			unset($flux['data']['previsu']);
		}
		// sinon, si on est au moment du post final (confirmation apres previsu => pas de previsu ni d'erreur)
		// on calcule la "popuparlite de post"
		elseif(!count($flux['data'])) {
			$now = $_SERVER['REQUEST_TIME'];
			// calculer la "popularite" des POST forums et forums avec liens
			if (!isset($GLOBALS['meta']['nospam_pop_forum_post'])) $GLOBALS['meta']['nospam_pop_forum_post'] = 0;
			if (!isset($GLOBALS['meta']['nospam_pop_forum_postwlink'])) $GLOBALS['meta']['nospam_pop_forum_postwlink'] = 0;
			if (!isset($GLOBALS['meta']['nospam_pop_date'])) $GLOBALS['meta']['nospam_pop_date'] = date('Y-m-d H:i:s',$now);

			$duree = max($now-strtotime($GLOBALS['meta']['nospam_pop_date']),1);
			list($a,$b) = nospam_popularite_constantes($duree);
			spip_log("Pop forum : $duree, $a, $b","nospam");
			// decrementer
			if ($duree>1800){
				$GLOBALS['meta']['nospam_pop_date'] = date('Y-m-d H:i:s',$now);
				$GLOBALS['meta']['nospam_pop_forum_post'] = round(floatval($GLOBALS['meta']['nospam_pop_forum_post'])*$a,2);
				$GLOBALS['meta']['nospam_pop_forum_postwlink'] = round(floatval($GLOBALS['meta']['nospam_pop_forum_postwlink'])*$a,2);
				spip_log("Pop Decremente : ".$GLOBALS['meta']['nospam_pop_forum_post'].", ".$GLOBALS['meta']['nospam_pop_forum_postwlink'],"nospam");
			}
			// incrementer
			$GLOBALS['meta']['nospam_pop_forum_post']=round(floatval($GLOBALS['meta']['nospam_pop_forum_post'])+$b,2);
			if (isset($infos['liens']) AND count($infos['liens'])){
				$GLOBALS['meta']['nospam_pop_forum_postwlink']=round(floatval($GLOBALS['meta']['nospam_pop_forum_postwlink'])+$b,2);
			}
			ecrire_meta("nospam_pop_forum_post",$GLOBALS['meta']['nospam_pop_forum_post']);
			ecrire_meta("nospam_pop_forum_postwlink",$GLOBALS['meta']['nospam_pop_forum_postwlink']);
			ecrire_meta("nospam_pop_date",$GLOBALS['meta']['nospam_pop_date']);
			spip_log("Pop Incremente : ".$GLOBALS['meta']['nospam_pop_forum_post'].", ".$GLOBALS['meta']['nospam_pop_forum_postwlink'],"nospam");
		}
	}

	return $flux;
}

//
// Popularite, modele logarithmique
//
function nospam_popularite_constantes($duree){
	// duree de demi-vie d'une visite dans le calcul de la popularite (en jours)
	$demivie = 0.5;
	// periode de reference en jours
	$periode = 1;
	// $a est le coefficient d'amortissement depuis la derniere mesure
	$a = pow(2, - $duree / ($demivie * 24 * 3600));
	// $b est la constante multiplicative permettant d'avoir
	// une visite par jour (periode de reference) = un point de popularite
	// (en regime stationnaire)
	// or, magie des maths, ca vaut log(2) * duree journee/demi-vie
	// si la demi-vie n'est pas trop proche de la seconde ;)
	$b = log(2) * $periode / $demivie;

	return array($a,$b);
}

<?php
/*
 * Plugin Emballe medias
 * (c) 2009-2013 SPIP
 * Distribue sous licence GPL
 *
 * Modération des medias proposés par email
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_media_paremail_dist() {

	// verification manuelle de la signature : cas particulier de cette action signee par email
	$arg = _request('arg');
	$hash = _request('hash');

	include_spip("inc/securiser_action");
	$action = 'instituer_media_paremail';
	$pass = secret_du_site();

	$verif = _action_auteur("$action-$arg", '', $pass, 'alea_ephemere');

	$ae = explode("-",$arg);
	$id_article = array_shift($ae);
	$statut = array_shift($ae);
	$statut_init = array_shift($ae);
	// l'email est ce qui reste
	$email = implode("-",$ae);
	$media = null;
	$erreur_auteur = _T('emballe_medias:info_moderation_media_interdite');

	include_spip("inc/filtres");
	$lien_moderation = lien_ou_expose(url_absolue(generer_url_entite($id_article,'article',"","",false)),_T('emballe_medias:info_moderation_media_lien_titre'));
	$erreur = _T('emballe_medias:info_moderation_media_url_perimee')."<br />$lien_moderation";

	if ($hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere')
	  OR $hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere_ancien'))
		$erreur = "";
	else {
		// le hash est invalide, mais peut-etre est-on loge avec cet email ?
		// auquel cas on peut utiliser les liens, meme perimes (confort)
		if (isset($GLOBALS['visiteur_session'])
		  AND $GLOBALS['visiteur_session']['id_auteur']
			AND $GLOBALS['visiteur_session']['email']==$email){
			$media = sql_fetsel("*","spip_articles","id_articles=".intval($id_article));
			if (autoriser("instituer","article",$id_article)){
				$erreur_auteur = "";
				$erreur = "";
			}
		}
		else
			spip_log("Signature incorrecte pour $arg","moderationparemail"._LOG_INFO_IMPORTANTE);
	}

	// si hash est ok, verifier si l'email correspond a un auteur qui a le droit de faire cette action
	if (!$erreur){

		if (!$media)
			$media = sql_fetsel("*","spip_articles","id_article=".intval($id_article));

		// on recherche le message en verifiant qu'il a bien le statut
		if ($media){
			if ($media['statut']!=$statut_init){
				$erreur = _T("emballe_medias:info_moderation_media_deja_faite",array('id_article'=>$id_article,'statut'=>$media['statut']))
					."<br />$lien_moderation";
			}
			else {
				// trouver le(s) auteur(s) et verifier leur autorisation si besoin
				if ($erreur_auteur){
					$res = sql_select("*","spip_auteurs","email=".sql_quote($email,'','text'));
					while ($auteur = sql_fetch($res)){
						if (autoriser("instituer","article",$id_article,$auteur)){
							$erreur_auteur = "";
							// on ajoute l'exception car on est pas identifie avec cet id_auteur
							autoriser_exception("instituer","article",$id_article);
							break;
						}
					}
				}
				if ($erreur_auteur){
					$erreur = $erreur_auteur 
					  . "<br /><small>"
					  . _L("(aucun auteur avec l'email $email n'a de droit suffisant)")
					  . "</small>";
					spip_log("Aucun auteur pour $email autorise a moderer $id_article","moderationparemail"._LOG_INFO_IMPORTANTE);
				}
			}
		}
		else {
			spip_log("Media $id_article introuvable","moderationparemail"._LOG_INFO_IMPORTANTE);
			$erreur = "Media $id_article introuvable"; // improbable ?
		}
	}

	if (!$erreur){
		spip_log("Moderation media $id_article $statut par $email","moderationparemail"._LOG_INFO_IMPORTANTE);
		$instituer_article = charger_fonction("instituer_objet","action");
		$instituer_article('article-'.$id_article.'-'.$statut);
	}

	// Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	$titre = (!$erreur ? _T("emballe_medias:info_moderation_media_confirmee_$statut",array('id_article'=>$id_article)) : $erreur);
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}

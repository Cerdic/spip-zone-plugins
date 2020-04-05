<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Action de modération par email
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de modération de signalement par email
 * Les administrateurs reçoivent un email avec un lien permettant de refuser/invalider un 
 * signalement
 */
function action_instituer_signalement_paremail_dist() {
	
	include_spip("inc/securiser_action");
	include_spip("inc/filtres");
	
	/**
	 * Verification manuelle de la signature : cas particulier de cette action signee par email
	 */ 
	$arg = _request('arg');
	$hash = _request('hash');

	$action = 'instituer_signalement_paremail';
	$pass = secret_du_site();

	$verif = _action_auteur("$action-$arg", '', $pass, 'alea_ephemere');

	$ae = explode("-",$arg);
	$id_signalement = array_shift($ae);
	$statut = array_shift($ae);
	$statut_init = array_shift($ae);
	/**
	 * l'email est ce qui reste
	 */
	$email = implode("-",$ae);
	$signalement = null;
	$erreur_auteur = _T('notifications:info_moderation_interdite');
	
	$lien_moderation = lien_ou_expose(url_absolue(generer_url_entite($id_signalement,'signalement',"","signalement$id_signalement",false)),_T('signalement:info_moderation_lien_titre'));
	$erreur = _T('notifications:info_moderation_url_perimee')."<br />$lien_moderation";

	/**
	 * Vérification de la validité du hash de l'action
	 */
	if ($hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere')
	  OR $hash==_action_auteur("$action-$arg", '', $pass, 'alea_ephemere_ancien'))
		$erreur = "";
	else {
		/**
		 * Le hash est invalide, mais peut-etre est-on loge avec cet email ?
		 * auquel cas on peut utiliser les liens, meme perimes (confort)
		 */ 
		if (isset($GLOBALS['visiteur_session'])
		  AND $GLOBALS['visiteur_session']['id_auteur']
			AND $GLOBALS['visiteur_session']['email']==$email){
			$signalement = sql_fetsel("id_objet,objet,statut","spip_signalement","id_signalement=".intval($id_signalement));
			if (autoriser("moderersignalement",$signalement['objet'],$signalement['id_objet'])){
				$erreur_auteur = "";
				$erreur = "";
			}
		}
		else
			spip_log("Signature incorrecte pour $arg - Moderation de signalement","notifications"._LOG_INFO_IMPORTANTE);
	}

	/**
	 * si hash est ok, verifier si l'email correspond a un auteur qui a le droit de faire cette action
	 */ 
	if (!$erreur){
		/**
		 * reconstituer l'arg pour l'action standard
		 */
		$arg = "$id_signalement-$statut";

		if (!$signalement)
			$signalement = sql_fetsel("id_objet,objet,statut","spip_signalements","id_signalement=".intval($id_signalement));

		/**
		 * on recherche le signalement en verifiant qu'il a bien le statut
		 */ 
		if ($signalement){
			if ($signalement['statut']!=$statut_init){
				$erreur = _T("signalement:info_moderation_deja_faite",array('id_signalement'=>$id_signalement,'statut'=>$signalement['statut']))
					."<br />$lien_moderation";
			}
			else {
				/**
				 * trouver le(s) auteur(s) et verifier leur autorisation si besoin
				 */ 
				if ($erreur_auteur){
					$res = sql_select("*","spip_auteurs","email=".sql_quote($email,'','text'));
					while ($auteur = sql_fetch($res)){
						if (autoriser("moderersignalement",$signalement['objet'],$signalement['id_objet'],$auteur)){
							$erreur_auteur = "";
							/**
							 * on ajoute l'exception car on est pas identifie avec cet id_auteur
							 */ 
							autoriser_exception("moderersignalement",$signalement['objet'],$signalement['id_objet']);
							break;
						}
					}
				}
				if ($erreur_auteur){
					$erreur = $erreur_auteur 
					  . "<br /><small>"
					  . _T('signalement:info_moderation_email_droit_insuffisant',array('email'=>$email))
					  . "</small>";
					spip_log("Aucun auteur pour $email autorise a moderer $id_signalement","moderationsignalementparemail"._LOG_INFO_IMPORTANTE);
				}
			}
		}
		else {
			spip_log("Signalement $id_signalement introuvable","moderationsignalementparemail"._LOG_INFO_IMPORTANTE);
			$erreur = _T('signalement:info_moderation_signalement_introuvable',array('id'=>$id_signalement)); // improbable ?
		}
	}

	if (!$erreur){
		spip_log("Moderation signalement $id_signalement $statut par $email","moderationsignalementparemail"._LOG_INFO_IMPORTANTE);
		$instituer_signalement = charger_fonction("instituer_signalement","action");
		$instituer_signalement($arg);
	}

	/**
	 * Dans tous les cas on finit sur un minipres qui dit si ok ou echec
	 */ 
	$titre = (!$erreur ? _T("signalement:info_moderation_confirmee_$statut",array('id_signalement'=>$id_signalement)) : $erreur);
	include_spip('inc/minipres');
	echo minipres($titre,"","",true);

}

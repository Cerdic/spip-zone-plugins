<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 * 
 * Formulaire de demande d'effacement de compte
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des valeurs par défaut du formulaire
 */
function formulaires_supprimer_visiteur_charger_dist(){
	$valeurs = array();

	/**
	 * On trouve la correspondance entre le code envoyé dans l'url et un compte utilisateur 
	 * éventuellement à supprimer
	 */
	if ($p=_request('s')) {
		$p = preg_replace(',[^0-9a-f.],i','',$p);
		if ($p AND $row = sql_fetsel(array('id_auteur','nom','email','statut','webmestre'),'spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'5poubelle'")))
			$valeurs['_hidden'] = '<input type="hidden" name="s" value="'.$p.'" />';
	}
	
	/**
	 * Si on a un compte valide pour le code fournit en url :
	 * - On doit être connecté pour supprimer le compte;
	 * - On doit être au minimum administrateur si la session actuelle n'est pas la même que le compte à supprimer;
	 * - On ne peut pas supprimer un compte webmestre;
	 * - Si ces conditions sont remplies, on ajoute dans l'environnement les informations nécessaires;
	 */
	if ($row['id_auteur']){
		if(!intval($GLOBALS['visiteur_session']['id_auteur'])){
			$valeurs['message_erreur'] = _T('inscription3:erreur_suppression_compte_connecte');
		}else if(($row['id_auteur'] != $GLOBALS['visiteur_session']['id_auteur']) && $GLOBALS['visiteur_session']['statut'] != '0minirezo'){
			$valeurs['message_erreur'] = _T('inscription3:erreur_suppression_compte_non_auteur');
		}else if($row['webmestre'] == 'oui' && $row['statut'] == '0minirezo'){
			$valeurs['message_erreur'] = _T('inscription3:erreur_suppression_compte_webmestre');
		}else{
			$valeurs['id_auteur'] = $row['id_auteur']; // a toutes fins utiles pour le formulaire
			$valeurs['nom'] = $row['nom'];
			$valeurs['email'] = $row['email'];
		}
	}
	/**
	 * Si pas d'auteur trouvé, on affiche un message comme quoi le code n'est pas valide
	 */
	else {
		$valeurs['message_erreur'] = _T('pass_erreur_code_inconnu');
	}
	
	/**
	 * Si un message d'erreur envoyé, on n'affiche pas le formulaire, mais juste le message
	 */
	if(isset($valeurs['message_erreur'])){
		$valeurs['editable'] =  false;
	}

	return $valeurs;

}

/**
 * Vérification du formulaire
 */
function formulaires_supprimer_visiteur_verifier_dist(){
	$erreurs = array();

	if ($p=_request('s')) {
		if ($id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut='0minirezo'")))
			$erreurs['oubli'] =  _T('inscription3:erreur_effacement_auto_impossible');

	}else{
		$erreurs['inconnu'] = _T('inscription3:erreur_effacement_auto_impossible');
	}

	return $erreurs;
}

/***
 * Traitement du formulaire
 */
function formulaires_supprimer_visiteur_traiter_dist(){

	if ($p=_request('s')) {
		if ($id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'0minirezo'")))
			$erreurs['oubli'] = _T('inscription3:erreur_effacement_auto_impossible');

	}else
		$erreurs['inconnu'] = _T('inscription3:erreur_effacement_auto_impossible');

	//supprimer un auteur
	$row = sql_getfetsel("statut","spip_auteurs","id_auteur=".intval($id_auteur));

	if($row['statut'] !='0minirezo' and $row['statut'] !='1comite')
		sql_delete("spip_auteurs","id_auteur=".intval($id_auteur));

	$message = _T('inscription3:message_compte_efface');
	return array('message_ok' => $message);
}

?>

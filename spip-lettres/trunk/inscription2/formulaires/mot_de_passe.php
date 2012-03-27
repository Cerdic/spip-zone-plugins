<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
/**
 * Chargement de l'auteur qui peut changer son mot de passe.
 * Soit un cookie d'oubli fourni par #FORMULAIRE_OUBLI est passe dans l'url par &p=
 * Soit un id_auteur est passe en parametre #FORMULAIRE_MOT_DE_PASSE{#ID_AUTEUR}
 * Dans les deux cas on verifie que l'auteur est autorise
 *
 * @param int $id_auteur
 * @return array
 */
function formulaires_mot_de_passe_charger_dist($id_auteur=null){

	$valeurs = array();
	if ($id_auteur=intval($id_auteur)) {
		$id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('id_auteur='.intval($id_auteur),"statut<>'5poubelle'","pass<>''"));
	}
	elseif ($p=_request('p')) {
		$p = preg_replace(',[^0-9a-f.],i','',$p);
		if ($p AND $id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'5poubelle'")))
			$valeurs['_hidden'] = '<input type="hidden" name="p" value="'.$p.'" />';
	}

	if ($id_auteur){
		$valeurs['id_auteur'] = $id_auteur; // a toutes fins utiles pour le formulaire
	}
	else {
		$valeurs['_hidden'] = _T('pass_erreur_code_inconnu');
		$valeurs['editable'] =  false; // pas de saisie
	}
	return $valeurs;
}

/**
 * Verification de la saisie du mot de passe.
 * On verifie qu'un mot de passe est saisi, et que sa longuer est suffisante
 * Ce serait le lieu pour verifier sa qualite (caracteres speciaux ...)
 *
 * @param int $id_auteur
 */
function formulaires_mot_de_passe_verifier_dist($id_auteur=null){
	$pass_min = !defined('_PASS_MIN')?6:_PASS_MIN;
	$erreurs = array();
	if (!_request('oubli'))
		$erreurs['oubli'] = _T('info_obligatoire');
	else if (strlen(_request('oubli')) < $pass_min)
		$erreurs['oubli'] = _T('info_passe_trop_court');

	return $erreurs;
}

/**
 * Modification du mot de passe d'un auteur.
 * Utilise le cookie d'oubli fourni en url ou l'argument du formulaire pour identifier l'auteur
 *
 * @param int $id_auteur
 */
function formulaires_mot_de_passe_traiter_dist($id_auteur=null){
	$message = '';
	include_spip('base/abstract_sql');
	if ($id_auteur=intval($id_auteur)) {
		$row = sql_fetsel('id_auteur,login,statut','spip_auteurs',array('id_auteur='.intval($id_auteur),"statut<>'5poubelle'","pass<>''"));
	}
	elseif ($p=_request('p')) {
		$p = preg_replace(',[^0-9a-f.],i','',$p);
		$row = sql_fetsel('id_auteur,email,login,statut','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'5poubelle'"));
	}

	if ($row
	 && ($id_auteur = $row['id_auteur'])
	 && ($oubli = _request('oubli'))) {
		include_spip('inc/acces');
		$mdpass = md5($oubli);
		$htpass = generer_htpass($oubli);
		if($row['statut'] == 'aconfirmer'){
			$statut = lire_config('inscription2/statut_nouveau');
		}else{
			$statut = $row['statut'];
		}
		include_spip('base/abstract_sql');
		sql_updateq('spip_auteurs', array('htpass' =>$htpass, 'pass'=>$mdpass, 'alea_actuel'=>'', 'cookie_oubli'=>'', 'statut'=> $statut), "id_auteur=" . intval($id_auteur));

		$email = $row['email'];
		$affordance = lire_config('inscription2/affordance_form','login');

		if($affordance == 'login'){
			$message = _T('pass_nouveau_enregistre').
			"<p>" . _T('pass_rappel_login', array('login' => $login));
		}else if($affordance == 'email'){
			$message = _T('pass_nouveau_enregistre').
			"<p>" . _T('inscription2:pass_rappel_email', array('email' => $email));
		}else{
			$message = _T('pass_nouveau_enregistre').
			"<p>" . _T('inscription2:pass_rappel_login_email', array('email' => $email,'login'=>$row['login']));
		}
	}
	return array('message_ok'=>$message);
}
?>

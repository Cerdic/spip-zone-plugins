<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');

/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_plan_charger_dist($id_plan='') {
	/* le nom de la table ne se termine pas par un s, on ne peut donc pas utiliser formulaires_editer_objet_charger */
	/* on charge donc dans $contexte tous les champs necessaires */
	if ($id_plan) { /* edition, il faut recuperer les valeurs dans la table */
		$plans = sql_fetsel("*", "spip_asso_plan", "id_plan=$id_plan");

		$contexte['classe'] = $plans['classe'];
		$contexte['code'] = $plans['code'];
		$contexte['intitule'] = $plans['intitule'];
		$contexte['reference'] = $plans['reference']; // ce champ la est appele a disparaitre
		$contexte['solde_anterieur'] = $plans['solde_anterieur'];
		$contexte['date_anterieure'] = $plans['date_anterieure'];
		$contexte['type_op'] = $plans['type_op'];
		$contexte['active'] = $plans['active'];
		$contexte['commentaire'] = $plans['commentaire'];
	} else { /* c'est une creation */
		$contexte['classe']=$contexte['code']=$contexte['intitule']=$contexte['reference']=$contexte['solde_anterieur']=$contexte['commentaire']='';
		/* par defaut les nouveaux comptes sont actives et multidirectionnels */
		$contexte['active'] = true;
		$contexte['type_op'] = 'multi';
		$contexte['date_anterieure'] = date('Y-m-d');
	}

	/* pour passer securiser action */
	$contexte['_action'] = array("editer_asso_plan",$id_plan);

	return $contexte;
}

function formulaires_editer_asso_plan_verifier_dist($id_plan='') {
	$erreurs = array();

	/* on verifie que le code est bien de la forme chiffre-chiffre-caracteres alphanumeriques et que le premier digit correspond a la classe */
	$classe = _request('classe');
	$code = _request('code');
	if ((!preg_match("/^[0-9]{2}\w*$/", $code)) || ($code[0] != $classe)){
		$erreurs['code'] = _T('asso:erreur_plan_code');
	}

	/* verifier la date */
	if ($erreur_date = association_verifier_date(_request('date_anterieure'))) {
		$erreurs['date_anterieure'] = _request('date_anterieure')."&nbsp;:&nbsp;".$erreur_date; /* on ajoute la date eronee entree au debut du message d'erreur car le filtre affdate corrige de lui meme et ne reaffiche plus les valeurs eronees */
	}


	if (!array_key_exists("code",$erreurs)) { /* si le code est valide */
		/* verifier que le code n'est pas deja attribue a une ligne du plan ou si il l'est que c'est a celle qu'on edite */
		if ($r = sql_fetsel('code,id_plan', 'spip_asso_plan', "code=$code")) {
			if ($r['id_plan']!=$id_plan) {
				$erreurs['code'] = _T('asso:erreur_plan_code_duplique');
			}
		}
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	return $erreurs;
}

function formulaires_editer_asso_plan_traiter_dist($id_plan='') {
	/* partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php */
	$res=array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	$action_cotisation = charger_fonction('editer_asso_plan','action');
	list($id_plan,$err) = $action_cotisation($id_plan);
	if ($err OR !$id_plan) {
		$res['message_erreur'] = ($err?$err:_T('erreur'));
	} else {
		$res['message_ok'] = '';
		$res['redirect'] = generer_url_ecrire('plan_comptable'); /* on renvoit sur la page adherents mais on perd a l'occasion d'eventuel filtres inseres avant d'arriver au formulaire de cotisation... */
	}

	return $res;
}
?>

<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_plan_charger_dist($id_plan='') {
	if ($id_plan) { // edition, il faut recuperer les valeurs dans la table. le nom de table ne se terminant pas par s, on ne peut pas utiliser formulaires_editer_objet_charger (voir si c'est maintenant possible avec la declaration des exceptions)
		$contexte = sql_fetsel('*', 'spip_asso_plan', "id_plan=$id_plan");
#		$contexte['classe'] = $plans['classe'];
#		$contexte['code'] = $plans['code'];
		$contexte['_hidden'] = "<input type='hidden' name='code_initial' value='$contexte[code]' />"; // on passe aussi le code originellement present pour detecter un changement de code a repercuter dans la table des comptes sans avoir a refaire de requete
	} else { // c'est une creation
		$contexte['classe'] = $contexte['code'] = $contexte['intitule'] = '';
		$contexte['solde_anterieur'] = 0;
		$contexte['commentaire'] = '';
		$contexte['active'] = TRUE; // par defaut les nouveaux comptes sont actives
		$contexte['type_op'] = 'multi'; // par defout les nouveau comptes sont multidirectionnels
		$contexte['date_anterieure'] = date('Y-m-d');
	}
	$contexte['_action'] = array('editer_asso_plan', $id_plan); // pour passer securiser action

	return $contexte;
}

function formulaires_editer_asso_plan_verifier_dist($id_plan='') {
	$erreurs = array();

	$classe = _request('classe');
	$code = _request('code');
	if ((!preg_match("/^[0-9]{2}\w*$/", $code)) || ($code[0]!=$classe)) { // on verifie que le code est bien de la forme chiffre-chiffre-caracteres_alphanumeriques et que le premier chiffre correspond a la classe
		$erreurs['code'] = _T('asso:erreur_plan_code');
	}
	if ($erreur = association_verifier_montant('solde_anterieur') )
		$erreurs['solde_anterieur'] = $erreur;
	if ($erreur = association_verifier_date('date_anterieure') )
		$erreurs['date_anterieure'] = $erreur;
	// verifie la validite d'un changement
	if (!array_key_exists('code', $erreurs)) { // si le code est valide
		$code_initial = _request('code_initial'); // on recupere le code initial pour verifier si on l'a modifie ou pas
		if ($r = sql_fetsel('code,id_plan', 'spip_asso_plan', "code='$code' AND id_plan<>$id_plan")) { // verifier que le code n'est pas deja attribue a une autre ligne du plan
			$erreurs['code'] = _T('asso:erreur_plan_code_duplique');
		} elseif ($code_initial && $code_initial[0]!=$classe && $GLOBALS['association_metas']['comptes']) { // on verifie que si on a change le code on n'a pas modifie la classe pour passer de la classe financiere a une autre classe quand des operations existent dans la base
			$colonne = '';
			if ($code_initial[0]==$GLOBALS['association_metas']['classe_banques']) { // le code original faisait partie de la classe financiere et par consequent le nouveau qui est different non
				$colonne = 'journal'; // si des operations avec ce compte existent, on trouve sa reference dans la colonne journal
			} else if ($classe==$GLOBALS['association_metas']['classe_banques']) { // le nouveau code fait partie de la classe financiere et par consequent l'ancien qui est different non
				$colonne = 'imputation'; // si des operations avec ce compte existent, on trouve sa reference dans la colonne imputation
			}
			if ($colonne) {
				if (sql_countsel('spip_asso_comptes', $colonne."='$code_initial'")) { // on a bien des operations avec les codes incrimines, on ne peut donc pas changer la classe du compte
					$erreurs['code'] = _T('asso:erreur_plan_changement_classe_impossible');
				}
			}
		}
		// verifier si on modifie un code existant qu'on n'attribue pas a un pc_XX des metas un code de la classe financiere
		if (!array_key_exists('code', $erreurs) && $code_initial && $GLOBALS['association_metas']['comptes'] && ($classe==$GLOBALS['association_metas']['classe_banques'])) { // on n'effectue ce controle que si la gestion comptable est activee, la classe est celle des comptes financiers, le code initial non nul (on modifie un compte existant et qu'il n'y a pas d'erreur precedente)
			if ($code_initial!=$code) {
				if (($GLOBALS['association_metas']['pc_cotisations']==$code_initial) ||
					(($GLOBALS['association_metas']['pc_dons']==$code_initial) && ($GLOBALS['association_metas']['dons'])) ||
					(($GLOBALS['association_metas']['pc_ventes']==$code_initial) && ($GLOBALS['association_metas']['ventes'])) ||
					(($GLOBALS['association_metas']['pc_prets']==$code_initial) && ($GLOBALS['association_metas']['prets'])) ||
					(($GLOBALS['association_metas']['pc_activites']==$code_initial) && ($GLOBALS['association_metas']['activites'])) ||
					(($GLOBALS['association_metas']['pc_colis']==$code_initial) && ($GLOBALS['association_metas']['colis'])) ||
					(($GLOBALS['association_metas']['pc_ressources']==$code_initial) && ($GLOBALS['association_metas']['ressources']))
				) {
					$erreurs['code'] = _T('asso:erreur_plan_code_modifie_utilise_classe_financiere');
				}
			}
		}
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_plan_traiter_dist($id_plan='') {
	// partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php
	$res = array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	$action_plancomptable = charger_fonction('editer_asso_plan', 'action');
	list($id_plan, $err) = $action_plancomptable($id_plan);
	if ($err OR !$id_plan) {
		$res['message_erreur'] = ($err ? $err : _T('erreur_traite'));
	} else {
		$res['message_ok'] = '';
		$res['redirect'] = generer_url_ecrire('plan_comptable'); // on renvoit sur la page adherents mais on perd a l'occasion d'eventuel filtres inseres avant d'arriver au formulaire de cotisation...
	}
	return $res;
}

?>
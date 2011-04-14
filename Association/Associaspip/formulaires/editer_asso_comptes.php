<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_asso_comptes_charger_dist($id_compte='new') {
	/* cet appel va charger dans $contexte tous les champs de la table spip_asso_compte associes a l'id_compte passe en param */
	$contexte = formulaires_editer_objet_charger('asso_comptes', $id_compte, '', '',  generer_url_ecrire('comptes'), '');

	/* si c'est une nouvelle operation, on charge la date d'aujourd'hui */
	if (!$id_compte) $contexte['date'] = date('Y-m-d');

	// on ajoute les metas de classe_banques, destinations
	$contexte['classe_banques'] = $GLOBALS['association_metas']['classe_banques'];
	if ($GLOBALS['association_metas']['destinations']) {
		include_spip('inc/association_comptabilite');
		$contexte['destinations_on'] = true;
		$dest_id_montant = association_liste_destinations_associees($id_compte);
		if (is_array($dest_id_montant)) {
			$contexte['id_dest'] = array_keys($dest_id_montant);
			$contexte['montant_dest'] = array_values($dest_id_montant);
		} else {
			$contexte['id_dest'] = '';
			$contexte['montant_dest'] = '';	
		}
		$contexte['unique_dest'] = '';
		$contexte['defaut_dest'] = ''; /* ces variables sont recuperees par la balise dynamique directement dans l'environnement */

	}

	/* meilleure presentation des montants */
	$contexte['depense'] = association_nbrefr($contexte['depense']);
	$contexte['recette'] = association_nbrefr($contexte['recette']);
	
	return $contexte;
}

function formulaires_editer_asso_comptes_verifier_dist($id_compte) {
	$erreurs = array();
	/* on verifie que l'on a bien soit depense soit recette different de 0 et qu'aucun n'est negatif */
	$recette = association_recupere_montant(_request('recette'));
	$depense = association_recupere_montant(_request('depense'));

	if (($recette<0) || ($depense<0) || ($recette>0 && $depense>0) || ($recette==0 && $depense==0))	{
		$erreurs['montant'] = _T('asso:erreur_recette_depense');
	}

	/* on verifie que le type d'operation est bien permise sur ce compte */
	$code=_request('imputation');
	if (!array_key_exists("montant",$erreurs)) {
		$type_op = sql_getfetsel('type_op', 'spip_asso_plan', 'code='.sql_quote($code));
		
		if ((($type_op=='credit') && ($depense>0)) || (($type_op=='debit') && ($recette>0))) {
			$erreurs['imputation'] = _T('asso:erreur_operation_non_permise_sur_ce_compte');
		}
	}

	/* verifier si besoin que le montant des destinations correspond bien au montant de l'opération, sauf si on a deja une erreur de montant */
	if (($GLOBALS['association_metas']['destinations']) && !array_key_exists("montant",$erreurs))
	{
		include_spip('inc/association_comptabilite');
		if ($err_dest = association_verifier_montant_destinations($recette+$depense)) {
			$erreurs['destinations'] = $err_dest;
		}
	}

	/* verifier la validite de la date */
	if ($erreur_date = association_verifier_date(_request('date'))) {
		$erreurs['date'] = _request('date')."&nbsp;:&nbsp;".$erreur_date;
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	
	return $erreurs;
}

function formulaires_editer_asso_comptes_traiter($id_compte) {
	return formulaires_editer_objet_traiter('asso_comptes', $id_compte, '', '',  generer_url_ecrire('comptes'), '');
}
?>

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
	if ($id_compte=='new') $contexte['date'] = date('Y-m-d');

	// on ajoute les metas de classe_banques, destinations et comptes stricts
	$contexte['classe_banques'] = $GLOBALS['association_metas']['classe_banques'];
	if ($GLOBALS['association_metas']['destinations']) $contexte['destinations'] = true;
	if  ($GLOBALS['association_metas']['comptes_stricts']) {
		$contexte['montant'] = association_nbrefr($contexte['depense']+$contexte['recette']);
		unset($contexte['depense']); /* le test dans le formulaire pour l'affichage de montant ou recette et depense et base sur l'existence de cette variable d'environement */
		unset($contexte['recette']);
	} else {
		$contexte['depense'] = association_nbrefr($contexte['depense']);
		$contexte['recette'] = association_nbrefr($contexte['recette']);
	}
	
	return $contexte;
}

function formulaires_editer_asso_comptes_verifier_dist($id_compte) {
	$erreurs = array();
	/* dans le cas de comptes non stricts, on verifie que l'on a bien soit depense soit recette different de 0 et qu'aucun n'est negatif */
	if  (!$GLOBALS['association_metas']['comptes_stricts']) {
		if ($recette_req = _request('recette')){
			$recette = floatval(preg_replace("/,/",".",$recette_req));
		} else $recette = 0;
		if ($depense_req = _request('depense')){
			$depense = floatval(preg_replace("/,/",".",$depense_req));
		} else $depense = 0;

		if (($recette<0) || ($depense<0) || ($recette>0 && $depense>0))	{
		$erreurs['montant'] = _T('asso:erreur_recette_depense');
		}
		$montant = $recette+$depense; /* utilise dans la verification des montants de destinations */
	} else { /* comptes stricts, on verifie le que le montant soit positif */
		if ($montant_req = _request('montant')){
			$montant = floatval(preg_replace("/,/",".",$montant_req));
			if($montant<0) {
				$erreurs['montant'] = _T('asso:erreur_montant');
			}
		}
	}

	/* verifier si besoin que le montant des destinations correspond bien au montant de l'opération, sauf si on a deja une erreur de montant */
	if (($GLOBALS['association_metas']['destinations']) && !array_key_exists("montant",$erreurs))
	{
		include_spip('inc/association_comptabilite');
		if ($err_dest = association_verifier_montant_destinations($montant)) {
			$erreurs['destinations'] = $err_dest;
		}
	}

	if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}

	
	return $erreurs;
}

function formulaires_editer_asso_comptes_traiter($id_compte) {
	if (!$_POST['destination_id1']) return "Erreur";
	return formulaires_editer_objet_traiter('asso_comptes', $id_compte, '', '',  generer_url_ecrire('comptes'), '');
}
?>

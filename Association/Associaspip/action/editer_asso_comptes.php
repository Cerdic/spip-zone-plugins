<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;


function action_editer_asso_comptes() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_compte = $securiser_action();

	include_spip('inc/association_comptabilite');

	$date= _request('date');
	$imputation= _request('imputation');

	$recette = association_recupere_montant(_request('recette'));
	$depense = association_recupere_montant(_request('depense'));
	$justification= _request('justification');
	$journal= _request('journal');

	$type_operation = _request('type_operation');
	/* dans le cas ou c'est un virement on va generer 2 ecritures
	 * Supposons un virement de 400€ du compte 5171 (Caisse d'epargne) vers le compte 531 (caisse)
	 * depense = 400   imputation = 531  journal = 5171
	 *
	 * 1ere ecriture : depense 400€ de 5171 vers 581 (virement interne)
	 * depense = 400   imputation = 581  journal = 5171
	 *
	 * 2eme ecriture : recette 400€ de 581 vers 531
	 * recette = 400   imputation = 581  journal = 531
	 *
	 * Dans Bilan et Compte de résultat, le compte 581 doit avoir un solde = 0 !!!!
	 */

	if ($type_operation == $GLOBALS['association_metas']['classe_banques']) {
		if(!$justification) $justification=_T('asso:virement_interne');
		/* si le compte 58xx n'existe pas on le cree dans le plan comptable */
		$compte_virement = association_creer_compte_virement_interne();
		/* c'est forcément un ajout car pour l'instant l'edition d'un virement est "desactive" */
		/* la modification d'un virement interne n'est pas encore implementee et donc pour modifier */
		/* un virement on le supprime et on le recree .... C'est pas beau mais ça fonctionne !!!*/
		/* TODO : decommenter les lignes si edition/modification d'un virement possible ! */
		//if (!$id_compte) { /* pas d'id_compte, c'est un ajout */
			// 1ere ecriture
			$old_imputation = $imputation;
			$imputation = $compte_virement;
			$id_compte = association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, 0);
			// 2eme ecriture
			$recette = $depense;
			$depense = 0;
			$journal = $old_imputation;
			$id_compte = association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, 0);
		//}
		//else {
			/* c'est une modif, ........ */
		//	association_modifier_compte_virement_interne($id_compte);
		//}
	}
	else {
		if (!$id_compte) { /* pas d'id_compte, c'est un ajout */
			$id_compte = association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, 0);
	}else { /* c'est une modif, la parametre id_journal de la fonction modifier operation comptable est mis a '' afin de ne pas le modifier dans la base */
			association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, '', $id_compte);
		}

	}
	return array($id_compte, '');
}
?>

<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_editer_asso_compte_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_compte = $securiser_action();
	$erreur = '';
	include_spip('inc/association_comptabilite');
	$date = association_recuperer_date('date');
	$imputation = _request('imputation');
	$recette = association_recuperer_montant('recette');
	$depense = association_recuperer_montant('depense');
	$justification = _request('justification');
	$journal = _request('journal');
	$type_operation = _request('type_operation');
	if ($type_operation==$GLOBALS['association_metas']['classe_banques']) { // dans le cas ou c'est un virement on va generer 2 ecritures ! Dans Bilan et Compte de résultat, le compte 581 doit avoir un solde = 0 !!!
		if(!$justification)
			$justification = _T('asso:virement_interne');
		// si le compte 58xx n'existe pas on le cree dans le plan comptable
		$compte_virement = comptabilite_reference_virements();
#		if (!$id_compte) { // pas d'id_compte, c'est un ajout
			// Supposons un virement de 400 du compte 5171 (Caisse d'epargne) vers le compte 531 (caisse)
			// 1ere ecriture : depense = 400   imputation = 581  journal = 5171
			$old_imputation = $imputation;
			$id_compte = comptabilite_operation_ajouter($date, $recette, $depense, $justification, $compte_virement, $journal, 0);
			if (!$id_compte)
				$erreur = _T('asso:erreur_sgbdr');
			// Supposons un virement de 400 du compte 5171 (Caisse d'epargne) vers le compte 531 (caisse)
			// 2eme ecriture : recette = 400   imputation = 581  journal = 531
			$id_compte = comptabilite_operation_ajouter($date, $depense, $recette, $justification, $compte_virement, $old_imputation, 0);
			if (!$id_compte)
				$erreur = _T('asso:erreur_sgbdr');
#		} else { // c'est une modif
		// pour l'instant l'edition d'un virement est "desactive" : la modification d'un virement interne n'est pas encore implementee et donc pour modifier un virement on le supprime et on le recree... (pas beau mais fonctionne)
#			$erreur = association_modifier_compte_virement_interne($id_compte);
#		}
	} else {
		if (!$id_compte) { // pas d'id_compte, c'est un ajout
			$id_compte = comptabilite_operation_ajouter($date, $recette, $depense, $justification, $imputation, $journal, 0);
			if (!$id_compte)
				$erreur = _T('asso:erreur_sgbdr');
		} else { // c'est une modif, la parametre id_journal de la fonction modifier operation comptable est mis a '' afin de ne pas le modifier dans la base
			$erreur = comptabilite_operation_modifier($date, $recette, $depense, $justification, $imputation, $journal, '', $id_compte);
		}
	}
	return array($id_compte, $erreur);
}

?>
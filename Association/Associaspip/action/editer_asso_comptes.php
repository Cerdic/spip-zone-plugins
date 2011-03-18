<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
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

	if ($GLOBALS['association_metas']['comptes_stricts']=="on") {
		if ($montant_req = _request('montant')){
			$montant = floatval(preg_replace("/,/",".",$montant_req));
		} else $montant = 0;
		$r = sql_fetsel('direction', 'spip_asso_plan', "code=$imputation");
		if ($r['direction'] == "credit")
		{
			$recette = $montant;
			$depense = 0;
		}
		else
		{
			$recette = 0;
			$depense = $montant;
		}
	}
	else
	{
		if ($recette_req = _request('recette')){
			$recette = floatval(preg_replace("/,/",".",$recette_req));
		} else $recette = 0;
		if ($depense_req = _request('depense')){
			$depense = floatval(preg_replace("/,/",".",$depense_req));
		} else $depense = 0;
	}
	$justification= _request('justification');
	$journal= _request('journal');


	if (!$id_compte) { /* pas d'id_compte, c'est un ajout */
		$id_compte = association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, 0);
	}else { /* c'est une modif, la parametre id_journal de la fonction modifier operation comptable est mis a '' afin de ne pas le modifier dans la base */
		association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, '', $id_compte);
	}

	return array($id_compte, '');
}
?>

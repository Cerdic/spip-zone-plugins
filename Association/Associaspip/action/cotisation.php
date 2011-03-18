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

function action_cotisation() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$date= $_POST['date'];
	$journal= $_POST['journal'];
	if ($montant_req =  _request('montant')) {
		$montant = floatval(preg_replace("/,/",".",$montant_req));
	}
	else $montant = 0;
	$justification =$_POST['justification'];
	$imputation=$GLOBALS['association_metas']['pc_cotisations'];
	$validite =$_POST['validite'];

	cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite);
}

function cotisation_insert($id_auteur, $montant, $journal, $justification, $imputation, $date, $validite)
{
	include_spip('base/association');
	include_spip('inc/association_comptabilite');
	association_ajouter_operation_comptable($date, $montant, 0, $justification, $imputation, $journal, $id_auteur);

	sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
				   array(
					 "validite" => $validite,
					 "statut_interne" => 'ok'),
				   "id_auteur=$id_auteur");

}
?>

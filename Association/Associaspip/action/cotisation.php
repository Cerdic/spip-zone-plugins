<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_cotisation() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$date = _request('date');
	$journal = _request('journal');
	$montant =  association_recupere_montant(_request('montant'));

	$justification = _request('justification');
	$validite = _request('validite');

	cotisation_insert($id_auteur, $montant, $journal, $justification, $date, $validite);

	return array($id_auteur, '');
}

function cotisation_insert($id_auteur, $montant, $journal, $justification, $date, $validite)
{
	include_spip('base/association');
	include_spip('inc/association_comptabilite');
	/* on verifie que la gestion comptable est activee et que le pc_cotisation n'est pas vide pour inserer l'operation */
	if ($GLOBALS['association_metas']['comptes'] && $GLOBALS['association_metas']['pc_cotisations'])
		association_ajouter_operation_comptable($date, $montant, 0, $justification, $GLOBALS['association_metas']['pc_cotisations'], $journal, $id_auteur);	

	sql_updateq('spip_asso_membres', 
				   array(
					 "validite" => $validite,
					 "statut_interne" => strtotime($validite)>strtotime("-1 day")?'ok':'echu'), // on verifie que la date entree soit aujourd'hui ou dans le futur pour attribuer le statut ok
				   "id_auteur=$id_auteur");

}
?>

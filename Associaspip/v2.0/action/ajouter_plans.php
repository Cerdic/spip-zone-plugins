<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_plans() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$code = _request('code');
	$actif = _request('actif');
	$intitule = _request('intitule');
	$classe = _request('classe');
	$reference = _request('reference');
	$solde_anterieur = _request('solde_anterieur');
	$commentaire = _request('commentaire');
	$date_anterieure = _request('date_anterieure');
	plan_insert($actif, $intitule, $reference, $code, $solde_anterieur, $date_anterieure, $classe, $commentaire);
}


function plan_insert($actif, $intitule, $reference, $code, $solde_anterieur, $date_anterieure, $classe, $commentaire)
{
	include_spip('base/association');		

	$id_plan = sql_insertq('spip_asso_plan', array(
				'date_anterieure' => $date_anterieure,
				'actif' => $actif,
				'code' => $code,
				'intitule' => $intitule,
				'classe' => $classe,
				'reference' => $reference,
				'solde_anterieur' => $solde_anterieur,
				'commentaire' => $commentaire));
}
?>

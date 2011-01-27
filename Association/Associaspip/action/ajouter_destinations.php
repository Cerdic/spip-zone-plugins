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

function action_ajouter_destinations() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$intitule = _request('intitule');
	$commentaire = _request('commentaire');
	destination_insert($intitule, $commentaire);
}


function destination_insert($intitule, $commentaire)
{
	include_spip('base/association');		

	$id_plan = sql_insertq('spip_asso_destination', array(
				'intitule' => $intitule,
				'commentaire' => $commentaire));
}
?>

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

function action_modifier_ressources() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_ressource=$securiser_action();

	$code= _request('code');
	$date = _request('date_acquisition');
	$intitule = _request('intitule');
	$id_achat = _request('id_achat');
	$pu = _request('pu');
	$statut = _request('statut');
	$commentaire = _request('commentaire');

	include_spip('base/association');
	sql_updateq('spip_asso_ressources', array(
			'date_acquisition' => $date,
			'code' => $code,
			'intitule' => $intitule,
			'id_achat' => $id_achat,
			'pu' => $pu,
			'statut' => $statut,
			'commentaire' => $commentaire),
		    "id_ressource=$id_ressource");
}
?>

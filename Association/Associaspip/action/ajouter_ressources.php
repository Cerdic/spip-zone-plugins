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

function action_ajouter_ressources() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_achat = $securiser_action();

	$pu = _request('pu');
	$code= _request('code');
	$statut = _request('statut');
	$intitule = _request('intitule');
	$date = _request('date_acquisition');
	$commentaire = _request('commentaire');

	ressource_insert($id_achat, $code, $intitule, $pu, $date, $statut, $commentaire);
}


function ressource_insert($id_achat, $code, $intitule, $pu, $date, $statut='', $commentaire='')
{
	include_spip('base/association');		
	sql_insertq('spip_asso_ressources', array(
					    'date_acquisition' => $date,
					    'id_achat' => $id_achat,
					    'code' => $code,
					    'statut' => $statut,
					    'intitule' => $intitule,
					    'pu' => $pu,
					    'commentaire' => $commentaire));

}
?>

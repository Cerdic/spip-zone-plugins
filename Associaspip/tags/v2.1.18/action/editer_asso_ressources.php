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

function action_editer_asso_ressources() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_ressource=$securiser_action();

	$code= _request('code');
	$date = _request('date_acquisition');
	$intitule = _request('intitule');
	$pu = association_recupere_montant(_request('pu'));
	$statut = _request('statut');
	$commentaire = _request('commentaire');

	include_spip('base/association');

	if ($id_ressource) {/* c'est une modification */
		sql_updateq('spip_asso_ressources', array(
			'date_acquisition' => $date,
			'code' => $code,
			'intitule' => $intitule,
			'pu' => $pu,
			'statut' => $statut,
			'commentaire' => $commentaire),
		    "id_ressource=$id_ressource");
	} else { /* c'est un ajout */
		$id_ressource = sql_insertq('spip_asso_ressources', array(
		    'date_acquisition' => $date,
		    'code' => $code,
		    'statut' => $statut,
		    'intitule' => $intitule,
		    'pu' => $pu,
		    'commentaire' => $commentaire));
	}

	return array($id_ressource, '');
}
?>

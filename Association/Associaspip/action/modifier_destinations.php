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

function action_modifier_destinations() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_destination=$securiser_action();

	$intitule = _request('intitule');
	$commentaire = _request('commentaire');

	include_spip('base/association');
	sql_updateq('spip_asso_destination', array(
				'intitule' => $intitule,
				'commentaire' => $commentaire),
		    "id_destination=$id_destination");
}
?>

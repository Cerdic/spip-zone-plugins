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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_supprimer_ressources()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_ressource= $securiser_action();
	if ( sql_countsel('spip_asso_prets', "id_ressource=$id_ressource") ) { // s'il y a un historique, juste masquer a l'affichage. (en supprimant on ne perd pas l'historique mais les references pointent dans le neant...)
		sql_update('spip_asso_ressources', array(
			'statut'=>NULL,
		), "id_ressource=$id_ressource" );
	} else { // on peut supprimer la reference
		sql_delete('spip_asso_ressources', "id_ressource=$id_ressource" );
	}
}

?>
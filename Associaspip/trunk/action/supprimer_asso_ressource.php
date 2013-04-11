<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_supprimer_asso_ressource_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_ressource= $securiser_action();
	if ( sql_countsel('spip_asso_prets', "id_ressource=$id_ressource") ) { // s'il y a un historique, juste masquer a l'affichage. (en supprimant on ne perd pas l'historique mais les references pointent dans le neant...) ou alors supprimer d'abord l'historique (ce qu'on laisse a l'appreciation de l'utilisateur qui peut supprimer les prets au prealable)
		sql_update('spip_asso_ressources', array(
			'statut'=>NULL,
		), "id_ressource=$id_ressource" );
	} else { // en l'absence d'historique de prets, on peut supprimer la reference
		sql_delete('spip_asso_ressources', "id_ressource=$id_ressource" );
	}
}

?>
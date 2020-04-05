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

function action_supprimer_asso_pret_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	// cette action a deux modes d'appel :
	if (strpos($arg, '-')) { // mode d'appel 1 (ideal/historique) : avec <id_pret>-<id_ressource>
		if (!preg_match('/^(\d+)\D(\d+)/', $arg, $r))
			spip_log("action_supprimer_prets: $arg incompris",'associaspip');
		else
			list($id_pret, $id_ressource) = $r;
	} else { // mode d'appel 2 (simple/nouveau) : juste avec <id_pret>
		$id_pret = intval($arg);
		$id_ressource = sql_getfetsel('id_ressource', 'spip_asso_prets', "id_pret=$ip_pret"); // on est oblige de faire une requete supplementaire car on a besoin du id_ressource pour mettre a jour le statut
	}
	include_spip ('inc/association_comptabilite');
	comptabilite_operation_supprimer(comptabilite_reference_operation('pc_prets', $id_pret));
	sql_delete('spip_asso_prets', "id_pret=$id_pret");
	sql_updateq('spip_asso_ressources',
		array('statut'=>'ok',
	), "statut='reserve' AND id_ressource=$id_ressource" ); // compatibilite avec les anciens statuts textuel
	sql_updateq('spip_asso_ressources',
			array('statut'=>'statut+1',
	), "statut>=0 AND id_ressource=$id_ressource" ); // retour d'une ressource disponible (nouveau statut numerique)
	sql_updateq('spip_asso_ressources',
		array('statut'=>'statut-1',
	), "statut<0 AND id_ressource=$id_ressource" ); // retour d'une ressource desactivee (nouveau statut numerique)
}

?>
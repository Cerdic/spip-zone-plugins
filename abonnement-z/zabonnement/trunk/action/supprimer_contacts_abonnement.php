<?php
/**
 * Plugin Coordonnées 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

// suppression d'un id_contacts_abonnement
/* 
[(#URL_ACTION_AUTEUR{supprimer_contacts_abonnement,contacts_abonnement-#ID_CONTACTS_ABONNEMENT-#ID_OBJET-#OBJET,[(#ENV{retour,#SELF})]})],
ajax,
<:abo:confirmer_supprimer_element:>})]
*/
	
function action_supprimer_contacts_abonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('-', $arg);

	if ($arg[0] == 'contacts_abonnement' and intval($arg[1]) and intval($arg[2])) {
		action_supprimer_contacts_abonnement_post($arg[1],$arg[2],$arg[3]);
	}	

}

function action_supprimer_contacts_abonnement_post($id_contacts_abonnement,$id_abonnement,$objet) {
	if (_DEBUG_ABONNEMENT) spip_log("suppression de $id_contacts_abonnement",'contacts_abonnement');
	if ($id_contacts_abonnement and $id_abonnement and $objet=='abonnement') {
		//on ferme la zone si elle existe
		$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = '. $id_abonnement);
		$ids_zone=$abonnement['ids_zone'];
			if($ids_zone!='')
			fermer_zone($id_contacts_abonnement,$ids_zone);
		}
		
		// on supprime la liaison auteur-objet de l'abonnement
		sql_delete("spip_contacts_abonnements", array(
			"id_contacts_abonnement=" . sql_quote($id_contacts_abonnement)
		));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_contacts_abonnement/$id_contacts_abonnement'");
}
?>

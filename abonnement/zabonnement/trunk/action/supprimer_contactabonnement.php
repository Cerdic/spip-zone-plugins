<?php
/**
 * Plugin Coordonnées 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

// suppression d'un objet abonnement pour un contact
/* [(#URL_ACTION_AUTEUR{supprimer_contactabonnement,
 contactabonnement-#ENV{id_auteur}-#ID_OBJET-#OBJET,[(#ENV{retour,#SELF})]})],
*/
	
function action_supprimer_contactabonnement_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('-', $arg);

	// todo verifier suppression dans la commande !!
	if ($arg[0] == 'contactabonnement' and intval($arg[1]) and intval($arg[2])) {
		action_supprimer_contactabonnement_post($arg[1],$arg[2],$arg[3]);
	}	

}

// todo reintroduire le cron pour refermer la zone a echeance et utiliser cette fonction
function fermer_zone($id_auteur,$ids_zone)
{
	$array_ids = explode(",", $ids_zone);
	foreach($array_ids as $id_zone)
	{
	if (_DEBUG_ABONNEMENT) spip_log("fermer_zone $id_zone pour $id_auteur",'abonnement');
		sql_delete("spip_zones_auteurs", array(
			"id_zone"=>$id_zone,
			"id_auteur"=>$id_auteur
		));
	}
}

function action_supprimer_contactabonnement_post($id_auteur,$id_abonnement,$objet) {

	if ($id_auteur and $id_abonnement) {
		//l' abonnement
		$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = '. $id_abonnement);
		$ids_zone=$abonnement['ids_zone'];
			if($ids_zone!='')
			fermer_zone($id_auteur,$ids_zone);
		
		// on supprime l'abonnement de l'auteur
		sql_delete("spip_contacts_abonnements", array(
			"id_auteur=" . sql_quote($id_auteur),
			"id_objet=" . sql_quote($id_abonnement),
			"objet='$objet'"
		));

	}
	

	include_spip('inc/invalideur');
	//suivre_invalideur("id='id_contactabonnement/$id_contactabonnement'");
}
?>

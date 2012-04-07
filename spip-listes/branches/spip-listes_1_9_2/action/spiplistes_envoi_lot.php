<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
function action_spiplistes_envoi_lot_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	//changer de statut
	// envoi d'un lot par la meleuse
	include_spip('inc/spiplistes_meleuse');
	
	// compter les mail restant a envoyer pour l'affichage
	$res = spip_query("SELECT COUNT(a.id_auteur) AS n FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='encour'");
	$n = 0;
	if ($row = spip_fetch_array($res))
		$n = $row['n'];
	ajax_retour($n);
}

?>
<?php
function cron_abonnement_cron($t){

// ---------------------------------------------------------------------------------------------
// Taches de fond
		
		spip_log("cron abonnement","abonnement");
		
		// fermer les zones aux echus

	$result = spip_query("
	SELECT id_auteur FROM spip_auteurs_elargis a, spip_auteurs_elargis_abonnements b
	WHERE
	a.id = b.id_auteur_elargi
	and a.validite <> '0000-00-00 00:00:00' 
	and a.validite < NOW()
	");
		
		while($row = spip_fetch_array($result)){
		$id_auteur = $row['id_auteur'] ;
		spip_log("$id_auteur est echu (salo), il perd sa (ses) zone(s)", "abonnement");		
		spip_query("DELETE FROM `spip_zones_auteurs` WHERE id_auteur='$id_auteur'");
		}
		
		
	return 1; 
}
?>
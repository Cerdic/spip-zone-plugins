<?php

function action_depublication_dist() {

	// on sauvgarde en base la date de dépublication dans la table spip_articles_depublication
	
	$id_article = _request("arg");
	
	$jour = _request('jour');
	
	$mois = _request('mois');
	$annee = _request('annee');
	$heures = _request('heures');
	$minutes = _request('minutes');
	
	$depublication = date("Y-m-d H:i:s", mktime($heures, $minutes, '00', $mois , $jour, $annee));
	
	spip_query("delete from spip_articles_depublication where id_article=".$id_article);
	if ($jour != '00' && $mois != '' && $annee != '') {
		spip_query("insert into spip_articles_depublication(id_article, depublication,statut) values(".$id_article.",'".$depublication."', NULL)");
	}
	
	
	
	
	
}

?>



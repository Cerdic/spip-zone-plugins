<?php
/*
 * Plugin Recommander a un ami
 * (c) 2006-2010 Fil
 * Distribue sous licence GPL
 *
 */


function formulaires_debut_fin_charger_dist($id_article){
	$valeurs = array("agenda", "date_debut", "date_fin");

	$query = sql_select("agenda, date_debut, date_fin", "spip_articles", "id_article=$id_article");
	if ($row = sql_fetch($query)) {
		$valeurs["agenda"] = $row["agenda"];
		$valeurs["date_debut"] = $row["date_debut"];
		$valeurs["date_fin"] = $row["date_fin"];
	}
	
	$valeurs["date_debut"] = preg_replace(",([0-9][0-9][0-9][0-9])\-([0-9][0-9])\-([0-9][0-9]),", "\\3/\\2/\\1", $valeurs["date_debut"]);
	$valeurs["date_fin"] = preg_replace(",([0-9][0-9][0-9][0-9])\-([0-9][0-9])\-([0-9][0-9]),", "\\3/\\2/\\1", $valeurs["date_fin"]);

	
	return $valeurs;
}


function formulaires_debut_fin_verifier_dist($id_article){
	$erreurs = array();


	return $erreurs;
}


function formulaires_debut_fin_traiter_dist($id_article){
	$agenda = _request("agenda");
	$date_debut = _request("date_debut");
	$date_fin = _request("date_fin");
	
	if (!autoriser('modifier', 'article', $id_article)) return false;

	$date_debut = preg_replace(",([0-9][0-9])\/([0-9][0-9])\/([0-9][0-9][0-9][0-9]),", "\\3-\\2-\\1", $date_debut);
	$date_fin = preg_replace(",([0-9][0-9])\/([0-9][0-9])\/([0-9][0-9][0-9][0-9]),", "\\3-\\2-\\1", $date_fin);

	sql_updateq("spip_articles",
		array(
			"agenda" => $agenda,
			"date_debut" => $date_debut,
			"date_fin" => $date_fin
		),
		"id_article=$id_article"
	);

}

?>
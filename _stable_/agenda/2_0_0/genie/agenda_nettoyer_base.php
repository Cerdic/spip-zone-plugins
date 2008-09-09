<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function genie_agenda_nettoyer_base_dist($t){
	# les evenements lies a un article inexistant
	$res = sql_select("evenements.id_evenement,evenements.id_article","spip_evenements AS evenements
			LEFT JOIN spip_articles AS articles
			ON evenements.id_article=articles.id_article","articles.id_article IS NULL");
	while ($row = sql_fetch($res))
		sql_delete("spip_evenements","id_evenement=".$row['id_evenement']
		." AND id_article=".$row['id_article']);

	# les liens de mots affectes a des evenements effaces
	$res = sql_select("mots_evenements.id_mot,mots_evenements.id_evenement",
			"spip_mots_evenements AS mots_evenements
			LEFT JOIN spip_evenements AS evenements
			ON mots_evenements.id_evenement=evenements.id_evenement",
			"evenements.id_evenement IS NULL");

	while ($row = sql_fetch($res))
		sql_delete("spip_mots_evenements","id_mot=".$row['id_mot']
		." AND id_evenement=".$row['id_evenement']);

	return 1;
}

?>
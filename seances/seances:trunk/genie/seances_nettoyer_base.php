<?php
function seances_taches_generales_cron($taches_generales){
	$taches_generales['seances_nettoyer_base'] = 24*3600;
	return $taches_generales;
}

function genie_seances_nettoyer_base_dist($t){
	# les seances liees a un article supprimé ou à la poubelle
	$res = sql_select('s.id_seance,s.id_article',
		'spip_seances AS s LEFT JOIN spip_articles AS a ON s.id_article=a.id_article',
		'a.id_article IS NULL');
	while ($row = sql_fetch($res))
		sql_delete('spip_seances','id_article='.$row['id_article'].' AND id_seance='.$row['id_seance']);

	return 1;
}

?>
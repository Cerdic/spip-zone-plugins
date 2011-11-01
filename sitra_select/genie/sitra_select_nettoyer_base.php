<?php
function sitra_select_taches_generales_cron($taches_generales){
	$taches_generales['sitra_select_nettoyer_base'] = 24*3600;
	return $taches_generales;
}

function genie_sitra_select_nettoyer_base_dist($t){
	# les noisettes liees a un article supprimé ou à la poubelle
	$res = sql_select('s.id_article',
		'spip_sitra_select_articles AS s LEFT JOIN spip_articles AS a ON s.id_article=a.id_article',
		'a.id_article IS NULL');
	while ($row = sql_fetch($res))
		sql_delete('spip_sitra_select_articles','id_article='.$row['id_article']);
	
	return 1;
}

?>
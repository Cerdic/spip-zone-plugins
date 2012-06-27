<?php

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * 
 * @return
 * @param object $time
 */
function genie_doc2article($time)  {
	spip_log('début de tache cron','doc2article');
	
	$action_importer = charger_fonction('doc2article_importer','action');
	
	$nb_docs = 5; // nb de docs à traiter à chaque passage du cron, à passer en val de config
	$result = sql_select("*","spip_doc2article","","","date","0,".intval($nb_docs+1));
	
	while ($row = sql_fetch($result)) {
		spip_log("import depuis cron : ".$row['fichier'],"doc2article");
		$action_importer($row['id_doc2article']);
	}

	spip_log('fin de tache cron','doc2article');
	
	return 1;
}
?>
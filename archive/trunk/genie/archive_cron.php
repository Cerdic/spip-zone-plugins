<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_archive_cron($t){
	// teste si l'archivage automatique est actif
	if (lire_config("archive/act_archive",'non') == "oui" && ($jours = lire_config("archive/jours",0)) != 0){
		// s'il est actif, alors il archive les articles
		// de chaque rubrique selectionnée
		$articles = sql_allfetsel('id_article','spip_articles','FROM_UNIXTIME(UNIX_TIMESTAMP(date))<FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())-($jours*24*3600)) AND '.sql_in('id_rubrique',lire_config("archive/idrub",array())));
		if(count($articles) > 0){
			include_spip('action/editer_article');
			foreach ($articles as $article) {
				$id_article = $article['id_article'];
				$modifs = array('archive_date'=>date(),'statut' => 'archive');
				$modif = article_modifier($id_article,$modifs);
			}
		}
	}
	return 1;
}
?>
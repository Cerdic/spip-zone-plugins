<?php
//determine le comportement du plugin selon la page appel�e
function archive_execution($flux) {
	//determine la page demand�e 
	switch ($flux['args']['exec']) {
		//la page articles est demand�e
		case "articles" :
			//charge les fonctions necessaire
			include_once('inc/archive_articles.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= archive_ajout_option($id_article);
			break;
		default : 
	}
	//retourne l'affichage complet
	return $flux;
}

?>



<?php
//determine le comportement du plugin selon la page appelée


function depublication_execution($flux) {
	
	
	//determine la page demandée 
	switch ($flux['args']['exec']) {
		//la page articles est demandée
		
		case "articles" :
			
			//charge les fonctions necessaire
			include_once('inc/depublication.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= inc_depublication_dist($id_article);
			break;
		default : 
	}
	
	
	//retourne l'affichage complet
	return $flux;
}

?>



<?php

/**
 * Plugin Quiz pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function quiz_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	
	if ($exec=='articles'){
		$id_article = $flux['args']['id_article'];
		$afficher = true;

		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
			 $quiz = recuperer_fond('prive/contenu/quiz_article',$contexte);
			 $flux['data'] .= $quiz;
		}
	}

	return $flux;
}







?>
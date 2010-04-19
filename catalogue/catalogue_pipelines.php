<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009-2010 - Ateliers CYM
 */

// echo '<script type="text/javascript">alert("catalogue pipelines");</script>';

function catalogue_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=articles
	if ($exec=='articles'){
	
		// on récupère l'id_article
		$id_article = $flux['args']['id_article'];
		$afficher = true;

		if ($afficher) {
			$contexte = array();
			foreach($_GET as $key=>$val)
				$contexte[$key] = $val;
				
			$catalogue = recuperer_fond('prive/contenu/catalogue_article',$contexte);
			$flux['data'] .= $catalogue;
		}
	}

	return $flux;
}

?>
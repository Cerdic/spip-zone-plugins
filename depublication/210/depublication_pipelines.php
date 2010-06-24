<?php
/*
 * Plugin Depublication
 * (c) 2010 Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */
 
// tous les jours par defaut
define('DEPUBLICATION_PERIODICITE_CRON', 1*24*3600); 


/**
 * Ajout au cron de la fonction de depublication d'articles 
 *
 * @param array $taches Liste des taches a effectuer
 * @return array Liste des taches a effectuer
**/
function depublication_taches_generales_cron($taches){
	$taches['depublier_articles'] = DEPUBLICATION_PERIODICITE_CRON;
	return $taches;
}



/**
 * ajouter le formulaire pour definir une date de depublication
 *
 * @param array $flux
 * @return array
 */
function depublication_affiche_milieu($flux) {
	if (($type = $flux['args']['exec'])=='articles'){
		$id = $flux['args']['id_article'];
		// on affiche uniquement si la rubrique est une traduction
		// OU si on a le droit de la modifier (pour en declarer une)
		$dater = recuperer_fond('prive/dater/depublication', array('id_article' => $id), array('ajax'=>true));
		$flux['data'] .= $dater;
	}
	return $flux;
}


?>

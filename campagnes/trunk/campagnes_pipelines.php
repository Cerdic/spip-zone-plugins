<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Ajout d'une tache CRON pour vérifier toutes les heures les pubs à publier/dépublier
 */
function campagnes_taches_generales_cron($taches){
	$taches['campagnes_publication'] = 60 * 60;
	return $taches;
}

/**
 * Ajout du script JS nécessaire à l'async si besoin
 **/
function campagnes_affichage_final($flux) {
	if (
		$GLOBALS['html'] // si c'est bien du HTML
		and ($p = strpos($flux, 'data-id_encart')) !== false // et qu'on a au moins une saisie
		and ($position = strpos($flux, '</body')) !== false // et qu'on a la balise </body> quelque part
	) {
		$js = '<script type="text/javascript" src="'. find_in_path('javascript/campagnes_async.js') .'"></script>';
		// On insère le JS à la fin du <body>
		$flux = substr_replace($flux, $js, $position, 0);
	}
	
	return $flux;
}

/**
 * Ajouter la purge des statistiques
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function campagnes_affiche_gauche($flux) {
	if ($flux['args']['exec'] == 'encarts') {
		$purger = recuperer_fond('prive/squelettes/inclure/purger_statistiques_campagnes', array());
		$flux['data'] .= $purger;
	}
	
	return $flux;
}

<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Quand une newsletter est publiee fixer les images dans un dossier IMG/nl/xx/
 * pour ne jamais perdre les images temporaires
 *
 * @param $flux
 * @return mixed
 */
function newsletters_pre_edition($flux){
	if ($flux['args']['table']=='spip_newsletters'
	  AND $flux['args']['action']=='instituer'
	  AND $id_newsletter = $flux['args']['id_objet']
	  AND $statut_ancien = $flux['args']['statut_ancien']
	  AND isset($flux['data']['statut'])
	  AND $statut = $flux['data']['statut']
	  AND $statut != $statut_ancien
	  AND ($statut=='publie')){

		// generer une version a jour (ne fera rien si deja cuite)
		$generer_newsletter = charger_fonction("generer_newsletter","action");
		$generer_newsletter($id_newsletter);

		// fixer les images et autre
		$fixer_newsletter = charger_fonction("fixer_newsletter","action");
		$fixer_newsletter($id_newsletter);

		$flux['data']['baked'] = 1;
	}

	return $flux;
}


/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param array $flux
 * @return array
 */
function newsletters_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('newsletter'=>'*'),'*');
	return $flux;
}

?>
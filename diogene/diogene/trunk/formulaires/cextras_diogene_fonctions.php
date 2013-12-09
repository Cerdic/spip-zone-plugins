<?php 
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2012 - Distribue sous licence GNU/GPL
 * 
 * Fichier de fonctions associées au squelette cextras_diogene.html
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
 
include_spip('cextras_pipelines');

/**
 * Récupération de la liste des champs extras d'un objet particulier (article, rubrique...)
 * 
 * @param string $type Le type de l'objet
 */
function diogene_recuperer_cextras($type){
	$extras = champs_extras_objet(table_objet_sql($type));

	$extras_finaux = array();
	foreach ($extras as $c) {
		if(preg_match('/\:/',$c['options']['label']))
			$extras_finaux[$c['options']['nom']] = _T($c['options']['label']);
		else 
			$extras_finaux[$c['options']['nom']] = typo($c['options']['label']);
	}
	return $extras_finaux;
}
?>

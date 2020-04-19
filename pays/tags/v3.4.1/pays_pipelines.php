<?php
/**
 * Utilisations de pipelines par Pays
 *
 * @plugin     Pays
 * @copyright  2015
 * @author     2. Cyril MARION
 * @licence    GNU/GPL
 * @package    SPIP\Pays\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function pays_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	include_spip('inc/config');

	// pays sur les articles, auteurs, contacts, organisations, rubriques
	if ($e and !$e['edition'] and in_array($e['table_objet_sql'], array_filter(lire_config('pays/pays_objets',array())))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'pays',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

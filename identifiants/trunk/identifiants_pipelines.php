<?php
/**
 * Utilisations de pipelines par Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2015
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout de contenu sur certaines pages.
 *
 * - Prix sur les objets configurés
 *
 * @pipeline affiche_milieu
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function identifiants_affiche_milieu($flux) {

	include_spip('inc/config');
	$texte  = "";
	$e      = trouver_objet_exec($flux['args']['exec']);
	$objets = lire_config('identifiants/objets', array());

	// Identifiants sur les objets activés
	if (
		$e !== false // page d'un objet éditorial
		AND $e['edition'] === false // pas en mode édition
		AND $table_objet_sql = $e['table_objet_sql']
		AND in_array($table_objet_sql,$objets)
	) {
		$texte .= recuperer_fond('prive/objets/editer/identifiant', array(
			'objet'    => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			),
			array('ajax'=>'identifiant')
		);
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

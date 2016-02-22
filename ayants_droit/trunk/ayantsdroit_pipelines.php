<?php
/**
 * Utilisations de pipelines par Ayants droit
 *
 * @plugin     Ayants droit
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Ayantsdroit\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function ayantsdroit_pre_boucle($boucle) {
	if ($boucle->type_requete == 'droits_contrats') {
		// On cherche s'il y a une jointure sur les ayants droit
		foreach ($boucle->from as $cle => $table){
			if ($table == 'spip_droits_ayants'){
				$boucle->from_type[$cle] = 'LEFT';
			}
		}
	}
	
	return $boucle;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function ayantsdroit_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	
	// Les liaisons sur les objets configurés
	if (
		is_array($e)
		and !$e['edition']
		and in_array(table_objet_sql($e['type']), lire_config('ayantsdroit/lier_objets',array()))
	) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'droits_contrats',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}
	
	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
}

/**
 * Optimiser la base de données 
 * 
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function ayantsdroit_optimiser_base_disparus($flux){
	sql_delete("spip_droits_contrats", "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

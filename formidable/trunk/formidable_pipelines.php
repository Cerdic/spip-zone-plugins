<?php

/**
 * Utilisation de pipelines
 * 
 * @package SPIP\Formidable\Pipelines
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Gérer les statut des boucles de réponses de formulaire
 * 
 * Si pas de critère "statut", on n'affiche que les réponses publiées
 *
 * @pipeline pre_boucle
 * @param Boucle $boucle
 *     Définition de la Boucle
 * @return Boucle
 *     Définition de la boucle complétée
 */
function formidable_pre_boucle($boucle){
	if ($boucle->type_requete == 'formulaires_reponses') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut']) and !isset($boucle->modificateur['tout'])){
			$boucle->where[] = array("'='", "'$statut'", "sql_quote('publie')");
		}
	}
	return $boucle;
}

/**
 * Optimiser la base de donnée en enlevant les liens de formulaires supprimés
 * 
 * @pipeline optimiser_base_disparus
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function formidable_optimiser_base_disparus($flux){
	// Les réponses qui sont à la poubelle
	$res = sql_select(
		'id_formulaires_reponse AS id',
		'spip_formulaires_reponses',
		'statut = '.sql_quote('poubelle')
	);
	
	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaires_reponse', $res);
	return $flux;
}

?>

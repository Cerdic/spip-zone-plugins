<?php

/**
 * Utilisation de pipelines
 * 
 * @package SPIP\Formidable\Pipelines
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


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
	// Les formulaires qui sont à la poubelle
	$res = sql_select(
		'id_formulaire AS id',
		'spip_formulaires',
		'statut='.sql_quote('poubelle')
	);

	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires', 'id_formulaire', $res);


	# les reponses qui sont associees a un formulaire inexistant
	$res = sql_select("R.id_formulaire AS id",
		        "spip_formulaires_reponses AS R
		        LEFT JOIN spip_formulaires AS F
		          ON R.id_formulaire=F.id_formulaire",
			 "R.id_formulaire > 0
			 AND F.id_formulaire IS NULL");

	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaire', $res);


	// Les réponses qui sont à la poubelle
	$res = sql_select(
		'id_formulaires_reponse AS id',
		'spip_formulaires_reponses',
		sql_in('statut',array('refuse','poubelle'))
	);
	
	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaires_reponse', $res);


	// les champs des reponses associes a une reponse inexistante
	$res = sql_select("C.id_formulaires_reponse AS id",
		        "spip_formulaires_reponses_champs AS C
		        LEFT JOIN spip_formulaires_reponses AS R
		          ON C.id_formulaires_reponse=R.id_formulaires_reponse",
			 "C.id_formulaires_reponse > 0
			 AND R.id_formulaires_reponse IS NULL");

	$flux['data'] += optimiser_sansref('spip_formulaires_reponses_champs', 'id_formulaires_reponse', $res);

	//
	// CNIL -- Informatique et libertes
	//
	// masquer le numero IP des vieilles réponses
	//
	## date de reference = 4 mois
	## definir a 0 pour desactiver
	## même constante que pour les forums
	if (!defined('_CNIL_PERIODE')) {
		define('_CNIL_PERIODE', 3600*24*31*4);
	}
	
	if (_CNIL_PERIODE) {
		$critere_cnil = 'date<"'.date('Y-m-d', time()-_CNIL_PERIODE).'"'
			. ' AND statut != "spam"'
			. ' AND (ip LIKE "%.%" OR ip LIKE "%:%")'; # ipv4 ou ipv6
		$c = sql_countsel('spip_formulaires_reponses', $critere_cnil);
		if ($c>0) {
			spip_log("CNIL: masquer IP de $c réponses anciennes à formidable");
			sql_update('spip_formulaires_reponses', array('ip' => 'MD5(ip)'), $critere_cnil);
		}
	}
	
	return $flux;
}

?>
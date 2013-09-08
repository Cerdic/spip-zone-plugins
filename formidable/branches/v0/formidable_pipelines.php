<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Si pas de critère "statut", on affiche que les réponses publiées
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

function formidable_optimiser_base_disparus($flux){
	// Les réponses qui sont à la poubelle
	$res = sql_select(
		'id_formulaires_reponse AS id',
		'spip_formulaires_reponses',
		'statut = '.sql_quote('poubelle')
	);
	
	// On génère la suppression
	$flux['data'] += optimiser_sansref('spip_formulaires_reponses', 'id_formulaires_reponse', $res);

	// CNIL -- Informatique et libertes
	//
	// masquer le numero IP des vieilles réponses
	//
	## date de reference = 4 mois
	## definir a 0 pour desactiver
	if (!defined('_CNIL_PERIODE_FORMIDABLE')) {
		define('_CNIL_PERIODE_FORMIDABLE', 3600*24*31*4);
	}
	
	if (_CNIL_PERIODE_FORMIDABLE) {
		$critere_cnil = 'date<"'.date('Y-m-d', time()-_CNIL_PERIODE_FORMIDABLE).'"'
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
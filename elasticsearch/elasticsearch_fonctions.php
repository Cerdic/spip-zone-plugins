<?php
/**
 * Fonctions utiles au plugin Elasticsearch
 *
 * @plugin     Elasticsearch
 * @copyright  2016
 * @author     Guy Cesaro
 * @licence    GNU/GPL
 * @package    SPIP\Elasticsearch\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function extraire_un_document($document) {
	// Extraire le contenu si possible
	if (defined('_DIR_PLUGIN_EXTRAIREDOC')) {
	include_spip('inc/extraire_document');
	$extraire = inc_extraire_document($document);
	}

/* 	$data['language'] = $extraire['metadata']['language'];
	$data['date'] = $extraire['metadata']['date']; */
	// Si on a réussi à extraire le document, on ajoute son contenu
	if ($extraire['body']) {
		return $extraire;
	}
	else {
		spip_log('impossible d\'extraire le document '.$document['id_document'], 'extraire_document' ._LOG_INFO_IMPORTANTE);
		return false;
	}

}
/*
 * Un fichier de fonctions permet de définir des éléments
 * systématiquement chargés lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, …
 * 
 */
function objet_est_indexe($objet, $id_objet) {
	$serveur = lire_config('elasticsearch_config/url_serveur');
	$index = lire_config('elasticsearch_config/nom_alias');
	$url = $serveur."/".$index."/".$objet."/".$id_objet;
	$resultat = json_decode(phpcurl_get($url), true);
	return $resultat['found'];
	
}
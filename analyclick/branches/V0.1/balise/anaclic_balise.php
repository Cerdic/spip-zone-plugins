<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

/*
// Lien pour le comptage avant telechargement
function generer_url_doc_compteur($id_document)
{	return generer_url_action ('telecharger', "arg=$id_document", true);
}

// Balise url vers telechargement du document 
function balise_URL_DOC_COMPTEUR_dist($p) 
{	$p->code = "generer_url_doc_compteur(" . champ_sql('id_document',$p) . ")";
	$p->interdire_scripts = false;	
	return $p;
}
*/

// Compter les telechargements d'un document
function anaclic_compter_document($id_document, $debut, $fin)
{	$where = array();
	$where[] = "id_document=$id_document";
	if ($debut) $where[] = "date > '$debut'";
	if ($fin) $where[] = "date < '$fin'";
	$row = sql_fetsel("SUM(telechargement) AS c", "spip_doc_compteurs", $where);
	return $row['c'];
}
/** Balise pour le comptage du nombre de telechargements d'un document */
function balise_COMPTEUR_TELECHARGEMENT_dist($p) 
{	if (!($debut = interprete_argument_balise(1,$p))) $debut='null';
	if (!($fin = interprete_argument_balise(2,$p))) $fin='null';
	$p->code = "anaclic_compter_document(" . champ_sql('id_document',$p) . ", $debut, $fin)";
	$p->interdire_scripts = false;	
	return $p;
}

/**
 * Compile la balise `#URL_DOCUMENT` qui génère l'URL d'un document
 *
 * Retourne l'URL d'un document ou si l'option activée, l'URL de l'action de comptage du téléchargement
 *
 * @balise
 * @uses generer_generer_url()
 * @link https://www.spip.net/3963
 * @example
 *     ```
 *     #URL_DOCUMENT
 *     #URL_DOCUMENT{3}
 *     ```
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_URL_DOCUMENT_dist($p) {
	include_spip('balise/url_');
	
	// Si on est dans le site est que l'option est activée
	// on redirige vers la balise de comptage
	if (!test_espace_prive() and defined('_ANALYCLICK_TOUJOURS_COMPTER') and _ANALYCLICK_TOUJOURS_COMPTER) {
		$id = interprete_argument_balise(1, $p);
		if (!$id) {
			$id = champ_sql('id_document', $p);
		}
		$code = "urlencode_1738(generer_url_action('telecharger', 'arg='.$id, true))";
	}
	else {
		$code = generer_generer_url('document', $p);
	}

	$p->code = $code;
	if (!$p->etoile) {
		$p->code = "vider_url($code)";
	}
	$p->interdire_scripts = false;

	return $p;
}

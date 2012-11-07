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

// Lien pour le comptage avant telechargement
function generer_url_doc_compteur($id_document)
{	return generer_url_action ('telecharger', "arg=$id_document", true);
}

/** Balise url vers telechargement du document */
function balise_URL_DOC_COMPTEUR_dist($p) 
{	$p->code = "generer_url_doc_compteur(" . champ_sql('id_document',$p) . ")";
	$p->interdire_scripts = false;	
	return $p;
}

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

?>
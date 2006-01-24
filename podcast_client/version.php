<?php

/*
 * podcast_client
 *
 * Client de podcast pour SPIP
 *
 * Auteur : fil@rezo.net
 * © 2005 - Distribue sous licence GNU/GPL
 *
 * Voir la documentation dans podcast_client.php
 * (ou, plus tard, dans documentation.html)
 */

$nom = 'podcast_client';
$version = 0.1;

// s'inserer dans le pipeline 'post_syndication' @ ecrire/inc_sites.php3
$GLOBALS['spip_pipeline']['post_syndication'] .= '|podcast_client';
$GLOBALS['spip_pipeline']['delete_tables'] .= '|delete_podcast_client';

// signaler ou se trouve cette fonction
$GLOBALS['spip_matrice']['podcast_client'] = dirname(__FILE__).'/podcast_client.php';

// renseigner le compilateur sur la nouvelle table
$GLOBALS['tables_auxiliaires']['spip_documents_syndic'] = array(
	'field' => array(
		"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_syndic"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
		"id_syndic_article"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
	),
	'key' => array(
		"KEY id_document"	=> "id_document",
		"KEY id_syndic"	=> "id_syndic",
		"KEY id_syndic_article"	=> "id_syndic_article"
	)
);

// ... ses relations et ses jointures

$GLOBALS['tables_relations']['documents']['id_syndic'] = 'documents_syndic';
$GLOBALS['tables_relations']['syndic']['id_document'] = 'documents_syndic';

$GLOBALS['tables_jointures']['spip_documents'][]= 'documents_syndic';
$GLOBALS['tables_jointures']['spip_syndic'][]= 'documents_syndic';
$GLOBALS['tables_jointures']['spip_syndic_articles'][]= 'documents_syndic';

?>

<?php

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
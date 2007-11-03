<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//global $tables_auxiliaires;
global $tables_principales;
global $tables_jointures;

$spip_annotations = array(
	"id_annotation" => "BIGINT(21) NOT NULL",
	"id_document" => "BIGINT(21) NOT NULL",
	"id_auteur" => "BIGINT(21) NOT NULL",
	"titre" => "text DEFAULT '' NOT NULL",
	"texte" => "longtext DEFAULT '' NOT NULL",
	"x" => "BIGINT(21) NOT NULL",
	"y" => "BIGINT(21) NOT NULL",
);
$spip_annotations_key = array(
	"PRIMARY KEY" => "id_annotation",
	"KEY id_auteur" => "id_auteur",
	"KEY id_document" => "id_document"
);

$tables_principales['spip_annotations'] = array(
	'field' => &$spip_annotations,
	'key' => &$spip_annotations_key);

$tables_jointures['spip_annotations'][] = 'documents';
$tables_jointures['spip_annotations']['id_document']= 'documents';

?>

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_auxiliaires;
global $tables_principales;
global $tables_jointures;

$spip_documents_auteurs = array(
	"id_document" => "BIGINT(21) NOT NULL",
	"id_auteur" => "BIGINT(21) NOT NULL",
	"vu" => "ENUM('oui','non') DEFAULT 'non'"
);
$spip_documents_auteurs_key = array(
	"PRIMARY KEY" => "id_document,id_auteur",
	"KEY id_auteur" => "id_auteur");

$tables_auxiliaires['spip_documents_auteurs'] = array(
	'field' => &$spip_documents_auteurs,
	'key' => &$spip_documents_auteurs_key);

$tables_jointures['spip_documents'][] = 'documents_auteurs';
$tables_jointures['spip_auteurs']['id_document']= 'documents_auteurs';

?>
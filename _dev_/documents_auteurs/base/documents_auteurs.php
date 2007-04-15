<?php
/*
 * documents_auteurs
 * Gestion de documents associés aux auteurs
 *
 * Auteurs :
 * Quentin Drouet
 * 2007 - Distribue sous licence GNU/GPL
 *
 */
include_spip('base/serial');

global $tables_principales;
global $tables_auxiliaires;

$spip_documents_auteurs = array(
	"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_auteur"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"vu"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL");

$spip_documents_auteurs_key = array(
	"PRIMARY KEY"		=> "id_auteur, id_document",
	"KEY id_document"	=> "id_document");

$tables_auxiliaires['spip_documents_auteurs'] = array(
	'field' => &$spip_documents_auteurs,
	'key' => &$spip_documents_auteurs_key);

global $tables_jointures;
$tables_jointures['spip_documents'][] = 'documents_auteurs';
?>
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

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales, $tables_auxiliaires, $table_des_tables;

/** Table pour le comptage **/
$spip_doc_count = array(
					"id_document"	=> "BIGINT(21) NOT NULL default 0",
					"date"			=> "DATE NOT NULL",
					"telechargement"=> "INTEGER UNSIGNED NOT NULL"
				);

$spip_doc_count_key = array(
					"PRIMARY KEY" => "id_document, date"
				);

$tables_principales['spip_doc_compteurs'] = array('field' => &$spip_doc_count, 'key' => &$spip_doc_count_key);

/** Table pour eviter le multi-clic */
$spip_doc_count_fix = array(
					"id_document"	=> "BIGINT(21) NOT NULL default 0",
					"ip"			=> "VARCHAR(30) NOT NULL",
					"time"			=> "INT"
				);
					
$spip_doc_count_fix_key = array(
					"PRIMARY KEY" => "id_document, ip"
				);

$tables_auxiliaires['spip_doc_compteurs_fix'] = array('field' => &$spip_doc_count_fix, 'key' => &$spip_doc_count_fix_key);

// Declarer dans la table des tables 
global $table_des_tables;
$table_des_tables['doc_compteurs']		= 'doc_compteurs';

global $tables_jointures;
$tables_jointures['spip_doc_compteurs']['id_document'] = 'documents';

?>
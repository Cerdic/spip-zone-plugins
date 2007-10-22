<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des tables
*  
**/
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;

$spip_notations = array(
					"id_notation" => "BIGINT(21) NOT NULL auto_increment",
					"id_article" => "BIGINT(21) NOT NULL",
					"id_auteur" => "BIGINT(21) NOT NULL",
					"ip"	=> "VARCHAR(255) NOT NULL",
					"note" => "TINYINT(1) NOT NULL",
					"maj" => "TIMESTAMP"
				);
$spip_notations_key = array(
					"PRIMARY KEY" => "id_notation",
					"KEY id_article" => "id_article",
					"KEY id_auteur"	=> "id_auteur",
					"KEY ip" => "ip",
					"KEY note" => "note"
				);

$spip_notations_articles = array(
					"id_article" => "BIGINT(21) NOT NULL",
					"note" => "DOUBLE NOT NULL",
					"note_ponderee" => "DOUBLE NOT NULL",
					"nb" => "BIGINT(21) NOT NULL"
				);
$spip_notations_articles_key = array(
					"PRIMARY KEY" => "id_article",
					"KEY note_ponderee"	=> "note_ponderee",
					"KEY note" => "note"
				);

$tables_principales['spip_notations'] = array('field' => &$spip_notations, 'key' => &$spip_notations_key);
$tables_principales['spip_notations_articles'] = array('field' => &$spip_notations_articles, 'key' => &$spip_notations_articles_key);

// Declarer dans la table des tables pour sauvegarde
global $table_des_tables;
$table_des_tables['notations']            = 'notations';
$table_des_tables['notations_articles']  = 'notations_articles';

// Relations entre les articles et les notations
global $tables_jointures;
$tables_jointures['spip_articles']['id_article'] = 'notations_articles';
$tables_jointures['spip_notations_articles'][] = 'articles';

?>
<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

global $tables_principales;
global $tables_auxiliaires;
global $table_des_tables;

$spip_liens_contenus = array(
		'type_objet_contenant'	=> 'varchar(10)', // article, rubrique, breve, site, mot, auteur, document
		'id_objet_contenant'	=> 'int UNSIGNED NOT NULL',
		'type_objet_contenu'	=> 'varchar(10)', // article, rubrique, breve, site, mot, auteur, document, modele
		'id_objet_contenu'		=> 'varchar(255)' // peut être le nom d'un modele
		);

$spip_liens_contenus_key = array(
		'PRIMARY KEY'	=> 'type_objet_contenant, id_objet_contenant, type_objet_contenu, id_objet_contenu',
		'KEY contenant'	=> 'type_objet_contenant, id_objet_contenant',
		'KEY contenu'	=> 'type_objet_contenu, id_objet_contenu'
		);

$tables_principales['spip_liens_contenus'] = array('field' => &$spip_liens_contenus, 'key' => &$spip_liens_contenus_key);
$table_des_tables['liens_contenus'] = 'liens_contenus';
?>
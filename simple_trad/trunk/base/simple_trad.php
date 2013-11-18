<?php
/**
 * Plugin Simple trad
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2013 - Distribue sous licence GNU/GPL
 *
 * Déclaration des tables pour Simple trad
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function simple_trad_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['champs_editables'][] = 'lang';
	$tables['spip_articles']['champs_editables'][] = 'langue_choisie';
	return $tables;
}
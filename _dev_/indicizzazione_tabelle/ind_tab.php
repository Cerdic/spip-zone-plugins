<?php
/*
 * Indicizzazione tabelle
 * plug-in per l'indicizzazione di tabelle esterne 
 * 
 *
 * Autore : renatoformato@virgilio.it
 * © 2006-2007 - Distribuito sotto licenza GNU/GPL
 *
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_INDEX_TABLES',(_DIR_PLUGINS.end($p)));


function Ind_Tab_Agg_ajouter_boutons($b) {
	$b["configuration"]->sousmenu["tabelle_aggiuntive"] = new Bouton("loupe.png","Indicizzazione");
	return $b;
}


?>

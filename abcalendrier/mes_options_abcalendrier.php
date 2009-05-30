<?php
/***************************************************************/
/*    Definizione del campo extra 'evento' per le brevi
/***************************************************************/
//error_reporting(E_ALL);

if (!defined('_DIR_PLUGIN_ABCALENDRIER')){
   $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
   define('_DIR_PLUGIN_ABCALENDRIER',(_DIR_PLUGINS.end($p)));
}
//change this to set the way multi-events date are linked in the agenda
/*

En cas de plusieurs évenements le meme jour est possible de laisser les titres en le balise ’title’ , comme cest le cas pour les jours dotés d’un seul évenement, et de créer un link vers une squelette ev_du_jour reprenant les evenenemts du jour.
define('MULTIEVENINTITLE','oui');

*/

define('MULTIEVENINTITLE','non');


?>
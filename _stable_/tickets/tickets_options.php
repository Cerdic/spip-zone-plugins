<?php

if (!defined('_DIR_PLUGIN_TICKETS')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_TICKETS',(_DIR_PLUGINS.end($p)).'/');
} 

if (!defined("_TICKETS_PREFIX")) define ("_TICKETS_PREFIX", "tickets");


function tickets_texte_severite ($niveau) {
	if ($niveau == 1) return "bloquant";
	else if ($niveau == 2) return "important";
	else if ($niveau == 3) return "normal";
	else if ($niveau == 4) return "peu important";
}


function tickets_texte_type ($niveau) {
	if ($niveau == 1) return "probl&egrave;me";
	else if ($niveau == 2) return "am&eacute;lioration";
	else if ($niveau == 3) return "t&acirc;che";
}
function tickets_texte_statut ($niveau) {
	if ($niveau == "redac") return "en cours de r&eacute;daction";
	else if ($niveau == "ouvert") return "ouvert et discut&eacute;";
	else if ($niveau == "resolu") return "résolu";
	else if ($niveau == "ferme") return "fermé";
}

function tickets_bouton_statut ($niveau) {
	if ($niveau == "redac") $img = "puce-blanche.gif";
	else if ($niveau == "ouvert") $img = "puce-orange.gif";
	else if ($niveau == "resolu") $img = "puce-verte.gif";
	else if ($niveau == "ferme")  $img = "puce-poubelle.gif";
	
	return "<img src='../prive/images/$img' alt='$niveau' />";
}

function tickets_bouton_severite ($niveau) {
	if ($niveau == 1) $img = "puce-rouge-breve.gif";
	else if ($niveau == 2) $img = "puce-orange-breve.gif";
	else if ($niveau == 3) $img = "puce-verte-breve.gif";
	else if ($niveau == 4)  $img = "puce-poubelle-breve.gif";
	
	return "<img src='../prive/images/$img' alt='$niveau' />";
}



?>
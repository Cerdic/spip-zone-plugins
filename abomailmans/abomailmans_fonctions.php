<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function abomailman_inputhidden ($texte) {
	$liste = explode ("@", $texte);
	$nom_liste_join = $liste[0] ."-join";
	$domaine = $liste[1];
	$abonnement = $nom_liste_join . "@" . $domaine;

	return $texte = "<input name=\"listes[]\" value=\"" . $abonnement . "\" type=\"hidden\" />";	
}

function nettoie_chemin($chemin){
	$liste = explode ("/", $chemin);
	$dernier=count($liste)-1;
	$chemin = str_replace('.html','',$liste[$dernier]);
	$liste2 = explode('&',$chemin);
	$chemin = $liste2[0];
	return $chemin;
}

function noextension($chemin){
	return str_replace('.html','',$chemin);
}

function recup_param($chemin){
	$a = explode('&', $chemin);
	$i = 1;
	while ($i < count($a)) { 
	    $retour.= "&".htmlspecialchars(urldecode($a[$i]));
	    $i++;
	}	
	return $retour;
}

function array_param($params){
	parse_str($params,$output);
	return $output;	
}

function moins30($date) {
	$moins30 = date('Y-m-d h:m:s', time()-24*3600*30);  
	return $moins30;
}

?>
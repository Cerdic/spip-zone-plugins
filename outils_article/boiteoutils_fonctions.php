<?php



// Toute personne censee se detournerait de la rfc 822... et pourtant
function date_rfc822($date_heure) {
	list($annee, $mois, $jour) = recup_date($date_heure);
	list($heures, $minutes, $secondes) = recup_heure($date_heure);
	$time = mktime($heures, $minutes, $secondes, $mois, $jour, $annee);
	$timezone = sprintf('%+03d',intval(date('Z')/3600)).'00';
	return date("D, d M Y H:i:s", $time)." $timezone";
}

// renvoie une couleur fonction de l'age du forum
function dec2hex($v) {
	return substr('00'.dechex($v), -2);
}

function age_style($date) {
	
	// $decal en secondes
	$decal = date("U") - date("U", strtotime($date));
 
	// 3 jours = vieux
	$decal = min(1.0, sqrt($decal/(3*24*3600)));
 
	// Quand $decal = 0, c'est tout neuf : couleur vive
	// Quand $decal = 1, c'est vieux : bleu pale
	$red = ceil(128+127*(1-$decal));
	$blue = ceil(130+60*$decal);
	$green = ceil(200+55*(1-$decal));
 
	$couleur = dec2hex($red).dec2hex($green).dec2hex($blue);
 
	return 'background-color: #'.$couleur.';';
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :    accord_pluriel
 *   +----------------------------------+
 *    Date : mercredi 16 avril 2003
 *    Auteur :  Roustoubi (roustoubi@tiscali.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Accorde le nom singulier passé en paramètre avec le nombre qui le quantifie
 *    Exemple : [#TOTAL_BOUCLE (#TOTAL_BOUCLE|accord_pluriel{article})]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=111
*/

function accord_pluriel ($nombre, $nom1='', $nom2='') {
	if ($nom1=='') { return "Erreur filtre <b>&laquo; accord_pluriel &raquo;</b> : probl&egrave;me de param&egrave;tre"; }
	if ($nom2!='') {
		$nom2 = " ".$nom2;
		$nom2s = $nom2."s";
	}
	if ($nombre == "0" OR $nombre == "1") {
		$texte = $nombre."&nbsp;".$nom1.$nom2 ;
	}
	else {
		$texte = $nombre."&nbsp;".$nom1."s".$nom2s ;
	}
	return $texte ; 
}
// FIN du Filtre accord_pluriel
?>
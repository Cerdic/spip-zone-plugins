<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* API pour la gestion de profils utilisateurs
* Redefinir la fonction geoportail_profil_par_defaut 
* pour definir un profil par utilisateur (pour permettre 
* un affichage automatique sur une zone).
*
**/

include_spip('base/geoportail');

/** 
	Gestion d'un profil particulier :
	On va chercher la zone dans le chemin, sinon on va chercher un profil par defaut
*/
function geoportail_profil ($att, $filtre=null)
{	static $profil;
	// On est dans le cas d'un filtre : [(#REM|geoportail_profil{att})]
	if ($filtre) $att=$filtre;
	// Deja fait ?
	if ($profil) return $profil[$att];
	// Recherche du profil utilisateur
	$f = 'geoportail_profil_par_defaut';
	if (!function_exists($f)) $f .= '_dist';
	$profil = $f();
	// Recherche si la zone est precisee dans le chemin
	$profil['id_zone'] = $profil['zone'];
	$zone = strtoupper($_GET['zone']);
	if ($zone) $profil['zone'] = $zone;
//	else $profil['zone'] = 'FXX';
	// OK
	return $profil[$att];
}

/**
	Profil par defaut (FXX)
*/
function geoportail_profil_par_defaut_dist()
{	$zone = $GLOBALS['meta']['geoportail_zone'];
	return array('zone'=>$zone?$zone:'FXX', 'lon'=>null, 'lat'=>null, 'echelle'=>null);
}

?>
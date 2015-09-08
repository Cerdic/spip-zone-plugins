<?php
/**
 *
 * Fonction reprise de SPIP (plugin dist urls_etendues)
 *
 *
**/
// pour url_nettoyer
include_spip('action/editer_url'); 
     
/*
 *
 * [(#DATE_DEBUT|duree{#DATE_FIN,minutes})]
 * [(#DATE_DEBUT|duree{#DATE_FIN,days})]
 *
 * 
*/
function duree($date_debut,$date_fin, $formate="minutes"){
	$start_date = new DateTime($date_debut);
	$since_start = $start_date->diff(new DateTime($date_fin));
	switch ($formate) {
	    case 'years':
		$duree = $since_start->y;
		break;
	    case 'months':
		$duree = $since_start->m;
		break;
	    case 'days':
		$duree = $since_start->d;
		break;
	    case 'hours':
		$duree = $since_start->h;
		break;
	    case 'minutes':
		$duree = $since_start->days * 24 * 60;
		$duree += $since_start->h * 60;
		$duree += $since_start->i;
		break;
	    case 'seconds':
		$duree = $since_start->days * 24 * 60;
		$duree += $since_start->h * 60;
		$duree += $since_start->i * 60;
		$duree += $since_start->s;
		break;
        }
        
	return $duree;
}

//retourne le jour en anglais d'après un chiffre, ne sert pas, juste pour le fun
function jddayofweek_perso($num){
	return date('l', strtotime("Sunday + $num Days"));
}

//depuis un nom de jour français retourne un chiffre 
//sinon le jour en chiffre
function convert_jour($jour_en_lettres){
	if(is_string($jour_en_lettres)){
	$jour=mb_strtolower($jour_en_lettres);
		switch ($jour) {
			case 'dimanche': return 1;
			case 'lundi': return 2;
			case 'mardi': return 3;
			case 'mercredi':return 4;
			case 'jeudi':return 5;
			case 'vendredi':return 6;
			case 'samedi':return 7;
		}
        } 
        return $jour_en_lettres;
}

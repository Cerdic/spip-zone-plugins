<?php
/**
 *
 * Fonction reprise de SPIP (plugin dist urls_etendues)
 *
 * todo, sortir la fonction url_nettoyer pour être réutilisable ici ou ailleurs
 *
 *
**/
function temps_url_nettoyer($titre,$longueur_maxi,$longueur_min=0,$separateur='-',$filtre=''){
	include_spip('action/editer_url'); 
	return url_nettoyer($titre,$longueur_maxi,$longueur_min,$separateur,$filtre);
}

/*
 *
 * [(#DATE_DEBUT|duree{#DATE_FIN,minutes})]
 * [(#DATE_DEBUT|duree{#DATE_FIN,days})]
 *
 * %i% minutes
 * %R%a days
 * à remplacer par case
*/
function duree($date_debut,$date_fin, $formate="minutes"){
	$start_date = new DateTime($date_debut);
	$since_start = $start_date->diff(new DateTime($date_fin));
	/*
	echo $since_start->days.' days total<br>';
	echo $since_start->y.' years<br>';
	echo $since_start->m.' months<br>';
	echo $since_start->d.' days<br>';
	echo $since_start->h.' hours<br>';
	echo $since_start->i.' minutes<br>';
	echo $since_start->s.' seconds<br>';
	*/
	/*
	if($formate=='y'){
		$duree = $since_start->y;
	}
	elseif($formate=='days'){
		$duree = $since_start->d;
	}
	elseif($formate=='hours'){
		$duree = $since_start->h;
	}
	elseif($formate=='minutes'){
		$duree = $since_start->days * 24 * 60;
		$duree += $since_start->h * 60;
		$duree += $since_start->i;
	}
	*/
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

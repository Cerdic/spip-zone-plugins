<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function simplecal_affdates($date_debut, $date_fin){
	// sécurisation des paramètres
    $dates_vide = array('', '0000-00-00 00:00:00');
    if (!in_array($date_debut, $dates_vide)){
        $date_debut = strtotime($date_debut);
        $d = date("Y-m-d", $date_debut);
    } else {
        $date_debut = '';
        $d = '';
    }
    if (!in_array($date_fin, $dates_vide)){
        $date_fin = strtotime($date_fin);
        $f = date("Y-m-d", $date_fin);
    } else {
        $date_fin = $date_debut;
        $f = $d;
    }
    // ---
	    
	$s = "";
    
    // meme jour : vendredi 12 novembre
	if ($d == $f) { 
		$s = nom_jour($d, $abbr)." ".affdate_jourcourt($d);
	}
	// meme annee et mois, jours differents : du 3 au 12 novembre
    else if ((date("Y-m",$date_debut)) == date("Y-m",$date_fin)) { 
        $s = _T("simplecal:date_du_au", array('date_debut'=>jour($d), 'date_fin'=>affdate_jourcourt($f)));
	}
	// meme annee, mois et jours differents : du 30 novembre au 10 décembre
    else if ((date("Y",$date_debut)) == date("Y",$date_fin)) { 
        $s = _T("simplecal:date_du_au", array('date_debut'=>affdate_jourcourt($d), 'date_fin'=>affdate_jourcourt($f)));
	}
	// tout different : du 25 décembre 2009 au 2 janvier 2010
    else { 
        $s = _T("simplecal:date_du_au", array('date_debut'=>affdate($d), 'date_fin'=>affdate($f)));
	}
    
	return $s;	
}

function simplecal_afftexteref($type, $id_objet){
	$texte = "";
    if ($type && $id_objet){
        $row = sql_fetsel("o.texte", "spip_".$type."s as o", "o.id_".$type."=".$id_objet);
        $texte = $row['texte'];
    }
    // interpreter la syntaxe SPIP
    $texte = propre($texte);
    
	return $texte;	
}

function simplecal_date_plus($date, $nb_jour){
	$date_now = date('Y-m-d H:i:s');
    
    $jour = jour($date);
    $mois = mois($date);
	$annee = annee($date);
    $heure = 0;
    $minute = 0;
    $seconde = 0;
    
    $date_plus = date("Y-m-d", mktime($heure, $minute, $seconde, $mois, $jour+$nb_jour, $annee));
            
	return $date_plus;	
}

function simplecal_date_moins($date, $nb_jour){
	$date_now = date('Y-m-d H:i:s');
    
    $jour = jour($date);
    $mois = mois($date);
	$annee = annee($date);
    $heure = 0;
    $minute = 0;
    $seconde = 0;
    
    $date_plus = date("Y-m-d", mktime($heure, $minute, $seconde, $mois, $jour-$nb_jour, $annee));
            
	return $date_plus;	
}

?>
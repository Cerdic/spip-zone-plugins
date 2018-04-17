<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction depreciée
function simplecal_affdates($date_debut, $date_fin, $horaire='non'){
	return affdate_debut_fin($date_debut, $date_fin, $horaire);	
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
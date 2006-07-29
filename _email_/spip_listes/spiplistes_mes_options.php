<?php

//$dossier_squelettes = " "; 


//
// Definition de tous les extras possibles
//

$champs_extra = true;

if (!is_array($GLOBALS['champs_extra'])) $GLOBALS['champs_extra'] = Array ();

	$GLOBALS['champs_extra']['auteurs']['abo'] = "radio|brut|Format|Html,Texte,D&eacute;sabonnement|html,texte,non" ;
	$GLOBALS['champs_extra']['articles']['squelette'] = 'bloc|propre|Patron' ;			

if (!is_array($GLOBALS['champs_extra_proposes'])) $GLOBALS['champs_extra_proposes'] = Array ();		
		
	$GLOBALS['champs_extra_proposes']['auteurs']['tous'] = 'abo' ;
	$GLOBALS['champs_extra_proposes']['articles']['0'] = 'squelette' ;
		

//Balises Spip-listes

function calcul_MELEUSE_CRON() {
  global $include_ok;
   if(!$include_ok) {
include_spip("inc/meleuse-cron");
$include_ok = true;
}
   return '';
}

function balise_MELEUSE_CRON($p) {
   $p->code = "calcul_MELEUSE_CRON()";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_SITE() {
   $date_art=spip_query("SELECT date,titre FROM spip_articles WHERE statut='publie' ORDER BY date DESC LIMIT 0,1");
   $date_art=spip_fetch_array($date_art);
   $date_art= $date_art['date'];
   
   $date_bre=spip_query("SELECT date_heure,titre FROM spip_breves WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_bre=spip_fetch_array($date_bre);
   $date_bre= $date_bre['date_heure'];
   
   $date_modif= ($date_bre>$date_art)? $date_bre : $date_art ;   
   return  $date_modif;
}

function balise_DATE_MODIF_SITE($p) {
   $p->code = "calcul_DATE_MODIF_SITE()";
   $p->statut = 'php';
   return $p;
}


include('inc/options_spip_listes.php');

?>

<?php
//
// Creation / ajout du champ extra auteur pour l'abonnement
//

if (!is_array($GLOBALS['champs_extra'])) $GLOBALS['champs_extra'] = Array ();
$GLOBALS['champs_extra']['auteurs']['abo'] = "radio|brut|Format|Html,Texte,D&eacute;sabonnement|html,texte,non";
//la ligne suivante serait ideale, mais ca retourne l'admin de spip 1.9...
//$GLOBALS['champs_extra']['auteurs']['abo'] = _T("spiplistes:options") ;

if (!is_array($GLOBALS['champs_extra_proposes'])) $GLOBALS['champs_extra_proposes'] = Array ();		

if (!isset($GLOBALS['champs_extra_proposes']['auteurs']['tous'])){		
	$GLOBALS['champs_extra_proposes']['auteurs']['tous'] = 'abo' ;
	}else{
	$GLOBALS['champs_extra_proposes']['auteurs']['tous'] = $GLOBALS['champs_extra_proposes']['auteurs']['tous']."|abo";
	}
	
if (isset($GLOBALS['champs_extra_proposes']['auteurs']['inscription'])){		
	$GLOBALS['champs_extra_proposes']['auteurs']['inscription'] = $GLOBALS['champs_extra_proposes']['auteurs']['inscription']."|abo";
	}
		
include_spip('inc/extra_plus');

//Balises Spip-listes

function balise_MELEUSE_CRON($p) {
   $p->code = "''";
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

function calcul_DATE_MODIF_FORUM() {
   $date_f=spip_query("SELECT date_heure,titre FROM spip_forum WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_f=spip_fetch_array($date_f);
   $date_f= $date_f['date_heure'];
   
   return  $date_f;
}

function balise_DATE_MODIF_FORUM($p) {
   $p->code = "calcul_DATE_MODIF_FORUM()";
   $p->statut = 'php';
   return $p;
}

//utiliser le cron pour envoyer les messages en attente
function spiplistes_taches_generales_cron($taches_generales){
	$taches_generales['spiplistes_cron'] = 20;
	return $taches_generales;
}

$GLOBALS['spiplistes_version'] = "SPIP-listes 1.9b2";

include_spip('inc/options_spip_listes');
?>
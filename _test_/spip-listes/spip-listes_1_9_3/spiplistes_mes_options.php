<?php

include_spip("inc/plugin_globales_lib");

//nombre de processus d'envoi simultanes
@define('_SPIP_LISTE_SEND_THREADS',1);

// virer les echo, a reprendre plus tard correctement
// avis aux spécialistes !!
define('_SIGNALER_ECHOS', false); // horrible 

define("_DIR_PLUGIN_SPIPLISTES_IMG_PACK", _DIR_PLUGIN_SPIPLISTES."img_pack/");

define("_SPIPLISTES_LOTS_PERMIS", "1;5;10;30;100");

define("_SPIPLISTES_PUBLIC_LIST", "liste");
define("_SPIPLISTES_PRIVATE_LIST", "inact");
define("_SPIPLISTES_TRASH_LIST", "poublist");
// statuts des listes tels qu'affichées en liste 
define("_SPIPLISTES_LISTES_STATUTS", _SPIPLISTES_PRIVATE_LIST.";"._SPIPLISTES_PUBLIC_LIST.";"._SPIPLISTES_TRASH_LIST);

// charsets:
// charsets autorisés :
define("_SPIPLISTES_CHARSETS_ALLOWED", "iso-8859-1;iso-8859-9;iso-8859-6;iso-8859-15;utf-8");
define("_SPIPLISTES_CHARSET_ENVOI", "iso-8859-1"); // pour historique
define("_SPIPLISTES_CHARSET_DEFAULT", _SPIPLISTES_CHARSET_ENVOI);

// les formats d'envoi autorisés, ou non pour pseudo-désabonné
define("_SPIPLISTES_FORMATS_ALLOWED", "html;texte;non");

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
	$taches_generales['spiplistes_cron'] = 10 ;
	return $taches_generales;
}

$spiplistes_v = $GLOBALS['meta']['spiplistes_version'] ;

//afficher la version de spip_listes dans le pied de page
if($spiplistes_v == 1.91)
$GLOBALS['spiplistes_version'] = "SPIP-listes 1.9.1";
if($spiplistes_v >= 1.92)
$GLOBALS['spiplistes_version'] = "SPIP-listes $spiplistes_v";


include_spip('inc/options_spip_listes');
?>
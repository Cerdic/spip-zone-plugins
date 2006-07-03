<?php

//$dossier_squelettes = " "; 


//
// Definition de tous les extras possibles
//

$champs_extra = true;

	$GLOBALS['champs_extra'] = Array (
		'auteurs' => Array (
				"abo" => "radio|brut|Format|Html,Texte,D&eacute;sabonnement|html,texte,non"

			),
			
		'articles' => Array (
				'squelette' => 'bloc|propre|Bibliographie'

			)

		);
		
		$GLOBALS['champs_extra_proposes'] = Array (
'auteurs' => Array (
		'tous' => 'abo',
		'inscription' => 'abo'
	        ),
'articles' => Array (
		'0' => 'squelette',
		'tous' => ''
		
                )
				
);

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

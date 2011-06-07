<?php

function exec_gestdidaspip(){
include_spip ("inc/presentation");

	// Recupere les donnees
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page('Gestion des projets Didapage', '', '', '');
	echo gros_titre('Gestion des projets Didapage','',false);
	echo debut_gauche("",true);
	echo debut_droite("",true);



//vérification de l'accès à la page de gestion des projets 
@ $acces_gestion=lire_config('didaspip/accesdida');
if (!isset($acces_gestion)) $acces_gestion="oui";

global $connect_statut;
if (($GLOBALS['connect_statut'] != "0minirezo" OR !$GLOBALS["connect_toutes_rubriques"]) AND ($acces_gestion!="oui")) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
//charger les chaines de caractères
include("dida_lang.php");
//charger les fonctions
include("dida_fonctions.php");
//création du répertoire de didapages à la première installation dans le doSsier IMG
if (!is_dir(_DIR_IMG.'/didapages')) mkdir(_DIR_IMG.'/didapages');
include("dida_pagecours.php");
echo fin_gauche(), fin_page();
}
?>

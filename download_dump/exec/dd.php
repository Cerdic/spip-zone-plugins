<?php
/*
 * Plugin dd
 *
 * Auteur : phil
 * (c) 2007 - Distribue sous licence LGPL
 *
 */



if (!defined("_ECRIRE_INC_VERSION")) return;
if ($GLOBALS['spip_version_branche']>"1.9.2") include_spip('inc/vieilles_defs');
function exec_dd_dist($class = null)
{
  global $connect_statut, $connect_login, $connect_toutes_rubriques, $couleur_foncee, $flag_gz, $options,$supp;

 $commencer_page = charger_fonction('commencer_page', 'inc');
 echo $commencer_page(_T('dd:titre_dump_download'));

 echo "<br />";

 if ($connect_statut != '0minirezo' ){
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
 }
	echo "<br /><br />";
	gros_titre(_T('dd:titre_admin_dump_download'));
	
	if ($connect_toutes_rubriques) {
		//echo barre_onglets("administration", "sauver");
		debut_gauche();
		debut_boite_info();
		echo _T('dd:info_gauche_admin_dump_download');
		fin_boite_info();
		$repertoire = _DIR_DUMP;
		if(!@file_exists($repertoire)) {
			$repertoire = preg_replace(','._DIR_TMP.',', '', $repertoire);
			$repertoire = sous_repertoire(_DIR_TMP, $repertoire);
		}
		$dir_dump = $repertoire;
	} else {
		debut_gauche();
		$dir_dump = determine_upload();
	}

	$dir_dump = joli_repertoire($dir_dump);
	
 debut_droite();

 debut_cadre_relief();


 if ($connect_toutes_rubriques) {

 if (substr(_DIR_IMG, 0, strlen(_DIR_RACINE)) === _DIR_RACINE)
   $dir_img = substr(_DIR_IMG,strlen(_DIR_RACINE));
 else
   $dir_img = _DIR_IMG;
 
 	$liste_dump = preg_files(_DIR_DUMP,'\.xml(\.gz)?$',50,false);
 	$selected = end($liste_dump);
	$selected_fichier = substr($selected,strlen(_DIR_DUMP));
	$lien = "../".$dir_img.$selected_fichier;
	
	if ($supp!='oui') 
	{
	copy($selected,$lien);
	
	//Telecharger
 	$liste_choix = "<ul>"; 
 	//foreach($liste_dump as $key=>$fichier){
 		$affiche_fichier = substr($fichier,strlen(_DIR_DUMP));
 		$liste_choix.="\n<li><a href='"
		. $lien
		. "' id='dump_$key' "
		. "/>"
		.   $file = str_replace('/', ' / ', $selected_fichier)
		. '&nbsp;&nbsp; ('
		. _T('dd:taille_octets',
		     array('taille' => number_format(filesize($selected), 0, ' ', ' ')))
		. ')</a></li>';
		
	echo	"\n<table border='0' cellspacing='1' cellpadding='8' width=\"100%\">",
	"<tr><td style='background-color: #eeeecc;'><b>",
	"<span style='color: #000000;' class='verdana1 spip_medium'>", _T('dd:texte_dump_download')."</span></b></td></tr>",
	"<tr><td class='serif'>\n",
	"\n<p style='text-align: justify;'> ",
	_T('dd:texte_download_dump', array('dd:dossier' => '<i>'.$dir_dump.'</i>')),
	  '</p>',
	_T('dd:entree_nom_fichier'),
	$liste_choix,
	"\n",
	"\n</ul>";
	  
	echo 
	  "\n</td></tr>",
	  "</table>";
	
	//Supprimer	  
		$liste_choix_supprime = "<ul>"; 
 		$liste_choix_supprime.="\n<li><a href='?exec=dd&amp;supp=oui'"
		. "/> Supprimer "
		.   $file = str_replace('/', ' / ', $selected_fichier)
		. '&nbsp;&nbsp; ('
		. _T('dd:taille_octets',
		     array('taille' => number_format(filesize($selected), 0, ' ', ' ')))
		. ')</a></li>';
		
	echo	"\n<table border='0' cellspacing='1' cellpadding='8' width=\"100%\">",
	"<tr><td style='background-color: #eeeecc;'><b>",
	"<span style='color: #000000;' class='verdana1 spip_medium'>", _T('dd:texte_supprime_download')."</span></b></td></tr>",
	"<tr><td class='serif'>\n",
	"\n<p style='text-align: justify;'> ",
	_T('dd:texte_download_dump_supprimer', array('dd:dossier' => '<i>'.$dir_dump.'</i>')),
	  '</p>',
	_T('dd:entree_nom_fichier_supprimer'),
	$liste_choix_supprime,
	"\n",
	"\n</ul>";
	  
	echo 
	  "\n</td></tr>",
	  "</table>";
	  
	}

	elseif ($supp=='oui') {
	if (unlink($lien)) {
	echo	"\n<table border='0' cellspacing='1' cellpadding='8' width=\"100%\">",
	"<tr><td style='background-color: #eeeecc;'><b>",
	"<span style='color: #000000;' class='verdana1 spip_medium'>", _T('dd:texte_supprime_ok')."</span></b></td></tr>",
	"<tr><td class='serif'>\n",
	"\n<p style='text-align: justify;'> ",
	_T('dd:texte_download_dump_supprimer_ok', array('dossier' => '<i>'.$dir_dump.'</i>')),
	  '</p>',
	"\n";
	  
	echo 
	  "\n</td></tr>",
	  "</table>";
	  }
	  
	}

 }



fin_cadre_relief();

echo "<br />";

echo fin_gauche(), fin_page();



}

function nom_fichier_dump()
{
	global $connect_toutes_rubriques;

	if ($connect_toutes_rubriques AND file_exists(_DIR_DUMP))
		$dir = _DIR_DUMP;
	else $dir = determine_upload();

	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(",\W,is","_", substr(trim($GLOBALS['meta']['nom_site']),0,20))
	  : 'spip';

	$site .= '_' . date('Ymd');

	$nom = $site;
	$cpt=0;
	while (file_exists($dir. $nom . ".xml") OR
	       file_exists($dir. $nom . ".xml.gz")) {
		$nom = $site . sprintf('_%03d', ++$cpt);
	}
	return $nom;
}

?>

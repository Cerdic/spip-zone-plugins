<?php

#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('jeux_utils');
// tableau de parametres exploitables par les plugins
global $jeux_config;

// fonction pre-traitement, pipeline pre_propre
// insersion de quelques balises de reconnaissance
function jeux_pre_propre($texte) { 
	// s'il n'est pas present dans un formulaire envoye,
	// l'identifiant du jeu est choisi au hasard...
	// ca peut servir en cas d'affichage de plusieurs articles par page.
	// en passant tous les jeux en ajax, ce ne sera plus la peine.
	static $indexJeux = null;
	if(!isset($GLOBALS['debut_index_jeux']))
		$GLOBALS['debut_index_jeux'] = isset($_POST['debut_index_jeux'])?$_POST['debut_index_jeux']:rand(10000, 99999);

	if (strpos($texte, _JEUX_DEBUT)===false || strpos($texte, _JEUX_FIN)===false) return $texte;
	if(isset($indexJeux)) ++$indexJeux;
		else $indexJeux = $GLOBALS['debut_index_jeux'];

	// isoler le jeu...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $texte, 2); 
	list($texte, $texteApres) = explode(_JEUX_FIN, $suite, 2); 

	// ...decoder le texte obtenu en fonction des signatures et inclure le jeu
	$liste = jeux_liste_les_jeux($texte);
	jeux_decode_les_jeux($texte, $indexJeux);
	// calcul des fichiers necessaires pour le header
	if(count($liste)) {
		// on oblige qd meme jeux.css et jeux.js si un jeu est detecte
		$header = jeux_stylesheet_html('jeux') ."\n". jeux_javascript('jeux') . "\n";
		// css et js des jeux detectes
		foreach($liste as $jeu) $header .= jeux_stylesheet($jeu) . "\n";
		foreach($liste as $jeu) $header .= jeux_javascript($jeu) . "\n";
		$header = htmlentities(preg_replace(",\n+,", "||", trim($header)));
		$header = jeux_rem('JEUX-HEAD', count($liste), base64_encode($header));
	} else $header = '';

	return $texteAvant . $header
		.jeux_rem('PLUGIN-DEBUT', $indexJeux, join('/', $liste))
		."<div id=\"JEU$indexJeux\" class=\"jeux_global\">$texte</div>"
#		."<div id=\"JEU$indexJeux\" class=\"jeux_global ajax\">$texte</div>"
		.jeux_rem('PLUGIN-FIN', $indexJeux).jeux_pre_propre($texteApres);
}

// fonction post-traitement, pipeline post_propre
// les jeux sont reellement decryptes
function jeux_post_propre($texte) { 
	$texte = echappe_retour($texte, 'JEUX');

	$sep1 = '['._JEUX_POST.'|'; $sep2 = '@@]';
	if (strpos($texte, $sep1)===false || strpos($texte, $sep2)===false) return $texte;
	
	// isoler les parametres...
	list($texteAvant, $suite) = explode($sep1, $texte, 2);
	list($texte, $texteApres) = explode($sep2, $suite, 2);
	list($fonc, $texteJeu, $indexJeux) = explode('|', $texte, 3);
	if (function_exists($fonc)) $texte = $fonc($texteJeu, $indexJeux);

	return $texteAvant.$texte.jeux_post_propre($texteApres);
}


// pipeline header_prive
function jeux_header_prive($flux){
	include_spip('public/assembler');
	include_spip('jeux_utils');
	global $jeux_header_prive, $jeux_javascript_prive;
	$flux .= _JEUX_HEAD1;
	$flux .= "<link rel='stylesheet' href='../spip.php?page=jeux.css' type='text/css' media='projection, screen' />";
	foreach($jeux_header_prive as $s) $flux .= jeux_stylesheet($s);
	foreach($jeux_javascript_prive as $s) $flux .= jeux_javascript($s);
	return $flux;
}

// pipeline insert_head
function jeux_insert_head($flux){
	include_spip('jeux_utils');
	return $flux . _JEUX_HEAD2;
}

// pipeline affiche_gauche

// Le pipeline affichage_final, execute a chaque hit sur toute la page
// Recherche tous les "title=JEUX-HEAD(...)" --> et incorporation a la place de _JEUX_HEAD2
// dans <head> des fichiers js et css necessaires.
function jeux_affichage_final($flux) {
	preg_match_all(",'JEUX-HEAD-#[0-9]+ `([^>]*)`',", $flux, $matches, PREG_SET_ORDER);
	if(!count($matches)) return $flux;
	$liste = array(_JEUX_HEAD2);
	foreach ($matches as $val) $liste = array_merge($liste, explode('||', base64_decode($val[1])));
	$liste = array_unique($liste);
 	$header = html_entity_decode(join("\n",$liste));
	return str_replace(_JEUX_HEAD2, $header."\n\n", $flux);
}



function jeux_affiche_droite($flux){
	if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))){
		include_spip('exec/inc_boites_infos');
		$flux['data'] .= boite_info_jeux_edit();
	}
	
	if (in_array($flux['args']['exec'],array('auteur_infos'))){
		include_spip('exec/inc_boites_infos');			
		$flux['data'] .= boite_infos_spip_auteur($flux['args']['id_auteur']);  
	}
	return $flux;
}

function jeux_taches_generales_cron($taches_generales){
	$taches_generales['jeux_nettoyer_base'] = 3600*48;
	return $taches_generales;
}



// SPIP >= 2.0 : c'est genie_ et pas cron_
function genie_jeux_nettoyer_base($t) {
	// plugins Corbeille ou Couteau Suisse : pas d'optimisation automatique
	if(defined('_DIR_PLUGIN_CORBEILLE') || defined('_CORBEILLE_SANS_OPTIM')) return;
	$sel = sql_select('id_jeu', 'spip_jeux', "statut='poubelle'");
	while ($r = sql_fetch($sel)) $ids[] = $r['id_jeu'];
	if (count($ids)) {
		sql_delete('spip_jeux', sql_in('id_jeu', $ids));
		sql_delete('spip_jeux_resultats', sql_in('id_jeu', $ids));
		spip_log('Le plugin Jeux vide la poubelle WHERE `id_jeu` IN '.join(',',$ids),'jeux');
	}
}

// SPIP >=2.0 : association type objet/table
function jeux_declarer_tables_objets_surnoms($surnoms) {
	$surnoms['jeu'] = 'jeux';
	return $surnoms;
}

?>

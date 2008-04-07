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

// fonction pre-traitement
function jeux_pre($chaine, $indexJeux){ 
	if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return $chaine;
	
	// isoler le jeu...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
	list($chaine, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	
	// ...decoder le texte obtenu en fonction des signatures et inclure le jeu
	$liste = jeux_liste_des_jeux($chaine, $indexJeux);
	// calcul des fichiers necessaires pour le header
	if(count($liste)) {
		// on oblige qd meme jeux.css et layer.js si un jeu est detecte
		$header = jeux_stylesheet_html('jeux') ."\n". jeux_javascript('layer') . "\n";
		// css et js des jeux detectes
		foreach($liste as $jeu) $header .= jeux_stylesheet($jeu) . "\n";
		foreach($liste as $jeu) $header .= jeux_javascript($jeu) . "\n";
		$header = htmlentities(preg_replace(",\n+,", "||", trim($header)));
		$header = jeux_rem('JEUX-HEAD', count($liste), base64_encode($header));
	} else $header = '';
//
	return $texteAvant . $header
		.jeux_rem('PLUGIN-DEBUT', $indexJeux, join('/', $liste))
		."<a name=\"JEU$indexJeux\"></a><div class=\"jeux_global\">$chaine</div>"
		.jeux_rem('PLUGIN-FIN', $indexJeux).jeux_pre($texteApres, ++$indexJeux);
}

// fonction post-traitement
function jeux_post($chaine){
	$chaine=echappe_retour($chaine, 'JEUX');

	$sep1 = '['._JEUX_POST.'|'; $sep2 = '@@]';
	if (strpos($chaine, $sep1)===false || strpos($chaine, $sep2)===false) return $chaine;
	
	// isoler les parametres...
	list($texteAvant, $suite) = explode( $sep1, $chaine, 2);
	list($chaine, $texteApres) = explode($sep2, $suite, 2);
	$params = explode('|', $chaine, 3);
	$fonc = $params[0];
	if (function_exists($fonc)) $chaine = $fonc($params[1], $params[2]);

	return $texteAvant.$chaine.jeux_post($texteApres);
}

// a la place de jeux(), pour le deboguage...
function jeux2($chaine, $indexJeux){
 if (strpos($chaine, _JEUX_DEBUT)!==false && strpos($chaine, _JEUX_FIN)!==false) {
	ob_start();
	$chaine = jeux_pre($chaine, $indexJeux);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
 }
 return $chaine;
}

// pipeline pre_propre
function jeux_pre_propre($texte) { 
	// s'il n'est pas present dans un formulaire envoye,
	// l'identifiant du jeu est choisi au hasard...
	// ca peut servir en cas d'affichage de plusieurs articles par page.
	// en passant tous les jeux en ajax, ce ne sera plus la peine.
	$GLOBALS['debut_index_jeux'] = isset($_POST['debut_index_jeux'])?$_POST['debut_index_jeux']:rand(1, 65000);
	return jeux_pre($texte, $GLOBALS['debut_index_jeux']);
}

// pipeline pre_propre
function jeux_post_propre($texte) { 
	return jeux_post($texte);
}

// pipeline header_prive
function jeux_header_prive($flux){
	include_spip('public/assembler');
	global $jeux_header_prive, $jeux_javascript_prive;
	$flux .= _JEUX_HEAD1;
	$flux .= "<link rel='stylesheet' href='../spip.php?page=jeux.css' type='text/css' media='projection, screen' />";
	foreach($jeux_header_prive as $s) $flux .= jeux_stylesheet($s);
	foreach($jeux_javascript_prive as $s) $flux .= jeux_javascript($s);
	return $flux;
}

// pipeline insert_head
function jeux_insert_head($flux){
	return $flux . _JEUX_HEAD2;
}

// pipeline affiche_gauche
function jeux_affiche_gauche($flux) {
// correction d'un bug d'affichage
 if (defined('_SPIP19100')) $flux['data'] .="<script type=\"text/javascript\"><!--
document.getElementById('haut-page').childNodes[2].align='center';
--></script>";
	return $flux;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
// Recherche tous les <!-- JEUX-HEAD (...) --> et incorporation a la place de _JEUX_HEAD2
// dans <head> des fichiers js et css necessaires.
function jeux_affichage_final($flux) {
	preg_match_all(",<!-- JEUX-HEAD-#[0-9]+ '([^>]*)' -->,", $flux, $matches, PREG_SET_ORDER);
	if(!count($matches)) return $flux;
	$liste = array();
	foreach ($matches as $val) $liste = array_merge($liste, explode('||', base64_decode($val[1])));
	$liste = array_unique($liste);
	$header = _JEUX_HEAD2
		. html_entity_decode(join("\n",$liste));
	return str_replace(_JEUX_HEAD2, $header, $flux);
}



function jeux_affiche_droite($flux){
	if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))){
	include_spip('exec/inc_boites_infos');	
	$flux['data'] .= boite_info_jeux_edit();
	}
	
	if (in_array($flux['args']['exec'],array('auteur_infos'))){
	include_spip('exec/inc_boites_infos');			
	$r = boite_infos_spip_auteur($flux['args']['id_auteur']);  
	
	}
	
	return $flux;
}

function jeux_taches_generales_cron($taches_generales){
	$taches_generales['jeux_cron'] = 60 * 20; // 20 minutes
	return $taches_generales;
}

function cron_jeux_cron($t,$attente=86400){

	$date = date("YmdHis", time() - $attente);
	include_spip('inc/utils');
	
	$sup = '(';
	$i = 0;
	$requete = spip_query("SELECT id_jeu from spip_jeux WHERE statut='poubelle' and 'date'<".$date);
	
	while ($ligne =spip_fetch_array($requete)){	

		$sup .=($i==0) ?'':',';
		$sup .=$ligne['id_jeu'];
		$i++;
	}
	$sup.=(')');
	if ($sup!='()') {
		spip_query('DELETE FROM spip_jeux WHERE `id_jeu` IN '.$sup);
		spip_query('DELETE FROM spip_jeux_resultats WHERE `id_jeu` IN '.$sup);
		spip_log('suppression jeux poubelle'.$sup);
		}
	
	}

// en 1.9.3 c'est genie_ et pas cron_
function genie_jeux_cron($time) {
	return cron_jeux_cron($time);
}

?>

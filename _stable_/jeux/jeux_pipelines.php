<?php

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

include_spip('jeux_config');
include_spip('jeux_utils');

// tableau de parametres exploitables par les plugins
global $jeux_config;

// fonction pre-traitement
function jeux_pre($chaine, $indexJeux){ 
	if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return $chaine;
	
	// isoler le jeu...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
	list($chaine, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	
	// ...et decoder le texte obtenu en fonction des signatures
	jeux_inclure_et_decoder($chaine, $indexJeux);

	return $texteAvant.jeux_rem('PLUGIN-DEBUT', $indexJeux).$chaine
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

//	$chaine = "OK : {$params[0]} - {$params[1]} - {$regs[2]}";
	
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
	return jeux_pre($texte, 1);
}

// pipeline pre_propre
function jeux_post_propre($texte) { 
	return jeux_post($texte);
}

// pipeline header_prive
function jeux_header_prive($flux){
	global $jeux_header_prive, $jeux_javascript;
	foreach($jeux_header_prive as $s) $flux .= jeux_stylesheet_public($s);
	foreach($jeux_javascript as $s) $flux .= jeux_javascript($s);
	return $flux;
}

// pipeline insert_head
function jeux_insert_head($flux){
	global $jeux_header_public, $jeux_javascript;
	$flux .= "<!-- CSS & JS JEUX -->\n";
	foreach($jeux_header_public as $s) $flux .= jeux_stylesheet_public($s);
	foreach($jeux_javascript as $s) $flux .= jeux_javascript($s);
	return $flux;
}

// pipeline affiche_gauche
function jeux_affiche_gauche($flux) {
if ($GLOBALS['spip_version_code']<1.92) $flux['data'] .="<script type=\"text/javascript\"><!--
document.getElementById('haut-page').childNodes[2].align='center';
--></script>";
	return $flux;
}

?>
<?php

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

include_spip('jeux_config');
include_spip('jeux_utils');

// fonction principale
function jeux($chaine, $indexJeux){ 
	if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return $chaine;
	
	// isoler le jeu...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
	list($chaine, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	
	// ...et decoder le texte obtenu en fonction des signatures
	jeux_inclure_et_decoder($chaine, $indexJeux);

	return $texteAvant.jeux_rem('PLUGIN-DEBUT', $indexJeux).$chaine
		.jeux_rem('PLUGIN-FIN', $indexJeux).jeux($texteApres, ++$indexJeux);
}

// a la place de jeux(), pour le deboguage...
function jeux2($chaine, $indexJeux){
 if (strpos($chaine, _JEUX_DEBUT)!==false && strpos($chaine, _JEUX_FIN)!==false) {
	ob_start();
	$chaine = jeux($chaine, $indexJeux);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
 }
 return $chaine;
}

// pipeline pre_propre
function jeux_pre_propre($texte) { 
	return jeux($texte, 1);
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

?>
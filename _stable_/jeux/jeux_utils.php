<?php
#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// splitte le texte du jeu avec les separateurs concernes
function jeux_split_texte($jeu, &$texte) {
  global $jeux_separateurs;
  $tableau = preg_split('/('.join('|', $jeux_separateurs[$jeu]).')/', trim(_JEUX_TEXTE.$texte), -1, PREG_SPLIT_DELIM_CAPTURE);
  foreach($tableau as $i => $valeur) $tableau[$i] = trim($valeur);
  return $tableau;
}  

// transforme un texte en listes html 
function jeux_listes($texte) {
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') $tableau[$i] = "<li>$valeur</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

function include_jeux($jeu, &$texte, $indexJeux) {
	include_spip('inc/'.$jeu);
	if (function_exists($f = 'jeux_'.$jeu)) $texte = $f($texte, $indexJeux);
}	

function jeux_rem($rem, $index=false) {
 return code_echappement("\n<!-- ".$rem.($index!==false?'-#'.$index:'')." -->\n");
}

?>
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
  $texte = '['._JEUX_TEXTE.']'.trim($texte);
  $expr = '/(\['.join('\]|\[', $jeux_separateurs[$jeu]).'\])/';
  $tableau = preg_split($expr, $texte, -1, PREG_SPLIT_DELIM_CAPTURE);
  foreach($tableau as $i => $valeur) $tableau[$i] = preg_replace('/^\[(.*)\]$/', '\\1', trim($valeur));
  return $tableau;
}  

// transforme un texte en listes html 
function jeux_listes($texte) {
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$valeur) if (($valeur=trim($valeur))!='') $tableau[$i] = "<li>$valeur</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

// ajoute un module jeu a la bibliotheque
function include_jeux($jeu, &$texte, $indexJeux) {
	$fonc = 'jeux_'.$jeu;
	if (!function_exists($fonc)) include_spip('inc/'.$jeu);
	// on est jamais trop prudent !!
	if (function_exists($fonc)) $texte = $fonc($texte, $indexJeux);
}	

// inclut et decode les jeux, si le module inc/lejeu.php est present
function jeux_inclure_et_decoder(&$texte, $indexJeux) {
	global $jeux_signatures;
	foreach($jeux_signatures as $jeu=>$signatures) {
		$ok = false;
		foreach($signatures as $s) $ok |= (strpos($texte, "[$s]")!==false);
		if ($ok) include_jeux($jeu, $texte, $indexJeux);
	}
}

// pour placer des commentaires
function jeux_rem($rem, $index=false) {
 return code_echappement("\n<!-- ".$rem.($index!==false?'-#'.$index:'')." -->\n");
}

// pour inserer un css en public
function jeux_stylesheet_public($b) {
 $f = find_in_path("styles/$b.css");
 return $f?'<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />'."\n":'';
}

// pour inserer un css en prive
function jeux_stylesheet_prive($b) {
 $f = find_in_path("styles/$b.css");
 return $f?'<link rel="stylesheet" href="'.$f.'" type="text/css" media="projection, screen" />'."\n":'';
// return '<link rel="stylesheet" href="'._DIR_PLUGIN_JEUX."styles/$b.css\" type=\"text/css\" media=\"projection, screen\" />\n";
}

// pour inserer un js
function jeux_javascript($b) {
 $f = find_in_path("javascript/$b.js");
 return $f?'<script type="text/javascript" src="'.$f.'"></script>'."\n":'';
}

?>
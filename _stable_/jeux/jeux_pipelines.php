<?php

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// balises du plugin a inserer dans les articles
define('_JEUX_DEBUT', '<jeux>');
define('_JEUX_FIN', '</jeux>');

// separateurs utilisables a l'interieur des balises ci-dessus
define('_JEUX_TITRE', '#TITRE');
define('_JEUX_HORIZONTAL', '#HORIZONTAL');
define('_JEUX_VERTICAL', '#VERTICAL');
define('_JEUX_SOLUTION', '#SOLUTION');
define('_JEUX_SUDOKU', '#SUDOKU');
define('_JEUX_KAKURO', '#KAKURO');
define('_JEUX_QCM', '#QCM');
define('_JEUX_CHARADE', '#CHARADE');
define('_JEUX_DEVINETTE', '#DEVINETTE');
define('_JEUX_REPONSE', '#REPONSE');
define('_JEUX_TROU', '#TROU');
define('_JEUX_TEXTE', '#TEXTE');
define('_JEUX_POESIE', '#POESIE');
define('_JEUX_CITATION', '#CITATION');
define('_JEUX_AUTEUR', '#AUTEUR');
define('_JEUX_RECUEIL', '#RECUEIL');

global $jeux_separateurs;
$jeux_separateurs = array(
	'sudoku' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_SUDOKU, _JEUX_SOLUTION),
	'kakuro' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_KAKURO, _JEUX_SOLUTION),
	'mots_croises' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_HORIZONTAL, _JEUX_VERTICAL, _JEUX_SOLUTION),
	'qcm' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_QCM),
	'textes' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_POESIE, _JEUX_CITATION, _JEUX_AUTEUR, _JEUX_RECUEIL),
	'trous' => array(_JEUX_TITRE, _JEUX_TEXTE, _JEUX_TROU),
);

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

// fonction principale
function jeux($chaine, $indexJeux){ 
	if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return $chaine;
	
	// isoler le jeu...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
	list($chaine, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	
	// ...et decoder le texte obtenu en fonction des signatures
	if (strpos($chaine, _JEUX_POESIE)!==false || strpos($chaine, _JEUX_CITATION)!==false)
		include_jeux('textes', $chaine, $indexJeux);
	if (strpos($chaine, _JEUX_HORIZONTAL)!==false || strpos($chaine, _JEUX_VERTICAL)!==false)
		include_jeux('mots_croises', $chaine, $indexJeux);
	if (strpos($chaine, _JEUX_QCM)!==false) include_jeux('qcm', $chaine, $indexJeux);
	if (strpos($chaine, _JEUX_SUDOKU)!==false) include_jeux('sudoku', $chaine, $indexJeux);
	if (strpos($chaine, _JEUX_TROU)!==false) include_jeux('trous', $chaine, $indexJeux);

	return $texteAvant.jeux_rem('PLUGIN-DEBUT', $indexJeux).$chaine
		.jeux_rem('PLUGIN-FIN', $indexJeux).jeux($texteApres, ++$indexJeux);
}

// a la place de jeux, pour le deboguage...
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

function jeux_pre_propre($texte) { 
	return jeux($texte, 1);
}

function jeux_stylesheet_public($b) {
 return '<link rel="stylesheet" href="'.direction_css(find_in_path('styles/'.$b.'.css'))."\" type=\"text/css\" media=\"projection, screen\" />\n";
}
function jeux_stylesheet_prive($b) {
 return '<link rel="stylesheet" href="'._DIR_PLUGIN_JEUX."styles/$b.css\" type=\"text/css\" media=\"projection, screen\" />\n";
}

function jeux_header_prive($flux){
	return $flux 
		. jeux_stylesheet_prive('qcm')
		. jeux_stylesheet_prive('jeux-prive')
		. '<script type="text/javascript" src="'.find_in_path("jeux.js").'"></script>';
}

function jeux_insert_head($flux){
	return $flux 
		. "<!-- CSS JEUX -->\n"
		. jeux_stylesheet_public('qcm')
		. jeux_stylesheet_public('jeux')
		. '<script type="text/javascript" src="'.find_in_path("jeux.js").'"></script>';
}

?>
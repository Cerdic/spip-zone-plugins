<?php

#------ filtres pas_de_plugin ----------------------#
#  Filtres : jeux                                   #
#  Auteurs : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

/*
  Titre du jeu : 
  	- soit après la balise #TITRE du jeu
	- soit entre les balises <intro> et </intro>
		(Spip s'en servira egalement en cas d'absence de descriptif pour 
		calculer la balise #INTRODUCTION utilisee pour resumer l'article)

  Calcul de #INTRODUCTION (vos sommaires, rubriques ou backends) : 
  si la fonction introduction() n'est pas surchargee, Spip cherche 
  d'abord le descriptif, puis en cas d'echec, le contenu du texte situé entre 
  les balises <intro> et </intro>. En dernier lieu, Spip utilise les 500 premiers 
  caractères du chapeau suivi du texte.
  Attention donc : pour ne pas faire apparaitre le contenu des jeux avec 
  les reponses, il vaut mieux penser à bien définir :
  	- soit le descriptif de l'article 
	- soit une introduction placee entre les balises <intro> et </intro>
		(utiliser dans ce cas la balise #TITRE du jeu
		pour definir le titre de la grille)
	- soit le titre du jeu place entre les balises <intro> et </intro>

*/

define(_JEUX_DEBUT, '<jeux>');
define(_JEUX_FIN, '</jeux>');
define(_JEUX_TITRE, '#TITRE');
define(_JEUX_HORIZONTAL, '#HORIZONTAL');
define(_JEUX_VERTICAL, '#VERTICAL');
define(_JEUX_SOLUTION, '#SOLUTION');
define(_JEUX_SUDOKU, '#SUDOKU');
define(_JEUX_QCM, '#QCM');
define(_JEUX_HTML, '#HTML'); // à faire !

// transforme les listes verticales/horizontale listes html 
function jeux_listes($texte) {
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$v) if (($v=trim($v))!='') $tableau[$i] = "<li>$v</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

// cette fonction retourne le texte entre deux balises si elles sont presentes
// et false dans le cas contraire
function jeux_recupere_le_titre(&$chaine, $ouvrant, $fermant) {
  // si les balises ouvrantes et fermantes ne sont pas presentes, c'est mort
  if (strpos($chaine, $ouvrant)===false || strpos($chaine, $fermant)===false) return false;
  list($texteAvant, $suite) = explode($ouvrant, $chaine, 2); 
  list($texte, $texteApres) = explode($fermant, $suite, 2); 
  // on supprime les balises de l'affichage...
  // $chaine = $texteAvant.$texteApres;
  return trim($texte);
}

//fonction principale
function jeux($chaine){ 
	if (strpos($chaine, _JEUX_DEBUT)===false || strpos($chaine, _JEUX_FIN)===false) return $chaine;

	// isoler les jeux...
	list($texteAvant, $suite) = explode(_JEUX_DEBUT, $chaine, 2); 
	list($texte, $texteApres) = explode(_JEUX_FIN, $suite, 2); 
	
	// ...et decoder le texte obtenu !
	if (strpos($texte, _JEUX_HORIZONTAL)!=false || strpos($texte, _JEUX_FIN)!=false) {
		include_spip('inc/mots_croises');
		$texte = jeux_mots_croises($texte);
	}
	if (strpos($chaine, _JEUX_QCM)!=false) {
		include_spip('inc/qcm');
		$texte = jeux_qcm($chaine);
	}
	if (strpos($chaine, _JEUX_SUDOKU)!=false) {
		include_spip('inc/sudoku');
		$texte = jeux_sudoku($chaine);
	}

	return $texteAvant.code_echappement('<!-- PLUGIN-DEBUT -->').$texte
		.code_echappement('<!-- PLUGIN-FIN -->').$texteApres;
}

// a la place de jeux, pour le deboguage...
function jeux2($chaine){
 if (strpos($chaine, _JEUX_DEBUT)!==false || strpos($chaine, '<horizontal>')!==false) {
	ob_start();
	$chaine = jeux($chaine);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
 }
 return $chaine;
}

function jeux_pre_propre($texte) { 
	return jeux($texte);
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

function jeux_post_propre($texte) { 
	// a supprimer dans le futur...
	return preg_replace(',<!(QCM-(DEBUT|FIN)(-#[0-9]+)?)>,UimsS', '<!-- \\1 -->', $texte);
}	


?>
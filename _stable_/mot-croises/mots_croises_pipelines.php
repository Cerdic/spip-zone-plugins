<?php
/*
  Titre de la grille des mots croisés : 
  	- soit après la balise #TITRE des mots croisés
	- soit entre les balises <intro> et </intro>
		(Spip s'en servira egalement en cas d'absence de descriptif pour 
		calculer la balise #INTRODUCTION utilisee pour resumer l'article)

  Calcul de #INTRODUCTION (vos sommaires, rubriques ou backends) : 
  si la fonction introduction() n'est pas surchargee, Spip cherche 
  d'abord le descriptif, puis en cas d'echec, le contenu du texte situé entre 
  les balises <intro> et </intro>. En dernier lieu, Spip utilise les 500 premiers 
  caractères du chapeau suivi du texte.
  Attention donc : pour ne pas faire apparaitre le contenu des mots croises avec 
  les reponses, il vaut mieux penser à bien définir :
  	- soit le descriptif de l'article 
	- soit une introduction placee entre les balises <intro> et </intro>
		(utiliser dans ce cas la balise #TITRE des mots croisés
		pour definir le titre de la grille)
	- soit le titre des mots croises place entre les balises <intro> et </intro>

*/

define(_MC_DEBUT, '<mots-croises>');
define(_MC_FIN, '</mots-croises>');

//transforme les listes verticales/horizontale listes html 
function mots_croises_listes($texte) {
	$tableau = explode("\r", trim($texte));	
	foreach ($tableau as $i=>$v) if (($v=trim($v))!='') $tableau[$i] = "<li>$v</li>\n";
	$texte = implode('', $tableau);
	return "<ol>$texte</ol>"; 
}

// cette fonction retourne le texte entre deux balises si elles sont presentes
// et false dans le cas contraire
function mots_croises_recupere_le_titre(&$chaine, $ouvrant, $fermant) {
  // si les balises ouvrantes et fermantes ne sont pas presentes, c'est mort
  if (strpos($chaine, $ouvrant)===false || strpos($chaine, $fermant)===false) return false;
  list($texteAvant, $suite) = explode($ouvrant, $chaine, 2); 
  list($texte, $texteApres) = explode($fermant, $suite, 2); 
  // on supprime les balises de l'affichage...
  // $chaine = $texteAvant.$texteApres;
  return trim($texte);
}

//fonction principale
function mots_croises($texte){ 
	if (strpos($texte, '<horizontal>')!==false || strpos($texte, '<vertical>')!==false) {
		include_spip('inc/compatibilite_syntaxe');
		return mots_croises_vieille_syntaxe($texte);
	}	
	if (strpos($texte, _MC_DEBUT)===false || strpos($texte, _MC_FIN)===false) return $texte;

	// isoler les mots-croises...
	list($texteAvant, $suite) = explode(_MC_DEBUT, $texte, 2); 
	list($texte, $texteApres) = explode(_MC_FIN, $suite, 2); 
	
	// ...et decoder le texte obtenu !
	include_spip('inc/gestion_grille');
	$tableau = preg_split("/(#TITRE|#HORIZONTAL|#VERTICAL|#SOLUTION)/", trim($texte), -1, PREG_SPLIT_DELIM_CAPTURE);
	$titre = $horizontal = $vertical = $solution = false;
	foreach($tableau as $i => $v){
	 if ($v=='#TITRE') $titre = trim($tableau[$i+1]);
	  elseif ($v=='#HORIZONTAL') $horizontal = mots_croises_listes($tableau[$i+1]);
	  elseif ($v=='#VERTICAL') $vertical = mots_croises_listes($tableau[$i+1]);
	  elseif ($v=='#SOLUTION') $solution = calcul_tableau_grille($tableau[$i+1]);
	}

	// trouver un titre, coute que coute...
	if (!$titre) $titre = mots_croises_recupere_le_titre($chaine, '<intro>', '</intro>');
	if (!$titre) $titre = _T('motscroises:titre');
	
	$texte = calcul_erreurs_grille($solution)
			. affichage_grille($solution)
	// definitions	
			. '<div class="spip horizontal"><h4 class="spip grille">'
					._T('motscroises:horizontalement')."</h4>\n".$horizontal.'</div>'
			. '<div class="spip vertical"><h4 class="spip grille">'
					._T('motscroises:verticalement')."</h4>\n".$vertical.'</div>'
	// solution
			. (($GLOBALS["solution"][0] == 1)? affichage_grille($solution, true) : '');

	return code_echappement($texteAvant.'<!-- PLUGIN-DEBUT -->').$texte.code_echappement('<!-- PLUGIN-FIN -->').$texteApres;
}

// a la place de mots_croises, pour le deboguage...
function mots_croises2($chaine){
 if (strpos($chaine, _MC_DEBUT)!==false || strpos($chaine, '<horizontal>')!==false) {
	ob_start();
	$chaine = mots_croises($chaine);
	$data = ob_get_contents();
	ob_end_clean();
	$chaine = nl2br(str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$data)).$chaine;
 }
 return $chaine;
}

function mots_croises_pre_propre($texte) { 
	return mots_croises($texte);
}

function mots_croises_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('mots-croises-prive.css')).'" />';
	$flux .= '<script type="text/javascript" src="'.find_in_path("mots-croises.js").'"></script>';
	return $flux;
}

function mots_croises_insert_head($flux){
	return $flux . '<link rel="stylesheet" type="text/css" href="'
	 .direction_css(find_in_path("mots-croises.css"))."\" />\n<script src=\"".find_in_path("mots-croises.js").'" type="text/javascript"></script>';
}
?>
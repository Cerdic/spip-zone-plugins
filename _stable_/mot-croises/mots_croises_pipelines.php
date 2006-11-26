<?php
/*
  Titre de la grille des mots croisés : 
  	- soit après la balise #TITRE des mots croisés
	- soit entre les balises <intro> et </intro>
		(Spip s'en servira egalement en cas d'absence de descriptif pour 
		calculer la balise #INTRODUCTION utilisee pour resumer l'article)

  Calcul de #INTRODUCTION : si introduction() n'est pas surchargee, Spip cherche 
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

//transforme les listes verticales/horizontale spip en html
//on garde pour compatibilite
function mots_croises_listes_vieille_syntaxe($texte) {
	$texte = preg_replace('/ *-#/','<li>',$texte);
	$texte = implode("</li>\n", preg_split("/\n*\r*\n+\r*\n*/",$texte));
	return "<ol>$texte</li></ol>"; 
}

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
//on garde pour compatibilite
function mots_croises_vieille_syntaxe($texte) {	
	if (! preg_match("/<grille>|<\/grille>/",$texte)) return $texte;
	include_spip('inc/gestion_grille');
	$tableau = preg_split("/<grille>|<\/grille>/",$texte);	//sera uniquement le tableau spip, mais on attend pour le moment
	$j =0;
	
	foreach ($tableau as $i){
			if ($j!=0 and $j!=count($tableau)-1){	//pas les extremités du tableau
				$tableau_php = calcul_tableau_grille_vieille_syntaxe($tableau[$j]);
			
				//calcul erreur
				if ($GLOBALS["bouton_envoi"] == '') $erreur='';
				else {
					list($nbr_erreurs, $nbr_vides) = comparaison_grille($tableau_php); 
					$erreur = '<strong class="erreur">';
					$erreur .= ($nbr_erreurs==0)?_T('motscroises:aucune_erreur'):(
					 ($nbr_erreurs==1)?_T('motscroises:une_erreur'):_T("motscroises:nombre_erreurs", Array('err'=>$nbr_erreurs))
					);
					$erreur .= ($nbr_vides==0)?(($nbr_erreurs==0)?'. '._T('motscroises:bravo'):''):(
					 ($nbr_vides==1)?' - '._T('motscroises:une_vide'):' - '._T("motscroises:nombre_vides", Array('vid'=>$nbr_vides))
					);
					$erreur.='</strong><br />';
				}
				//fin calcul erreur
				
				$tableau[$j] = code_echappement(_GRILLE_.$erreur.affichage_grille($tableau_php)._GRILLE_);	
				}
			
			$j++;
			}
	
	$texte = implode($tableau);
	
	// definitions horizontales
	$tableau = preg_split("/<horizontal>|<\/horizontal>/",$texte);
	$j = 0;
	foreach ($tableau as $i){
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				$tableau[$j] = code_echappement(_GRILLE_.'<div class="spip horizontal"><h4 class="spip grille">'
					._T('motscroises:horizontalement')."</h4>\n".mots_croises_listes_vieille_syntaxe(trim($i))."</div>"._GRILLE_);
		$j++;
	}
	$texte = implode($tableau);
	
	// definitions verticales
	$tableau = preg_split("/<vertical>|<\/vertical>/",$texte);
	$j = 0;
	foreach ($tableau as $i){
		if ($j!=0 and $j!=count($tableau)-1)	//pas les extremités du tableau
				$tableau[$j] = code_echappement(_GRILLE_.'<div class="spip vertical"><h4 class="spip grille">'
					._T('motscroises:verticalement')."</h4>\n"
					.mots_croises_listes_vieille_syntaxe(trim($i))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>"._GRILLE_); // Bug IE ?
		$j++;
	}
	$texte = implode($tableau);
	
	// solution
	if ($GLOBALS["solution"][0] == 1) $texte .= code_echappement(_GRILLE_.affichage_grille($tableau_php, true)._GRILLE_);

	return $texte;
}
	
//fonction principale
function mots_croises($texte){ 
	if (strpos($texte, '<horizontal>')!==false && strpos($texte, '</horizontal>')!==false) return mots_croises_vieille_syntaxe($texte);
	if (strpos($texte, _MC_DEBUT)===false || strpos($texte, _MC_FIN)===false) return $texte;

	list($texteAvant, $suite) = explode(_MC_DEBUT, $texte, 2); 
	list($texte, $texteApres) = explode(_MC_FIN, $suite, 2); 
	
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
	return mots_croises2($texte);
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
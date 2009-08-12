<?php
function favoris_datefr($date) { 
		$split = split('-',$date); 
		$annee = $split[0]; 
		$mois = $split[1]; 
		$jour = $split[2]; 
		return $jour.'/'.$mois.'/'.$annee; 
	} 
function favoris_date_du_jour($heure=false) {
		return '<p>'.($heure ? _T('favoris:date_du_jour_heure') : _T('favoris:date_du_jour')).'</p>';
	}
/**************************************************************************************/
/* Balise "#TMP" 1.0 */
/* ---------------------------------------------------------------------------------------------------- */
/* Infos : GPL - 02/08 (c) FredoMkb */
/* Utilisation : #TMP{cle,valeur,defaut} (voir details dans le code) */
/* Role : Memorise des valeurs pour pouvoir les utiliser ensuite */
/* ---------------------------------------------------------------------------------------------------- */
function balise_TMP($p) {
// Fonction de la balise "#TMP", c'est le code appele par Spip.

	// Initialisation des variables qui vont recevoir les arguments 
	$cle = "";
	$val = "";
	$dft = "";

	// Recuperation des paramettres fournis, 3 arguments reconnus 
	// On test d'abord l'existence de chaque parametre, s'il existe, 
	// alors on affecte l'argument a la variable correspondante. 
	if (isset($p->param[0][1])) { $cle .= ($p->param[0][1][0]->texte); }
	if (isset($p->param[0][2])) { $val .= ($p->param[0][2][0]->texte); }
	if (isset($p->param[0][3])) { $dft .= ($p->param[0][3][0]->texte); }

	// Comme on ne peut pas passer des "array", il faut generer du "string" exploitable
	$listargs = var_export(array($cle,$val,$dft), true);

	// Affectation du nom de la fonction a utiliser dans la balise, avec les arguments
	$p->code = "get_tmp($listargs)";

	// Le statut 'php' est sur, le statut 'html' passe le retour par le filtre 'interdire_scripts'.
	// On peut aussi faire : '$p->interdire_scripts = false;' ou 'true'
	$p->statut = 'html';

	// Retour du resultat
	return $p;
}
function get_tmp($listargs) {
// Fonction pour faire le "pont" entre la balise "#TMP" et le filtre "|tmp".
	$cle = $listargs[0];
	$val = $listargs[1];
	$dft = $listargs[2];

	// On change un peu l'ordre des arguments afin que le filtre
	// puisse trouver la variable $val en premiere place
	return tmp($val, $cle, $dft);
}
function tmp($val='', $cle='', $dft='') {
// Fonction du filtre "|tmp", gere les donnees, pour memoriser ou retourner une valeur.

	if (empty($cle) && empty($val) && empty($dft)) {
		// Si les trois arguments sont vides, alors on supprime la GLOBALE "tmp"
		// Utilisation : [(#TMP)]
		unset($GLOBALS['tmp']);

	} elseif (empty($cle) && !empty($val)) {
		// Si la cle est vide mais pas la valeur, alors un utilise cette derniere comme cle 
		// pour supprimer l'entree ayant comme cle la valeur fournie
		// Utilisation : [(#TMP{'',cle})]
		unset($GLOBALS['tmp'][$val]);

	} elseif (!empty($cle) && !empty($val)) {
		// Si la cle et la valeur sont fournies, alors on memorise ces infos 
		// Utilisation : [(#TMP{cle,valeur})] ou [(#VALEUR|tmp{cle})]
		$GLOBALS['tmp'][$cle] = $val;

	} elseif (!empty($cle) && isset($GLOBALS['tmp'][$cle])) { 
		// Si la cle seule est fournie, alors on tente de retourner les donnees correspondantes 
		// Utilisation : [(#TMP{cle})]
		return $GLOBALS['tmp'][$cle];

	} else {
		// Si rien de ce qui precede ne fonctionne, alors on retourne la valeur par defaut fournie
		// Utilisation : [(#TMP{cle,'',defaut})]
		return $dft;
	}
}	
?>
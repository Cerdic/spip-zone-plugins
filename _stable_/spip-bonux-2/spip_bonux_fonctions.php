<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

define('_BONUX_CSS_MD5_FORMULAIRES','a2d73f0c930b01341d8a3d3f38b5ec54');
/**
 * une fonction pour generer des menus avec liens
 * ou un span lorsque l'item est selectionne
 *
 * @param string $url
 * @param string $libelle
 * @param bool $on
 * @param string $class
 * @param string $title
 * @return string
 */
function aoustrong($url,$libelle,$on=false,$class="",$title="",$rel=""){
	return 
	($on ?"<strong class='on'>":
		"<a href='$url'"
	  	.($title?" title='".attribut_html($title)."'":'')
	  	.($class?" class='".attribut_html($class)."'":'')
	  	.($rel?" rel='".attribut_html($rel)."'":'')
	  	.">"
	)
	. $libelle
	. ($on ? "</strong>":"</a>");
}


/**
 * une fonction pour generer une balise img a partir d'un nom de fichier
 *
 * @param string $img
 * @param string $alt
 * @param string $class
 * @return string
 */
function tag_img($img,$alt="",$class=""){
	$taille = taille_image($img);
	list($hauteur,$largeur) = $taille;
	if (!$hauteur OR !$largeur)
		return "";
	return 
	"<img src='$img' width='$largeur' height='$hauteur'"
	  ." alt='".attribut_html($alt)."'"
	  .($class?" class='".attribut_html($class)."'":'')
	  .' />';
}

/**
 * Afficher un message "un truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	if (!$nb=intval($nb)) return "";
	if ($nb>1) return _T($chaine_plusieurs, array($var => $nb));
	else return _T($chaine_un);
}

/**
 * Ajouter un timestamp a une url de fichier
 *
 * @param unknown_type $fichier
 * @return unknown
 */
function timestamp($fichier){
	$m = filemtime($fichier);
	return "$fichier?$m";
}



/**
 * filtre explode pour les squelettes permettant d'ecrire
 * #GET{truc}|explode{-}
 *
 * @param strong $a
 * @param string $b
 * @return array
 */
function filtre_explode($a,$b){return explode($b,$a);}

/**
 * filtre implode pour les squelettes permettant d'ecrire
 * #GET{truc}|implode{-}
 *
 * @param array $a
 * @param string $b
 * @return string
 */
function filtre_implode($a,$b){return implode($b,$a);}


/**
 * un filtre icone mappe sur icone_inline, qui cree une icone a gauche par defaut
 *
 * @param unknown_type $lien
 * @param unknown_type $texte
 * @param unknown_type $fond
 * @param unknown_type $align
 * @param unknown_type $fonction
 * @return unknown
 */
function filtre_icone($lien, $texte, $fond, $align="", $fonction=""){
	return icone_inline($texte, $lien, $fond, $fonction, $align?$align:$GLOBALS['spip_lang_left']);
}

function picker_selected($selected,$type){
	$select = array();
	$type = preg_replace(',\W,','',$type);
	foreach($selected as $value)
		if (preg_match(",".$type."[|]([0-9]+),",$value,$match)
		  AND $v=intval($match[1]))
		  $select[] = $v;
	return $select;
}
?>
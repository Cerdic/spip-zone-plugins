<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

include_spip('inc/core21_filtres');

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
	return lien_ou_expose($url,$libelle,$on,$class,$title,$rel);
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
	return balise_img($img,$alt,$class);
}

/**
 * Afficher un message "un truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	return singulier_ou_pluriel($nb,$chaine_un,$chaine_plusieurs,$var);
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


function picker_selected($selected,$type){
	$select = array();
	$type = preg_replace(',\W,','',$type);
	if (is_array($selected))
		foreach($selected as $value)
			if (preg_match(",".$type."[|]([0-9]+),",$value,$match)
			  AND $v=intval($match[1]))
			  $select[] = $v;
	return $select;
}
?>
<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */

/**
 * Afficher un message "une truc"/"N trucs"
 *
 * @param int $nb
 * @return string
 */
function gestdoc_affiche_un_ou_plusieurs($nb,$chaine_un,$chaine_plusieurs,$var='nb'){
	if (!$nb=intval($nb)) return "";
	if ($nb>1) return _T($chaine_plusieurs, array($var => $nb));
	else return _T($chaine_un);
}

/**
 * Enter description here...
 *
 * @param unknown_type $id_document
 * @param unknown_type $statut
 * @param unknown_type $id_rubrique
 * @param unknown_type $type
 * @param unknown_type $ajax
 * @return unknown
 */
function gestdoc_puce_statut_document($id_document, $statut){
	if ($statut=='publie') {
		$puce='puce-verte.gif';
	}
	else if ($statut == "prepa") {
		$puce = 'puce-blanche.gif';
	}
	else if ($statut == "poubelle") {
		$puce = 'puce-poubelle.gif';
	}
	else 
		$puce = 'puce-blanche.gif';

	return http_img_pack($puce, $statut, "class='puce'");
}



//
// <BOUCLE(DOCUMENTS)>
//
// http://doc.spip.org/@boucle_DOCUMENTS_dist
function boucle_DOCUMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	// on ne veut pas des fichiers de taille nulle,
	// sauf s'ils sont distants (taille inconnue)
	array_unshift($boucle->where,array("'($id_table.taille > 0 OR $id_table.distant=\\'oui\\')'"));

	// Supprimer les vignettes
	if (!$boucle->modificateur['criteres']['mode']
	AND !$boucle->modificateur['criteres']['tout']) {
		array_unshift($boucle->where,array("'!='", "'$id_table.mode'", "'\\'vignette\\''"));
	}

	// Pour une boucle generique (DOCUMENTS) sans critere de lien, verifier
	// qu notre document est lie a un element publie
	// (le critere {tout} permet de les afficher tous quand meme)
	// S'il y a un critere de lien {id_article} par exemple, on zappe
	// ces complications (et tant pis si la boucle n'a pas prevu de
	// verification du statut de l'article)
	if (!$boucle->modificateur['tout']
	AND !$boucle->modificateur['criteres']['statut']
	) {
		if ($GLOBALS['var_preview']) {
			array_unshift($boucle->where,"'($id_table.statut IN (\"publie\",\"prop\",\"prepa\")");
		} else {
			array_unshift($boucle->where,"'(($id_table.statut = \"publie\"))'");
		}
	}

	return calculer_boucle($id_boucle, $boucles);
}

function aouspan($url,$libelle,$on=false,$class="",$title=""){
	return 
	($on ?"<span class='on'>":
		"<a href='$url'"
	  	.($title?" title='".attribut_html($title)."'":'')
	  	.($class?" class='".attribut_html($class)."'":'')
	  	.">"
	)
	. $libelle
	. ($on ? "</span>":"</a>");
}
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

?>
<?php




if (!defined('_ECRIRE_INC_VERSION')) return;

// Une balise qui prend en argument un squelette suppose contenir un FORM
// et gere ses saises automatiquement dans une table SQL a 2 colonnes
// nom / valeur

// Comme l'emplacement du squelette est calcule (par l'argument de la balise)
// on ne peut rien dire sur l'existence du squelette lors de la compil
// On pourrait toutefois traiter le cas de l'argument qui est une constante.

function _fonds_image($type, $nom, $id) {
	$fichier = sous_repertoire(sous_repertoire(_DIR_IMG, "fonds"), "$type".$id)."$nom$id.jpg";
	if (file_exists($fichier)) {
		include_spip("inc/filtres_images_lib_mini");
		$image = _image_valeurs_trans($fichier, "");
		return $image["tag"];
	}

	// SVG
	$fichier = sous_repertoire(sous_repertoire(_DIR_IMG, "fonds"), "$type".$id)."$nom$id.svg";
	if (file_exists($fichier)) {
		return $fichier;
	}


}


function balise_IMG_HAUT_dist($p) {

	$id_objet = id_table_objet("article");
	if (!isset($_id_objet) OR !$_id_objet)
		$_id_objet = champ_sql($id_objet, $p);

	$p->code = "_fonds_image('article', 'img_haut', $_id_objet)";
	return $p;
}

function balise_FOND_HAUT_dist($p) {

	$id_objet = id_table_objet("article");
	if (!isset($_id_objet) OR !$_id_objet)
		$_id_objet = champ_sql($id_objet, $p);

	$p->code = "_fonds_image('article', 'fond_haut', $_id_objet)";
	return $p;
}

function balise_IMG_FOND_dist($p) {

	$id_objet = id_table_objet("article");
	if (!isset($_id_objet) OR !$_id_objet)
		$_id_objet = champ_sql($id_objet, $p);

	$p->code = "_fonds_image('article', 'img_fond', $_id_objet)";
	return $p;
}


function balise_IMG_BAS_dist($p) {

	$id_objet = id_table_objet("article");
	if (!isset($_id_objet) OR !$_id_objet)
		$_id_objet = champ_sql($id_objet, $p);

	$p->code = "_fonds_image('article', 'img_bas', $_id_objet)";
	return $p;
}

function balise_FOND_BAS_dist($p) {

	$id_objet = id_table_objet("article");
	if (!isset($_id_objet) OR !$_id_objet)
		$_id_objet = champ_sql($id_objet, $p);

	$p->code = "_fonds_image('article', 'fond_bas', $_id_objet)";
	return $p;
}

if (!function_exists("fonds_largeur_svg")){
	function _fonds_taille_svg($file) {
		global $metas_svg;
		
		if (!$metas_svg[$file]) {
			include_spip("metadata/svg");
			$metas_svg[$file] = metadata_svg_dist($file);
		}
		return $metas_svg[$file];
	}

	function fonds_largeur_svg ($file) {
		$mt = _fonds_taille_svg($file);
		return $mt["largeur"];
	}
	function fonds_hauteur_svg ($file) {
		$mt = _fonds_taille_svg($file);
		return $mt["hauteur"];
	}
}


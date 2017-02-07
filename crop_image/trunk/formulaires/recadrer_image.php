<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_recadrer_image_charger_dist($objet="document", $id_objet, $redirect=''){
	$valeurs = array();
	if ($objet == "document") {
		$valeurs['id_document'] = intval($id_objet);
	}
	if ($objet == "article") {
		$valeurs['id_article'] = intval($id_objet);
	}
	if ($objet == "rubrique") {
		$valeurs['id_rubrique'] = intval($id_objet);
	}

	return $valeurs;
}

function formulaires_recadrer_image_verifier_dist($objet="document", $id_objet, $redirect=''){
	$erreurs = array();
	// on ne recadre pas une image non modifiee
	if ( intval(_request('w')) === 0 ) {
		$erreurs['message_erreur'] = _T('jcrop:selectionner_region_obligatoire');
	}
	return $erreurs;
}

function formulaires_recadrer_image_traiter_dist($objet="document", $id_objet, $redirect=''){

	$retour = array();
	$fichier = _request('fichier');
	if ($objet == "document") {
		$fichier = find_in_path($fichier);
	} else {
		$fichier = _DIR_IMG.'/'.$fichier;
	}

	$modif = decoupe_img(
		$fichier,
		$fichier,
		_request('x'),
		_request('y'),
		_request('w'),
		_request('h')
	);
	// On modifie les valeurs taille/largeur/hauteur en bdd
	if ($modif and $objet == "document") {
		$set = array(
			'taille'  => filesize(find_in_path(_request('fichier'))),
			'largeur' => _request('w'),
			'hauteur' => _request('h')
		);
		include_spip('action/editer_objet');
		$objet = "document";
		objet_modifier($objet, intval($id_objet), $set);
	}

	if ($redirect) {
		$retour['redirect'] = $redirect;
	}
	return $retour;
}

/**
 * Recadrer/Découper une image jpg ou png
 *
 * @param $img_ini = CHEMIN+NOM_FICHIER image initiale,
 * @param $img_fin = CHEMIN+NOM_FICHIER image finale,
 * @param $x et $y = coordonnées x et y de la découpe de l'image
 * @param $l et $h = largeur et hauteur max de l'image finale
 * @param $detruire_ini = effacer l'original (true) ou le garder (false)
 * @return return la taille de la nouvelle image
 * @author cy_altern
 **/
function decoupe_img($img_ini, $img_fin, $x = 0, $y = 0, $w = 400, $h = 300, $detruire_ini=false) {
	if (!file_exists($img_ini)) return 'Le fichier '.$img_ini.' n\'existe pas';
	// déterminer le type de fonction de création d'image à utiliser
	$param_img = getimagesize($img_ini);
	$type_img = $param_img[2];
	switch ($type_img) {
	case 2 :
		$fct_creation_ext = 'imagecreatefromjpeg';
		$fct_ecrire = 'imagejpeg';
		$ext_img = '.jpg';
		break;
	case 3 :
		$fct_creation_ext = 'imagecreatefrompng';
		$fct_ecrire = 'imagepng';
		$ext_img = '.png';
		break;
	default :
		return false;
		break;
	}
	$img_nv = imagecreatetruecolor($w, $h);
	$img_acopier = $fct_creation_ext($img_ini);
	// gérer la transparence pour les images PNG (le mec qui a trouvé ce code est génial! :-)
	if ($type_img == 3) {
		imagecolortransparent($img_nv, imagecolorallocate($img_nv, 0, 0, 0));
		imagealphablending($img_nv, false);
		imagesavealpha($img_nv, true);
	}
	$res = imagecopyresampled($img_nv, $img_acopier, 0, 0, $x, $y, $w, $h, $w, $h);
	// sauvegarder l'image et éventuellement détruire le fichier image initial
	$fct_ecrire($img_nv, $img_fin);
	imagedestroy($img_nv);
	imagedestroy($img_acopier);
	if ($detruire_ini == true) unlink($img_ini);
	if ($res) {
		return true;
	} else {
		return false;
	}
}

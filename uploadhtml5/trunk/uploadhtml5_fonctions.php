<?php
/**
 * Fonctions utiles au plugin Formulaire upload html5
 *
 * @plugin	   Formulaire upload html5
 * @copyright  2014
 * @author	   Phenix
 * @licence	   GNU/GPL
 * @package	   SPIP\Uploadhtml5\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Uploader et lier des documents à un objet SPIP
 *
 * @param mixed $files $_FILES envoyer par un formulaire had hoc
 * @param mixed $objet
 * @param mixed $id_objet
 * @param string $id_document Dans le cas ou l'on veux remplacer un document.
 * @access public
 */
function uploadhtml5_uploader_document($objet, $id_objet, $files, $id_document = 'new', $mode = 'auto') {

	// tester l'autorisation d'ajout de document
	include_spip('inc/autoriser');
	/* S'il n'y a pas d'id_objet, c'est qu'on crée un nouveau
	   document. Les autorisations seront gérées en aval dans
	   ajouter_document. */
	if ($id_objet and (!autoriser('joindredocument', $objet, $id_objet))) {
		return false;
	}

	// On va créer le tableau des documents.
	$docs = array();
	foreach ($files as $doc) {
		// pas de fichier vide
		if (!empty($doc['name'])) {
			$docs[] = $doc;
		}
	}

	// On fait un test au cas ou
	if (!empty($docs)) {
		// On ajoute les documents a un objet SPIP.
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$res = $ajouter_documents(
			$id_document,
			$docs,
			$objet, // Article, rubrique, autre objet
			$id_objet,
			$mode
		);

		// Obfusquer les noms de fichiers
		if (_UPLOADHTML5_OBFUSQUER == true) {
			foreach ($res as $key => $id_document) {
				obfusquer_document($id_document);
			}
		}
		return $res;
	}
}

/**
 * Uploader un logo sur un objet en spip 3.0/3.1
 *
 * @param mixed $objet
 * @param mixed $id_objet
 * @param mixed $fichier
 * @access public
 * @return mixed
 */
function uploadhtml5_uploader_logo($objet, $id_objet, $fichier) {

	// Autorisation de mettre un logo?
	include_spip('inc/autoriser');
	if (!autoriser('iconifier', $objet, $id_objet)) {
		return false;
	}

	// On commence par invalider le cache de l'objet
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");

	include_spip('action/editer_logo');
	// Version SPIP 3.1 de cette fonction:
	if (function_exists('logo_modifier')) {
		return logo_modifier($objet, $id_objet, 'on', $fichier);
	}

	include_spip('action/iconifier');
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$ajouter_image = charger_fonction('spip_image_ajouter', 'action');

	$type = type_du_logo(id_table_objet($objet));
	$logo = $chercher_logo($id_objet, id_table_objet($objet));

	if ($logo) {
		spip_unlink($logo[0]);
	}

	// Dans le cas d'un tableau, on présume que c'est un $_FILES et on passe directement
	if (is_array($fichier)) {
		$err = $ajouter_image($type.'on'.$id_objet, ' ', $fichier, true);
	} else {
		// Sinon, on caviarde la fonction ajouter_image
		$err = $ajouter_image($type.'on'.$id_objet, ' ', array('tmp_name' => $fichier), true);
	}

	return ($err) ? $err : true;
}

/**
 * Convertir les formats de logo accepté en mime_type
 *
 * @param mixed $type Liste des formats à convertir en mime type, séparé par une virgule.
 * @access public
 * @global mixed $formats_logos
 * @return mixed Liste des mimes types séparé par une virgule.
 */
function trouver_mime_type($type) {

	// Si le type est logo on récupère automatiquement les formats de
	// logo défini par SPIP
	if ($type == 'logo') {
		global $formats_logos;
		$type = $formats_logos;
	} else {
		// on explode pour passer $type dans sql_in
		$type = explode(',', $type);
	}

	// On récupère les mimes types demandé par la fonction
	$mime_type = sql_allfetsel('mime_type', 'spip_types_documents', sql_in('extension', $type));

	// Simplifier le tableau
	$mime_type = array_column($mime_type, 'mime_type');

	// Renvoyer une chaine utilisable
	return implode(',', $mime_type);
}


/**
 * Fonction qui va créer le titre du cadre d'un logo
 * C'est reprit de prive/formulaires/editer_logo.phpL55
 * Cela devrait être dans une fonction du core de SPIP non ?
 */
function titre_cadre_logo($objet, $id_objet) {
	$balise_img = chercher_filtre('balise_img');
	$img = $balise_img(chemin_image('image-24.png'), '', 'cadre-icone');
	$libelles = pipeline('libeller_logo', $GLOBALS['logo_libelles']);
	$libelle = (($id_objet or $objet != 'rubrique') ? $objet : 'racine');
	if (isset($libelles[$libelle])) {
		$libelle = $libelles[$libelle];
	} elseif ($libelle = objet_info($objet, 'texte_logo_objet')) {
		$libelle = _T($libelle);
	} else {
		$libelle = _L('Logo');
	}
	$aider = function_exists('aider') ? 'aider' : 'aide';
	switch ($objet) {
		case 'article':
			$libelle .= ' ' . $aider('logoart');
			break;
		case 'breve':
			$libelle .= ' ' . $aider('breveslogo');
			break;
		case 'rubrique':
			$libelle .= ' ' . $aider('rublogo');
			break;
		default:
			break;
	}

	return $img . $libelle;
}

/**
 * Permet d'obfusquer le nom d'un document SPIP
 *
 * @param int $id_document
 * @access public
 * @return string Chemin du nouveau fichier
 */
function obfusquer_document($id_document) {

	// On commence par le fichier
	$fichier = sql_getfetsel('fichier', 'spip_documents', 'id_document='.intval($id_document));

	$fichier = _DIR_IMG.$fichier;

	// Récupérer les informations du fichier
	$fichier_info = pathinfo($fichier);

	// obfusquer
	$nouveau_nom = uniqid();

	// Construire le nouveau fichier
	$nouveau_fichier = $fichier_info['dirname'].'/'.$nouveau_nom.'.'.$fichier_info['extension'];

	renommer_document($fichier, $nouveau_fichier, $id_document);

	return $nouveau_fichier;
}

/**
 * Permet de renommer un fichier
 * De manière optionnel, si on passe un id_document,
 * le champs fichier sera mis à jour avec le nouveau chemin
 *
 * @param string $ancien_chemin
 * @param string $nouveau_chemin
 * @param int $id_document
 * @access public
 */
function renommer_document($ancien_chemin, $nouveau_chemin, $id_document = null) {

	rename($ancien_chemin, $nouveau_chemin);

	// Mettre à jour la base de donnée avec le nouveau chemin au besoin
	if (!is_null($id_document)) {
		// Dans la base de donnée il ne faut pas avoir le dossier IMG
		$nouveau_chemin = str_replace(_DIR_IMG, '', $nouveau_chemin);
		sql_updateq('spip_documents', array('fichier' => $nouveau_chemin), 'id_document='.intval($id_document));
	}
}

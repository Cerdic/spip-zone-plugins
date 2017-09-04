<?php
/**
 * Fonctions utiles au plugin Emplois
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * afficher le placeholder dans le formulaire de l'espace publique
 *
 * @param null|string $arg
 *     Clé des arguments. En absence utilise l'argument
 *     de l'action sécurisée.
 * @return bool
 */
function emplois_afficher_public($attribut) {
	include_spip('inc/config');
	$val = lire_config('emplois/affichage_public/placeholder');
	if (!test_espace_prive() AND $val == 'oui') 
		return true;
	return false;
}

/**
 * Récupérer l'id du CV si l'auteur en en déjà déposé un
 *
 * @param int $id_auteur
 * @return string|int
 *	new si pas de CV, $id du CV sinon
 */
function emplois_get_id_cv($id_auteur){
	$id_cv = sql_getfetsel('id_cv', 'spip_cvs', 'id_auteur='.intval($id_auteur));
	if (is_null($id_cv))
		$id_cv = 'new';
	return $id_cv;
}

/**
 * Savoir si un PDF a déjà été associé au CV
 *
 * @param int $id_auteur
 * @return bool
 */
function emplois_pdf_deja_depose($id_auteur) {
	$id_document_cv = sql_getfetsel('id_document_cv', 'spip_cvs', "id_auteur=$id_auteur");
	if (!is_null($id_document_cv))
		return true;
	return false;
}
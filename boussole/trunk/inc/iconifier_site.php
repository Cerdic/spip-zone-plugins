<?php
/**
 * Ce fichier contient la fonction surchargeable de récupération des informations d'un plugin.
 *
 * @package SPIP\BOUSSOLE\Outils\Plugins
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Remplacement ou ajout d'un logo au site. L'image source est connue par son url.
 *
 * @param int $id_site
 * 		id du site concerné.
 * @param string $mode
 * 		Le type de l'icone, "on" pour le logo de base, ou "off" pour le survol.
 * @param string $url_image
 * 		url de l'image source destinée à devenir le logo du site.
 * @return void
 */
function inc_iconifier_site_dist($id_site, $mode, $url_image) {
	include_spip('inc/chercher_logo');
	$objet = objet_type('site');
	$nom_id_objet = id_table_objet($objet);
	$type = type_du_logo($nom_id_objet);

	$chercher_logo = charger_fonction('chercher_logo','inc');
	$logo = $chercher_logo($id_site, $nom_id_objet, 'on');
	if ($logo)
		spip_unlink($logo[0]);

	include_spip('action/iconifier');
	include_spip('inc/distant');
	$ajouter_image = charger_fonction('spip_image_ajouter','action');

	$image = creer_image_pour_iconifier($url_image);
	$ajouter_image($type.$mode.$id_site, " ", $image);
}

function creer_image_pour_iconifier($url_image) {
	$image = array('error' => 1);

	$fichier = _DIR_RACINE . copie_locale($url_image, 'force');
	if ($fichier) {
		$image['tmp_name'] = $fichier;
		$image['name'] = basename($fichier);
		$image['type'] = 'image/' . strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
		$image['size'] = filesize($fichier);
		$image['error'] = 0;
	}

	return $image;
}

?>

<?php
/**
 * JAZ - Joindre Automatiquement une Zone
 * Cyril MARION (c)2012 GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Détecter la création d'un nouvel auteur et demander son ajout aux zones
 *
 * On détecte via 'inscription' ou 'editer_auteur'
 * 
 * @param array $flux
 * @return array
 */
function jaz_formulaire_traiter($flux)
{

	// Si on est sur le formulaire d'inscription d'un nouveau visiteur
	if ($flux['args']['form'] == 'inscription') {
		// on chope le mail pour la requete plus bas...
		$mail = _request('mail_inscription');
		$nom  = _request('nom_inscription');
		if (function_exists('test_inscription'))
			$f = 'test_inscription';
		else    $f = 'test_inscription_dist';

		// On teste la validité de l'inscription
		// $desc = $f($mode, $mail, $flux['args']['args'][0], $flux['args']['args'][2]);
		$desc = $f($mode, $mail, $nom, $flux['args']['args'][2]);

		if (is_array($desc)
			AND $mail = $desc['email']
		) {
			include_spip('base/abstract_sql');

			// On cherche le numéro de l'auteur dont le mail a été saisi
			$auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email="' . $mail . '"');

			// On ajoute cet auteur aux zones
			jaz_ajouter_auteur_zones($auteur);

		}
	}
	// Si on est sur le formulaire d'édition d'un auteur
	if ($flux['args']['form'] == 'editer_auteur') {
		if (!intval($flux['args']['args'][0])
			AND intval($flux['data']['id_auteur'])
		) {
			$id_auteur = $flux['data']['id_auteur'];
			include_spip('base/abstract_sql');

			// On ajoute cet auteur aux zones
			jaz_ajouter_auteur_zones($auteur);
		}
	}
	return $flux;
}



/**
 * Chercher les zones automatiques à joindre et ajouter l'auteur à ces zones
 *
 * Les zones peuvent être indiquées soit :
 * - par la constante `_ZONES_AUTO_JOINTES` (dans `mes_options.php` par exemple)
 *   tel que `define('_ZONES_AUTO_JOINTES', '2:4')` où 2 et 4 sont des identifiants de zone
 * - soit, à défaut de constante, par les zones indiquées dans le formulaire de configuration
 *   du plugin (meta `jaz/zones_automatiques`).
 * 
 * @param int $id_auteur Identifiant d'auteur
 */
function jaz_ajouter_auteur_zones($id_auteur)
{

	// On cherche les Zones Auto Jointes :
	if (defined('_ZONES_AUTO_JOINTES')) {
		$zones = explode(':', _ZONES_AUTO_JOINTES);
	} else {
		include_spip('inc/config');
		$zones = explode(',', lire_config('jaz/zones_automatiques'));
	}

	if ($zones) {
		include_spip('action/editer_zone');
		zone_lier($zones, 'auteur', $id_auteur);
		spip_log('Auteur ' . $id_auteur . ' ajouté aux zones : ' . implode(', ', $zones), 'jaz');
	}

}

?>

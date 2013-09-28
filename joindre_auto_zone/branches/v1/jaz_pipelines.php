<?php
/**
 * JAZ - Joindre Automatiquement une Zone
 * Cyril MARION (c)2012 GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('jaz_fonctions');

/**
 * Détecter la création d'un nouvel auteur (via 'inscription' ou 'editer_auteur') et demander son ajout aux zones
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


?>

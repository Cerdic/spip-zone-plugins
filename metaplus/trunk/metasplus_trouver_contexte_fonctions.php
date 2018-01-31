<?php
/**
 * Fonctions utiles à ce squelette
 */

/**
 * Retrouver le contexte d'après l'URL : type de page, objet éventuel
 *
 * @Note
 * Il n'est pas recommandé d'utiliser $GLOBALS['contexte], donc on utilise la fonction qui décode l'URL et retourne les bonnes infos :
 * [0]            => page (le fond)
 * [1][id_patate] => id si page d'un objet
 * [1][erreur]    => erreur éventuelle (404)
 *
 * @param string $url
 * @return array En gros on reformate le tableau retourné par decoder_url
 * [type-page]
 * [objet]
 * [id_objet]
 * [id_patate]
 * [erreur] true si page en erreur
 */
function metasplus_trouver_contexte($url) {

	$res              = array();
	$decoder_url      = charger_fonction('decoder_url', 'urls');
	$decodage         = $decoder_url($url);
	$res['type-page'] = $decodage[0];
	$res['erreur']    = isset($decodage[1]['erreur']) ? true : false;

	// 1) Si la page est identifiée et pas en erreur, on regarde s'il s'agit d'un objet
	if ($res['type-page']
		and !$res['erreur']
	) {
		include_spip('base/objets');
		$id_table_objet = id_table_objet($res['type-page']);
		$id_objet = isset($decodage[1][$id_table_objet]) ? $decodage[1][$id_table_objet] : null;
		if ($id_objet) {
			$res['objet'] = $res['type-page'];
			$res['id_objet'] = $id_objet;
			$res[$id_table_objet] = $id_objet; // ça peut servir
		}

	// 2) Sinon c'est en principe une page lambda avec 'page' en query string
	} elseif (!$res['type-page']) {
		$res['type-page'] = _request('page');
	}

	return $res;
}
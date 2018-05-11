<?php
/**
 * Fonctions utiles au plugin Métas+
 *
 * @plugin     Métas+
 * @copyright  2016-2018
 * @author     Tetue, Erational, Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Metas+\Fonctions
 */

/**
 * Retrouver le contexte d'après l'URL : type de page, objet éventuel
 *
 * @Note
 * Il n'est pas recommandé d'utiliser $GLOBALS['contexte],
 * donc on utilise la fonction qui décode l'URL
 * et retourne un tableau linéaire avec les bonnes infos :
 * [0]            => page (le fond)
 * [1][id_patate] => identifiant si page d'un objet
 * [1][erreur]    => erreur éventuelle (404)
 *
 * @param string $url
 * @return array
 *     Tableau associatif :
 *     [type-page] le type de la page
 *     [objet]     le type de l'objet le cas échéant
 *     [id_objet]  son identifiant
 *     [id_patate] idem, mais avec le nom de sa clé primaire
 *     [erreur]    'true' si page en erreur
 */
function metasplus_identifier_contexte($url) {

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


/**
 * Sélectionner le squelette des métadonnées pour un type de page
 *
 * Règle :
 * On va chercher dans le dossier inclure/metasplus le squelette
 * de la variante spécifique au type de page s'il existe,
 * sinon le squelette générique dist.html qui génère automatiquement les métas.
 *
 * @param array $contexte
 *     Contexte de la page, avec le type de page, le type d'objet etc.
 * @return string
 *     Le fond
 */
function metasplus_selectionner_fond($type_page) {

	$fond_defaut   = 'inclure/metasplus/dist';
	$fond_variante = 'inclure/metasplus/' . $type_page;
	if (find_in_path($fond_variante.'.html')) {
		$fond = $fond_variante;
	} elseif (find_in_path($fond_defaut.'.html')) {
		$fond = $fond_defaut;
	}

	return $fond;
}
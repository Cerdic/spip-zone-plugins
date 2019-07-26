<?php
/**
 * Fonctions utiles au plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Inc
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifier si il existe déjà des logos de téléchargés pour un type d'objet
 * Exception : le logo du site (dans 'Identité du site') n'est pas pris en compte
 * 
 * @type string
 * @return bool
 */
function lim_verifier_presence_logo($type) {
	include_spip('inc/chercher_logo');
	include_spip('base/objets');
	$id_objet = id_table_objet($type);
	$prefixe_logo = _DIR_LOGOS.type_du_logo($id_objet).'*.*';
	$liste_logos = glob($prefixe_logo);

	// ne pas prendre en compte le logo du site (id = 0)
	if ($type == 'spip_syndic') {
		$chercher_logo = charger_fonction('chercher_logo','inc');
		$logo_du_site = $chercher_logo(0,'id_syndic');
		if(!empty($logo_du_site[0])) {
			$logo_du_site = array_slice($logo_du_site, 0, 1);
			$liste_logos = array_diff($liste_logos, $logo_du_site);
		}
	}
	
	if (is_array($liste_logos) AND count($liste_logos) > 0) return true;
	return false;
}

/**
 * Vérifier si il existe déjà des pétitions
 * @return bool
 */
function lim_verifier_presence_petitions() {
	/* recherche de pétitions */
	if (sql_countsel('spip_petitions', "statut='publie'") > 0) {
		return true;
	}
	return false;
}

/**
 * si LIM est activé pour un objet et si une seule rubrique est activée, ne pas afficher le sélecteur de rubrique
 * surcharge de inc/chercher_rubrique.php
 * @return string
 *     Code HTML du sélecteur
 */
function inc_chercher_rubrique($id_rubrique, $type, $restreint, $idem = 0, $do = 'aff') {
	include_spip('inc/chercher_rubrique');
	if (sql_countsel('spip_rubriques') < 1) {
		return '';
	}
	/* surcharge */
	$rubriques_restreintes = lire_config("lim_rubriques/$type");
	if (!is_null($rubriques_restreintes)) {
		$nbre_rubriques_total		= sql_countsel('spip_rubriques');
		$nbre_rubriques_desactives	= count($rubriques_restreintes);

		if ($nbre_rubriques_total - $nbre_rubriques_desactives <= 1) {
			return '';
		}
	}
	// note : du coup, plus de input name='id_parent' ! Un traitement via le pipeline "editer_contenu_objet" s'occupe de palier à ce problème.
	/* fin surcharge */
		

	// Mode sans Ajax :
	// - soit parce que le cookie ajax n'est pas la
	// - soit parce qu'il y a peu de rubriques
	if (_SPIP_AJAX < 1
		or $type == 'breve'
		or sql_countsel('spip_rubriques') < _SPIP_SELECT_RUBRIQUES
	) {
		return selecteur_rubrique_html($id_rubrique, $type, $restreint, $idem);
	} else {
		return selecteur_rubrique_ajax($id_rubrique, $type, $restreint, $idem, $do);
	}

}

/**
 * Vérifier si il existe déjà des objets dans la rubrique
 * on renvoi un tableau avec le type et la table_objet
 * @param int $id_rubrique
 * @param string $objet
 * @return bool
 */
function lim_verifier_presence_objets($id_rubrique, $objet) {
	$table = table_objet_sql($objet);
	if (sql_countsel($table, "id_rubrique=$id_rubrique") > 0) return true;
	return false;
}


/**
 * Construire la liste des objets à exclure
 * les objets SPIP qui ne sont jamais listés dans rubrique, et donc non pertinents dans la restriction par rubrique.
 *
 * exception : pour les brèves et les sites, on vérifie qu'elles ont été activées
 * exception : les documents si ceux-ci ont été activés dans les rubriques (menu Configuration -> Contenu du site -> paragraphe Documents joints)
 * 
 * @return array
 *	tableau des nom de tables SPIP à exclure (ex : spip_auteurs, spip_mots, etc.)
 */
function lim_objets_a_exclure() {
	$exclus = array();
	$tables = lister_tables_objets_sql();
	foreach ($tables as $key => $value) {
		if ($value['editable'] == 'oui' AND !isset($value['field']['id_rubrique']))
			array_push($exclus,$key);	
	}
	
	// Exception pour les objets breves et sites : sont-ils activés
	if (lire_config('activer_breves') == 'non') {
		array_push($exclus, 'spip_breves');
	}
	if (lire_config('activer_sites') == 'non') {
		array_push($exclus, 'spip_syndic');
	}

	// Exception pour les documents (si ils ont été activés pour les rubriques)
	$document_objet = lire_config('documents_objets');
	if (strpos($document_objet, 'spip_rubriques')) {
		$key = array_search('spip_documents', $exclus);
		unset($exclus[$key]);
	}

	// donner aux plugins la possibilité de gérer les exclusions (ajouter, supprimer une exclusion)
	$exclus = pipeline('lim_declare_exclus', $exclus);

	return $exclus;
}

/**
 * Récupérer la liste des rubriques dans lesquelles il est possible de créer l'objet demandé
 * 
 * @param string $type
 * @return array
 */

function lim_publierdansrubriques($type) {
	$rubriques_choisies = array();
	$tab_rubrique_objet = lire_config("lim_rubriques/$type");

	// si l'objet n'est pas dans la config, on sort
	if (is_null($tab_rubrique_objet)) {
		return $rubriques_choisies;
	} 

	$res = sql_allfetsel('id_rubrique', 'spip_rubriques');
	$tab_rubriques = array_column($res, 'id_rubrique');
	$rubriques_choisies = array_diff($tab_rubriques,$tab_rubrique_objet);
	return $rubriques_choisies;
}

/**
 * Récupérer le type des objets sélectionnés. ex. spip_articles -> article
 * 
 * @param array 
 * @return array
 */
function lim_type($tableau) {
	if (!is_array($tableau)) {
		return '';
	}

	array_walk($tableau, 'lim_get_type');
	return $tableau;
}

/**
 * fonction callback pour lim_type
 * Changer les valeurs du tableau spip_articles -> article
 */
function lim_get_type(&$value, $key) {
	$value = objet_type(table_objet($key));
}

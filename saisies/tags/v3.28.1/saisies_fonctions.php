<?php

/**
 * Déclaration de fonctions pour les squelettes
 *
 * @package SPIP\Saisies\Fonctions
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies');
include_spip('balise/saisie');
include_spip('inc/saisies_afficher_si_js');
// picker_selected (spip 3)
include_spip('formulaires/selecteur/generique_fonctions');

/**
 * Retourne une balise `div` si on est en SPIP >= 3.1, sinon le texte en parametre.
 *
 * @example `[(#VAL{ul}|saisie_balise_structure_formulaire)]`
 * @see balise_DIV_dist() pour une écriture plus courte.
 * @note Préférer `[(#DIV|sinon{ul})]` dans les squelettes, plus lisible.
 *
 * @param $tag
 *   ul ou li
 * @return string
 *   $tag initial ou div
 */
function saisie_balise_structure_formulaire($tag) {

	static $is_div = null;
	if (is_null($is_div)) {
		$version = explode('.', $GLOBALS['spip_version_branche']);
		if ($version[0] > 3 or ($version[0] == 3 and $version[1] > 0)) {
			$is_div = true;
		}
	}
	if ($is_div) {
		return 'div';
	}
	return $tag;
}

if (
	!function_exists('balise_DIV_dist')
	and $version = explode('.', $GLOBALS['spip_version_branche'])
	and ($version[0]>3 or ($version[0]==3 and $version[1] > 0))
) {

	/**
	 * Compile la balise `DIV` qui retourne simplement le texte `div`
	 *
	 * Sert à la compatibilité entre SPIP 3.0 et SPIP 3.1+
	 *
	 * Variante d'écriture, plus courte, que le filtre `saisie_balise_structure_formulaire`
	 *
	 * À partir de SPIP 3.1
	 * - ul.editer-groupe deviennent des div.editer-groupe
	 * - li.editer devient div.editer
	 *
	 * @see saisie_balise_structure_formulaire()
	 * @example
	 *     `[(#DIV|sinon{ul})]`
	 *
	 * @param Pile $p
	 * @return Pile
	 */
	function balise_DIV_dist($p) {
		$p->code = "'div'";
		$p->interdire_scripts = false;
		return $p;
	}
}

/**
 * Traiter la valeur de la vue en fonction du env
 * si un traitement a ete fait en amont (champs extra) ne rien faire
 * si pas de traitement defini (formidable) passer typo ou propre selon le type du champ
 *
 * @param string $valeur
 * @param string|array $env
 * @return string
 */
function saisie_traitement_vue($valeur, $env) {
	if (is_string($env)) {
		$env = unserialize($env);
	}
	if (!function_exists('propre')) {
		include_spip('inc/texte');
	}
	if (!is_array($valeur)) {
		$valeur = trim($valeur);
	}
	// si traitement est renseigne, alors le champ est deja mis en forme
	// (saisies)
	// sinon on fait une mise en forme smart
	if ($valeur and !is_array($valeur) and !isset($env['traitements'])) {
		if (in_array($env['type_saisie'], array('textarea'))) {
			$valeur = propre($valeur);
		} else {
			$valeur = '<p>' . typo($valeur) . '</p>';
		}
	}

	return $valeur;
}

/**
 * Passer un nom en une valeur compatible avec une classe css
 *
 * - toto => toto,
 * - toto/truc => toto_truc,
 * - toto[truc] => toto_truc
 *
 * @param string $nom
 * @return string
**/
function saisie_nom2classe($nom) {
	return str_replace(array('/', '[', ']', '&#91;', '&#93;'), array('_', '_', '', '_', ''), $nom);
}

/**
 * Ajouter une ou des classes sur la saisie en fonction du type
 * @param $type_saisie
 * @return string
 */
function saisie_type2classe($type_saisie) {
	static $compteur = 0;
	$class = "saisie_{$type_saisie}";
	if (strpos($type_saisie, 'selecteur') === 0) {
		$class .= " selecteur_item";
	}
	if (!in_array($type_saisie, array('hidden','fieldset'))) {
		$class .= ($compteur & 1) ? " editer_even" : " editer_odd";
		$compteur = 1 - $compteur;
	}
	$class = trim($class);
	return $class;
}

/**
 * Passer un nom en une valeur compatible avec un `name` de formulaire
 *
 * - toto => toto,
 * - toto/truc => toto[truc],
 * - toto/truc/ => toto[truc][],
 * - toto[truc] => toto[truc]
 *
 * @see saisie_name2nom() pour l'inverse.
 * @param string $nom
 * @return string
**/
function saisie_nom2name($nom) {
	if (false === strpos($nom, '/')) {
		return $nom;
	}
	$nom = explode('/', $nom);
	$premier = array_shift($nom);
	$nom = implode('][', $nom);
	return $premier . '[' . $nom . ']';
}

/**
 * Passer un `name` en un format de nom compris de saisies
 *
 * - toto => toto,
 * - toto[truc] => toto/truc,
 * - toto[truc][] => toto/truc/
 * - toto/truc => toto/truc
 *
 * @see saisie_nom2name() pour l'inverse.
 * @param string $name
 * @return string
 **/
function saisie_name2nom($name) {
	if (false === strpos($name, '[')) {
		return $name;
	}
	$name = explode('[', str_replace(']', '', $name));
	return implode('/', $name);
}

/**
 * Compile la balise `#GLOBALS{xxx}` qui retourne la valeur d'une vilaine variable globale de même nom si elle existe
 *
 * @example
 *     ```
 *     #GLOBALS{debut_intertitre}
 *     ```
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée du code php de la balise.
**/
function balise_GLOBALS_dist($p) {
	if (function_exists('balise_ENV')) {
		return balise_ENV($p, '$GLOBALS');
	} else {
		return balise_ENV_dist($p, '$GLOBALS');
	}
}

/**
 * Liste les éléments du sélecteur générique triés
 *
 * Les éléments sont triés par objets puis par identifiants
 *
 * @example
 *     L'entrée :
 *     'rubrique|3,rubrique|5,article|2'
 *     Retourne :
 *     array(
 *        0 => array('objet'=>'article', 'id_objet' => 2),
 *        1 => array('objet'=>'rubrique', 'id_objet' => 3),
 *        2 => array('objet'=>'rubrique', 'id_objet' => 5),
 *     )
 *
 * @param string $selected
 *     Liste des objets sélectionnés
 * @return array
 *     Liste des objets triés
**/
function picker_selected_par_objet($selected) {
	$res = array();
	$liste = picker_selected($selected);
	// $liste : la sortie dans le désordre
	if (!$liste) {
		return $res;
	}

	foreach ($liste as $l) {
		if (!isset($res[ $l['objet'] ])) {
			$res[ $l['objet'] ] = array();
		}
		$res[$l['objet']][] = $l['id_objet'];
	}
	// $res est trié par objet, puis par identifiant
	ksort($res);
	foreach ($res as $objet => $ids) {
		sort($res[$objet]);
	}

	// on remet tout en file
	$liste = array();
	foreach ($res as $objet => $ids) {
		foreach ($ids as $id) {
			$liste[] = array('objet' => $objet, 'id_objet' => $id);
		}
	}

	return $liste;
}


/**
 * Lister les objets qui ont une url_edit renseignée et qui sont éditables.
 *
 * @return array Liste des objets :
 *               index : nom de la table (spip_articles, spip_breves, etc.)
 *               'type' : le type de l'objet ;
 *               'url_edit' : l'url d'édition de l'objet ;
 *               'texte_objets' : le nom humain de l'objet éditorial.
 */
function lister_tables_objets_edit() {
	include_spip('base/abstract_sql');

	$objets = lister_tables_objets_sql();
	$objets_edit = array();

	foreach ($objets as $objet => $definition) {
		if (isset($definition['editable']) and isset($definition['url_edit']) and $definition['url_edit'] != '') {
			$objets_edit[$objet] = array('type' => $definition['type'], 'url_edit' => $definition['url_edit'], 'texte_objets' => $definition['texte_objets']);
		}
	}
	$objets_edit = array_filter($objets_edit);

	return $objets_edit;
}

/**
 * Afficher la chaine de langue traduite.
 *
 * @param string $chaine
 * @return string
 */
function saisies_label($chaine) {
	$chaine = trim($chaine);
	if (preg_match("/^(&lt;:|<:)/", $chaine)) {
		$chaine = preg_replace("/^(&lt;:|<:)/", "", $chaine);
		$chaine = preg_replace("/(:&gt;|:>)$/", "", $chaine);
		return _T($chaine);
	}

	return $chaine;
}

/**
 * Masque les derniers caractères d'une clé secrete
 *
 * @param string $cle
 * @return string
**/
function saisies_masquer_cle_secrete($cle) {
	if (!defined('_SAISIES_ΤΑUX_MASQUE_CLE_SECRETE')) {
		define('_SAISIES_ΤΑUX_MASQUE_CLE_SECRETE',0.85);
	}
	$taille = strlen($cle);
	$a_masquer = round($taille * _SAISIES_ΤΑUX_MASQUE_CLE_SECRETE, 0, PHP_ROUND_HALF_UP);
	$court = substr($cle, 0, $taille-$a_masquer);
	$cle = $court.str_repeat("*",$a_masquer);
	return $cle;
}

/**
 * Les liens ouvrants, c'est mal en général.
 * Sauf dans un cas particulier : dans les explications dans un formulaire.
 * En effet, si le lien n'est pas ouvrant, la personne en train de remplir un formulaire
 * a) lis une explication
 * b) clique sur le lien pour savoir comment remplir son formulaire
 * c) est redirigée directement vers une page
 * d) perd du coup ce qu'elle avait commencé remplir.
 * Par conséquent, en terme d'accessibilité, il vaut mieux POUR LES EXPLICATIONS DE FORMULAIRE
 * avoir des liens systématiquement ouvrant,
 * et ce que le lien pointe en interne ou en externe (ce qui distingue du filtre |liens_ouvrants).
 * D'où un filtre saisies_liens_ouvrants
 * @param string $texte
 * @return string $texte
**/
function saisies_liens_ouvrants($texte) {
	if (preg_match_all(",(<a\s+[^>]*https?://[^>]*\b[^>]+>),imsS",
		$texte, $liens, PREG_PATTERN_ORDER)) {
		foreach ($liens[0] as $a) {
			$rel = 'noopener noreferrer ' . extraire_attribut($a, 'rel');
			$ablank = inserer_attribut($a, 'rel', $rel);
			$ablank = inserer_attribut($ablank, 'target', '_blank');
			$texte = str_replace($a, $ablank, $texte);
		}
	}
	return $texte;
}

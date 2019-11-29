<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Tetue
 * Licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('public/spip_bonux_criteres');
include_spip('public/spip_bonux_balises');

/**
 * une fonction pour generer une balise img a partir d'un nom de fichier
 *
 * @param string $img
 * @param string $alt
 * @param string $class
 *
 * @return string
 */
function tag_img($img, $alt = '', $class = '') {
	$balise_img = chercher_filtre('balise_img');

	return $balise_img($img, $alt, $class);
}

if (!defined('_PREVISU_TEMPORAIRE_VALIDITE')) {
	define('_PREVISU_TEMPORAIRE_VALIDITE', 12);
}

function previsu_verifier_cle_temporaire($cle) {
	$validite = _PREVISU_TEMPORAIRE_VALIDITE; // validite de 12h maxi par défaut
	$old = 0;

	do {
		$date = date('Y-m-d H', strtotime("-$old hour"));
		if ($cle == previsu_cle_temporaire($date)) {
			return true;
		}
	} while ($old++ < $validite);

	return false;
}

function previsu_cle_temporaire($date = null) {
	include_spip('inc/securiser_action');

	if (!$date) {
		$date = date('Y-m-d H');
	}

	$url = self();
	$cle = md5($url . $date . secret_du_site());

	return $cle;
}

if (!function_exists('push_table_valeur')) {
	/**
	 * Filtre ajoutant une valeur à la fin d'une liste, possiblement dans une clé d'un tableau (comme table_valeur()).
	 *
	 * Attention, cette fonction est bien un "push", c'est-à-dire qu'elle ajoute un élément à la fin d'une liste.
	 *
	 * `#TABLEAU|push_table_valeur{a/b/c, valeur, #CONDITION}`
	 * - si la clé "c" est une liste : on ajoute "valeur" à la fin
	 * - si la clé "c" n'existe pas : on crée une liste et on met la première valeur dedans
	 * - si la clé "c" est un scalaire : on ne fait rien
	 * - si les clés autres que la dernière n'existent pas : on en fait des tableaux en cascade
	 *
	 * @see table_valeur
	 * @filtre
	 *
	 * @param array|object|string $table
	 *                         Table ou objet dont on veut modifier ou augmenter une des clés/propriétés. Peut être une chaîne à désérialiser contenant ce tableau ou cet objet.
	 * @param string $chemin
	 *                         Une suite de clés ou de propriétés d'objet, séparées par le caractère "/" : un/element/ici
	 * @param mixed $valeur
	 *                         Une valeur à ajouter au paramètre précédent.
	 * @param mixed $condition =true
	 *                         Une valeur quelconque qui sera testée de manière booléenne pour savoir si on fait le traitement ou pas.
	 *                         Cela allège l'écriture si l'ajout de valeur ne doit se faire que sous condition, notamment que s'il y a une valeur à ajouter.
	 * @param string $cle_finale
	 *                         Une clé dans laquelle placer la valeur si précisée
	 *
	 * @return array|object
	 *     Retourne le tableau ou l'objet initial, modifié suivant les paramètres.
	 */
	function push_table_valeur($table, $chemin, $valeur, $condition = true, $cle_finale = false) {
		// Si la condition est fausse, on ne fait rien du tout
		if ($condition) {
			$table = is_string($table) ? @unserialize($table) : $table;
			$element_a_modifier =& $table;

			// S'il y a un emplacement donné, on cherche si c'est un tableau
			if ($chemin and is_string($chemin) and $chemin = explode('/', $chemin) and is_array($chemin)) {
				$i = 0;
				foreach ($chemin as $cle) {
					$i += 1;
					// On ne fait que s'il la clé vaut quelque chose
					if ($cle !== '') {
						// Si c'est un tableau, on vérifie si la clé existe
						if (is_array($element_a_modifier)) {
							// Si la clé n'existe pas : on met un tableau vide qui se remplira
							if (!isset($element_a_modifier[$cle])) {
								$element_a_modifier[$cle] = array();
							}
							$element_a_modifier =& $element_a_modifier[$cle];
						} elseif (is_object($element_a_modifier)) {
							// Si c'est un objet on vérifie si la propriété existe
							// Si la propriété n'existe pas : on met un tableau vide qui se remplira
							if (!isset($element_a_modifier->$cle)) {
								$element_a_modifier->$cle = array();
							}
							$element_a_modifier =& $element_a_modifier->$cle;
						}
					}
				}
			}

			// Maintenant on est au bon endroit de ce qu'on veut modifier :

			// Si l'élément à modifier est bien un tableau : on push la valeur dedans
			if (is_array($element_a_modifier)) {
				if ($cle_finale and is_string($cle_finale)) {
					$element_a_modifier[$cle_finale] = $valeur;
				} else {
					array_push($element_a_modifier, $valeur);
				}
			}
			// Sinon (si c'est un scalaire) on ne fait rien et il faudra utiliser set_table_valeur() par exemple
		}

		return $table;
	}
}

/**
 * Cette fonction permet de transformer un texte clair en nom court pouvant servir d'identifiant, class, id, url...
 * en ne conservant que des caracteres alphanumeriques et un separateur
 *
 * @param string $texte
 *   Texte à transformer en nom machine
 * @param string $type
 *
 * @param array $options
 *   string separateur : par défaut, un underscore `_`.
 *   int longueur_maxi : par defaut 60
 *
 * @return string
 */
function filtre_slugify_dist($texte, $type = '', $options = array()) {

	$separateur = (isset($options['separateur'])?$options['separateur']:'_');
	$longueur_maxi = (isset($options['longueur_maxi'])?$options['longueur_maxi']:60);

	if (!function_exists('translitteration')) {
		include_spip('inc/charsets');
	}

	// pas de balise html
	if (strpos($texte, '<') !== false) {
		$texte = strip_tags($texte);
	}
	if (strpos($texte, '&') !== false) {
		$texte = unicode2charset($texte);
	}
	// On enlève les espaces indésirables
	$texte = trim($texte);

	// On enlève les accents et cie
	$texte = translitteration($texte);

	// On remplace tout ce qui n'est pas un mot par un separateur
	$texte = preg_replace(',\W+,ms', $separateur, $texte);

	// nettoyer les doubles occurences du separateur si besoin
	while (strpos($texte, "$separateur$separateur") !== false) {
		$texte = str_replace("$separateur$separateur", $separateur, $texte);
	}

	// pas de separateur au debut ni a la fin
	$texte = trim($texte, $separateur);

	// en minuscules
	$texte = strtolower($texte);

	switch ($type) {
		case 'class':
		case 'id':
		case 'anchor':
			if (preg_match(',^\d,', $texte)) {
				$texte = substr($type, 0, 1).$texte;
			}
	}

	if (strlen($texte)>$longueur_maxi) {
		$texte = substr($texte, 0, $longueur_maxi);
	}

	return $texte;
}

if (!function_exists('slugify')) {
	function slugify($texte, $type = '', $options = array()) {
		$slugify = chercher_filtre('slugify');
		return $slugify($texte, $type, $options);
	}
}

<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin	   foundation_6
 * @copyright  2013
 * @author	   Phenix
 * @licence	   GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Inclure les fonctions de foundation
include_spip('inc/foundation');

/**
 * Rendre les iframes responsives via un filtre et
 * la classe responsive-embed de Foundation.
 *
 * @param string $texte HTML dans lequel chercher des iframes
 * @access public
 * @return string
 */
function filtre_responsive_embed_dist($texte) {

	if (!empty($texte)) {
		$dom = new DomDocument;

		// Charger le texte de l'article dans DOMDocument
		$dom->LoadHTML($texte, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOENT);

		// Récupérer les iframes du document
		$iframes = $dom->getElementsByTagName('iframe');

		// Créer la div qui contiendra l'iframe et lui ajouter les class
		// foundation
		$div = $dom->createElement('div');
		$div->setAttribute('class', 'responsive-embed flex-video');

		// On boucle sur toutes les iframes
		foreach ($iframes as $iframe) {
			// Faire une copie de la div responsive
			$div_clone = $div->cloneNode();

			// Récupérer la largeur et la hauteur définie dans l'iFrame
			$height = $iframe->getAttribute('height');
			$width = $iframe->getAttribute('width');

			// Définir s'il faut ajouter la class widescreen ou non
			if ($height and $width) {
				$ratio = intval($width)/intval($height);
				$ratio_4_3 = 4/3;

				if ($ratio > $ratio_4_3) {
					$div_clone->setAttribute('class', 'responsive-embed flex-video widescreen');
				}
			}

			// Injecter la div à la place de l'iframe
			$iframe->parentNode->replaceChild($div_clone, $iframe);
			// Injecter l'iframe dans le div
			$div_clone->appendChild($iframe);
		}

		return $dom->saveHTML();
	}

	return $texte;
}


/**
 * Assurer la rétro-compatibilité avec l'ancien nom de ce filtre
 *
 * @deprecated Utiliser filtre_responsive_embed_dist()
 * @see filtre_responsive_embed_dist()
 *
 * @param mixed $texte
 * @access public
 * @return mixed
 */
function filtre_iframe_responsive($texte) {
	$responsive_embed = charger_filtre('responsive_embed');
	return $responsive_embed($texte);
}
/**
 * Cette balise va permettre de rendre le squelette compatible
 * avec toutes les versions de Foundation.
 * La syntaxe est la suivante:
 *
 * #COLONNES{nombre,type}
 * nombre: le nombre de colonne foundation
 * (optionnel) type: Dans le cas des version utilisant une
 * syntaxe avec prefix, on lui passe le type (défaut: large)
 *
 * @param mixed $p
 * @access public
 * @return mixed
 */
function balise_COLONNES_dist($p) {
	// On récupère les paramètres de la balise.
	$nombre_colonnes = interprete_argument_balise(1, $p);
	$type = interprete_argument_balise(2, $p);

	// On met une valeur par défaut à type.
	if (!$type) {
		$type = "'large'";
	}

	// On calcule la class
	$p->code = "class_grid_foundation($nombre_colonnes, $type).'columns'";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Generer un bouton d'action qui accepte les class de foundation
 */
function balise_BOUTON_ACTION($p) {

	$args = array();
	for ($k=1; $k<=6; $k++) {
		$_a = interprete_argument_balise($k, $p);
		if (!$_a) {
			$_a="''";
		}
		$args[] = $_a;
	}
	// supprimer les args vides
	while (end($args)=="''" and count($args)>2) {
		array_pop($args);
	}
	$args = implode(',', $args);

	$bouton_action = chercher_filtre('bouton_action');
	$p->code = "$bouton_action($args)";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * On surcharge le filtre bouton_action pour ajouter $class
 * sur la balise <button> au lieu de pour assurer la
 * compatibilité avec les class button de foundation
 */
function filtre_bouton_action($libelle, $url, $class = '', $confirm = '', $title = '', $callback = '') {
	if ($confirm) {
		$confirm = 'confirm("' . attribut_html($confirm) . '")';
		if ($callback) {
			$callback = "$confirm?($callback):false";
		} else {
			$callback = $confirm;
		}
	}
	$onclick = $callback?" onclick='return ".addcslashes($callback, "'")."'":'';
	$title = $title ? " title='$title'" : '';

	if (test_espace_prive()) {
		return "<form class='bouton_action_post $class' method='post' action='$url'><div>".form_hidden($url)
		 ."<button type='submit' class='submit'$title$onclick>$libelle</button></div></form>";
	} else {

		// Détection de la class ajax
		// Code reprit (et modifié) du plugin bootstrap:
		// http://zone.spip.org/trac/spip-zone/browser/_plugins_/bootstrap/trunk/bootstrap3_fonctions.php#L109
		$array_class = explode(' ', $class);
		$ajax_index = array_search('ajax', $array_class);
		if ($ajax_index !== false) {
			$ajax = ' ajax';
			// On a plus besoin de la class ajax dans la liste des class,
			// ça pourrait créer des problèmes
			unset($array_class[$ajax_index]);
			$class = implode(' ', $array_class);
		} else {
			$ajax = '';
		}

		return "<form class='bouton_action_post $ajax' method='post' action='$url'><div>".form_hidden($url)
		."<button type='submit' class='submit $class'$title$onclick>$libelle</button></div></form>";
	}
}

/**
 * Filtre pour afficher des étoiles à la suite via les
 * icone foundation.
 *
 * @param int $nombre
 * @access public
 * @return string
 */
function filtre_etoile_foundation_dist($nombre) {

	$config = lire_config('foundation');

	if (!$config['foundation-icons']) {
		return '<span>Les icones foundation ne sont pas activée !</span>';
	}

	$etoile = '<span class="foundation_etoile">';
	for ($i=0; $i<$nombre; $i++) {
		$etoile .= '<span class="fi-star"></span>';
	}
	$etoile .= '</span>';

	return $etoile;
}


if (!function_exists('balise_LIRE_CONSTANTE_dist')) {
	/**
	 * Balise LIRE_CONSTANT pour SPIP
	 *
	 * @param mixed $p
	 * @access public
	 * @return mixed
	 */
	function balise_LIRE_CONSTANTE_dist($p) {
		$constante = interprete_argument_balise(1, $p);

		$p->code = "constant($constante)";

		$p->interdire_scripts = false;

		return $p;
	}
}
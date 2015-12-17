<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin	   foundation-4-spip
 * @copyright  2013
 * @author	   Phenix
 * @licence	   GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

// Inclure les fonctions de foundation
include_spip('inc/foundation');


/**
 * Rendre les iframes responsive via un filtre et
 * la classe flex-video de Foundation.
 *
 * @param string $texte HTML dans lequel chercher des iframes
 * @access public
 * @return string
 */
function filtre_iframe_responsive($texte) {
	include_spip('inc/foundation');
	// On détecte tout les iFrames et on les rends responsives.
	return preg_replace_callback('/<iframe(.+)><\/iframe>/', 'responsive', $texte);
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
function balise_F_BOUTON_ACTION_dist($p){

	$args = array();
	for ($k=1;$k<=6;$k++){
		$_a = interprete_argument_balise($k,$p);
		if (!$_a) $_a="''";
		$args[] = $_a;
	}
	// supprimer les args vides
	while(end($args)=="''" AND count($args)>2) {
		array_pop($args);
	}
	$args = implode(",",$args);

	$bouton_action = chercher_filtre("f_bouton_action");
	$p->code = "$bouton_action($args)";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * On surcharge le filtre bouton_action pour ajouter $class
 * sur la balise <button> au lieu de pour assurer la
 * compatibilité avec les class button de foundation
 */
function filtre_f_bouton_action_dist($libelle, $url, $class="", $confirm="", $title="", $callback=""){
	if ($confirm) {
		$confirm = "confirm(\"" . attribut_html($confirm) . "\")";
		if ($callback)
			$callback = "$confirm?($callback):false";
		else
			$callback = $confirm;
	}
	$onclick = $callback?" onclick='return ".addcslashes($callback,"'")."'":"";
	$title = $title ? " title='$title'" : "";
	return "<form class='bouton_action_post' method='post' action='$url'><div>".form_hidden($url)
			 ."<button type='submit' class='submit $class'$title$onclick>$libelle</button></div></form>";
}


/**
 * Filtre pour afficher des étoiles à la suite via les
 * icone foundation.
 *
 * @param mixed $nombre
 * @access public
 * @return mixed
 */
function filtre_etoile_foundation_dist ($nombre) {

	$config = lire_config('foundation');

	if (!$config['foundation-icons']) {
		return '<span>Les icones foundation ne sont pas activée !</span>';
	}

	$etoile = '<span class="foundation_etoile">';
	for ($i=0;$i<$nombre; $i++) {
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
		$constante = interprete_argument_balise(1,$p);

		$p->code = "constant($constante)";

		$p->interdire_scripts = false;

		return $p;
	}
		}
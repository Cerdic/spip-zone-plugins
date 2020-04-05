<?php
/**
 * Fonctions utiles au plugin deckjs
 *
 * @plugin     deckjs
 * @copyright  2016
 * @author     Tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Deckjs\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * on transforme le texte spip
 *
 * On ajoute encadre les h3.spip par des section.slide
 * on ajoute des class "slide" au li
 *
 * @param $texte
 * @return $texte
 * @author tofulm
 **/
function creer_deckjs($texte,$objet='article'){

	$texte = propre($texte);

	if ($objet == "article"){
		// on ajoute les sections et on remplace les h3 en h1
		$search = '<h3 class="spip">';
		$replace = '</section><section class="slide alignement"><h2 class="spip">';
		$texte = str_replace($search,$replace,$texte);
		$texte = str_replace('</h3>','</h2>',$texte);

		// on efface le premier </section>
		$texte = substr($texte,10);

		// on ajoute le </section> terminal
		$texte .= '</section>';
	}

	// ajoute le class="slide" au li
	$search = '<li>';
	$replace = '<li class="slide">';
	$texte = str_replace($search,$replace,$texte);

	return $texte;
}

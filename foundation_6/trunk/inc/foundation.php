<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction de callback utilisé par le filtre `|responsive_embed`.
 * A chaque iFrame, on encadre de div.responsive-embed.
 *
 * On détecte aussi le ratio de l'iFrame (via les attributs html) pour ajouter
 * automatiquement la class widescreen
 *
 * @param  string $matches iframe
 * @return string          iframe encadrée
 */
function responsive($matches) {
	// On inclu les filtres, au cas ou
	include_spip('inc/filtres');

	$class = '';

	// Récupérer la largeur et la hauteur définie dans l'iFrame
	$height = extraire_attribut($matches[0], 'height');
	$width = extraire_attribut($matches[0], 'width');

	if ($height and $width) {
		$ratio = intval($width)/intval($height);
		$ratio_4_3 = 4/3;

		if ($ratio > $ratio_4_3) {
			$class = ' widescreen';
		}
	}

	// On revoie la bonne structure html d'iframe.
	return wrap($matches[0], '<div class="responsive-embed flex-video'.$class.'">');
}

/**
 * Cette fonction va créer la class foundation de la balise #COLONNES
 *
 * @param int|array|string $nombre_colonnes Nombre de colonne désiré. Un étoile
 * (ex: 4*) activera les colonnes calculée
 * @param  string $type type de colonne: large, medium ou small
 * @return string $class foundation applicable directement.
 */
function class_grid_foundation($nombre_colonnes, $type = '', $total_boucle = null) {
	// Si la première variable est un tableau, on va le convertir en class
	if (is_array($nombre_colonnes)) {
		$class= '';
		foreach ($nombre_colonnes as $key => $value) {
			// On va traiter le nombre de colonne avant de créer la class css
			$calculer_colonnes = false; // Par défaut les colonnes calculée sont désactivées
			if (strpos($value, '*')) {
				$calculer_colonnes = true; // L'étoile est détectée, on active les colonnes calculée
				$value = str_replace('*', '', $value); // Supprimer l'étoile
			}

			// Utiliser un tableau large => 4
			if (is_numeric($value)) {
				if (!is_null($total_boucle) and $calculer_colonnes) {
					$value = calculer_colonnes($value, $total_boucle);
				}

				$class .= $key.'-'.$value.' ';
			}
		}
		return $class;
	} else {
		return $type.'-'.$nombre_colonnes.' ';
	}
}

/**
 * Cette fonction va calculer le nombre de colonne et les limiter à $max
 *
 * @param int $max nombre maximum de colonne
 * @param int $total_boucle nombre d'élément dans la boucle
 * @access public
 * @return int
 */
function calculer_colonnes($max, $total_boucle) {
	if ($total_boucle >= $max) {
		return $max;
	} else {
		return 12/$total_boucle;
	}
}

/**
 * Cette fonction va créer la class Foundation de la balise #BLOCKGRID
 *
 * @param int|array|string $nombre_colonnes Nombre de colonne désiré. Un étoile
 * (ex: 4*) activera les colonnes calculée
 * @param  string $type type de colonne: large-up, medium-up ou small-up
 * @return string $class Foundation applicable directement.
 */
function class_blocs_foundation($nombre_colonnes, $type = '', $total_boucle = null) {
	// Si la première variable est un tableau, on va le convertir en class
	if (is_array($nombre_colonnes)) {
		$class= '';
		foreach ($nombre_colonnes as $key => $value) {
			// On va traiter le nombre de colonne avant de créer la class css
			$calculer_colonnes = false;
			if (strpos($value, '*')) {
				$calculer_colonnes = true; // L'étoile est détectée, on active les colonnes calculée
				$value = str_replace('*', '', $value); // Supprimer l'étoile
			}

			// Utiliser un tableau large => 4
			if (is_numeric($value)) {
				if (!is_null($total_boucle) and $calculer_colonnes) {
					$value = calculer_bloc($value, $total_boucle);
				}

				$class .= $key.'-'.$value.' ';
			}
		}
		return $class;
	} else {
		return $type.'-'.$nombre_colonnes.' ';
	}
}

/**
 * Cette fonction va calculer le nombre de colonne d'une blockgrid et les limiter à $max
 *
 * @param int $max nombre maximum de colonne
 * @param int $total_boucle nombre d'élément dans la boucle
 * @access public
 * @return int
 */
function calculer_bloc($max, $total_boucle) {
	if ($total_boucle >= $max) {
		return $max;
	} else {
		return $total_boucle;
	}
}

/**
 * Utiliser jQl pour charger les scripts de Foundation
 *
 * @param array $files les fichiers à envoyer dans jQl
 * @access public
 */
function jQlfoundation($files) {
	$js = '';

	// Trimer le tableau pour éviter tout problème
	$files = array_map('trim', $files);

	// Dans le cas ou jQl n'est pas activé pour tout les scripts
	if (!defined('_JS_ASYNC_LOAD') and !test_espace_prive()) {
		// On cherche dans un premier temps si jQl existe
		lire_fichier(find_in_path('lib/jQl/jQl.min.js'), $jQl);
		if ($jQl) {
			// Inclure les librairie utile du compresseur
			include_spip('inc/compresseur_concatener');
			include_spip('inc/compresseur_minifier');

		// traitement des fichiers, on concatène et minifie le tout
			$foundation_js = concatener_fichiers($files);
			$foundation_js = minifier($foundation_js[0]);

			// charger et utiliser jQl sur les fichiers de foundation
			$js = "<script type=\"text/javascript\">\n".$jQl."\n";
			$js .= 'jQl.loadjQ("'.$foundation_js.'");'."\n";
			$js .= '</script>';
		}
	} elseif (defined('_JS_ASYNC_LOAD') and !test_espace_prive()) {
		// Dans le cas ou jQl est activé pour tout les scripts, on va simplement
		// les référencés de manière "classique" pour qu'ils soit intégrer par
		// le compresseur.
		foreach ($files as $file) {
			$js .= '<script type="text/javascript" src="'.trim($file).'"/>';
		}
	}

	return $js;
}

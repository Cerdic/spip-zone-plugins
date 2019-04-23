<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

// demander a SPIP de definir 'type-page' dans le contexte du premier squelette
define('_DEFINIR_CONTEXTE_TYPE_PAGE', true);
define('_ZPIP', true);
// differencier le cache,
// la verification de credibilite de var_zajax sera faite dans public_styliser_dist
// mais ici on s'assure que la variable ne permet pas de faire une inclusion arbitraire
// avec un . ou un /
if ($z = _request('var_zajax') AND !preg_match(",[^\w-],", $z)) {
	if (!isset($GLOBALS['marqueur'])) {
		$GLOBALS['marqueur'] = "$z:";
	} else {
		$GLOBALS['marqueur'] .= "$z:";
	}
	$GLOBALS['flag_preserver'] = true;
} else {
	// supprimer cette variable dangereuse
	set_request('var_zajax', '');
}

if (!isset($GLOBALS['spip_pipeline']['recuperer_fond'])) $GLOBALS['spip_pipeline']['recuperer_fond'] = '';
$GLOBALS['spip_pipeline']['recuperer_fond'] .= '||zcore_recuperer_fond';

/**
 * Passe un chemin en URL absolue uniquement si non vide
 * utilise pour l'insertion d'URL conditionnee a l'existence d'un fichier (favicon.ico par exemple)
 *
 * @param string $path
 * @param string $base
 *
 * @return string
 */
function url_absolue_si($path, $base = '') {
	if (!$path) {
		return "";
	}
	if (!function_exists('url_absolue')) {
		include_spip('inc/filtres_mini');
	}

	return url_absolue($path, $base);
}

/**
 * html Pour pouvoir masquer les logos sans les downloader en petit ecran
 * il faut le mettre dans un conteneur parent que l'on masque
 * http://timkadlec.com/2012/04/media-query-asset-downloading-results/
 *
 * On utilise un double conteneur :
 * le premier fixe la largeur, le second la hauteur par le ratio hauteur/largeur
 * grace a la technique des intrinsic-ratio ou padding-bottom-hack
 * http://mobile.smashingmagazine.com/2013/09/16/responsive-images-performance-problem-case-study/
 * http://alistapart.com/article/creating-intrinsic-ratios-for-video
 *
 * Le span interieur porte l'image en background CSS
 * Le span conteneur ne porte pas de style display car trop prioritaire.
 * Sans CSS il occupe la largeur complete disponible, car en inline par defaut
 * Il suffit de lui mettre un float:xxx ou un display:block pour qu'il respecte la largeur initiale du logo
 *
 * Pour masquer les logos :
 * .spip_logos {display:none}
 * Pour forcer une taille maxi :
 * .spip_logos {max-width:25%;float:right}
 *
 * @param $logo
 *
 * @return string
 */
function responsive_logo($logo) {
	if (!function_exists('extraire_balise')) {
		include_spip('inc/filtres');
	}
	if (!$logo OR !$img = extraire_balise($logo, "img")) {
		return $logo;
	}
	list($h, $w) = taille_image($img);
	if (!$h or !$w) {
		return $logo;
	}
	$src = extraire_attribut($img, "src");
	$class = extraire_attribut($img, "class");

	// timestamper l'url si pas deja fait
	if (strpos($src, "?") == false) {
		$src = timestamp($src);
	}

	if (defined('_STATIC_IMAGES_DOMAIN')) {
		$src = url_absolue($src, _STATIC_IMAGES_DOMAIN);
	}

	$hover = "";
	if ($hover_on = extraire_attribut($img, "onmouseover")) {
		$hover_off = extraire_attribut($img, "onmouseout");
		$hover_on = str_replace("this.src=", "jQuery(this).css('background-image','url('+", $hover_on) . "+')')";
		$hover_off = str_replace("this.src=", "jQuery(this).css('background-image','url('+", $hover_off) . "+')')";
		$hover = " onmouseover=\"$hover_on\" onmouseout=\"$hover_off\"";
	}

	$ratio = round($h * 100 / $w, 2);

	return "<span class='$class' style=\"width:{$w}px;\"><span class=\"img\" style=\"display:block;position:relative;height:0;width:100%;padding-bottom:{$ratio}%;overflow:hidden;background:url($src) no-repeat center;background-size:100%;\"$hover> </span></span>";
}

/**
 * Compatibilite : permet de remplacer les [(#TEXTE|image_reduire{500})] des squelettes
 * par un simple [(#TEXTE|adaptive_images)]
 * Avec le plugin adaptive_images cela produire des images adaptives
 */
if (!defined('_DIR_PLUGIN_ADAPTIVE_IMAGES')) {
	// les images 1x sont au maximum en _ADAPTIVE_IMAGES_MAX_WIDTH_1x px de large dans la page
	if (!defined('_ADAPTIVE_IMAGES_MAX_WIDTH_1x')) {
		define('_ADAPTIVE_IMAGES_MAX_WIDTH_1x', 640);
	}

	function adaptive_images($texte, $max_width_1x = _ADAPTIVE_IMAGES_MAX_WIDTH_1x) {
		if (!function_exists('filtrer')) {
			include_spip('inc/filtres');
		}
		$texte = filtrer('image_reduire', $texte, $max_width_1x, 10000);

		return filtrer('image_graver', $texte);
	}
}

/**
 * #ICON{search,icon-sm,Rechercher}
 * @param $p
 * @return mixed
 */
function balise_ICON_dist($p) {
	$_name = interprete_argument_balise(1, $p);
	if (!$_name) {
		// compat avec les champs #ICON utilises dans composition et noizetier : pas d'argument = champ sql (ou DATA)
		$_icon = champ_sql('icon', $p);
		$p->code = $_icon;
	}
	else {
		$_class = interprete_argument_balise(2, $p);
		if (!$_class) {
			$_class = "''";
		}
		$_alt = interprete_argument_balise(3, $p);
		if (!$_alt) {
			$_alt = "''";
		}
		$p->code = "afficher_icone_svg($_name, $_class, $_alt)";
	}

	$p->interdire_scripts = false;
	return $p;
}

function afficher_icone_svg($name, $class = '', $alt = '') {
	static $sprite_file;
	if (is_null($sprite_file)) {
		if (!defined('_ICON_SPRITE_SVG_FILE')) {
			define('_ICON_SPRITE_SVG_FILE', "css/bytesize/bytesize-symbols.min.svg");
		}
		$sprite_file = timestamp(find_in_path(_ICON_SPRITE_SVG_FILE));
	}
	if (!$name) {
		return $sprite_file;
	}
	if ($sprite_file) {
		$name = preg_replace(",[^\w\-],", "", $name);
		/*
			<svg aria-labelledby="my-icon-title" role="img">
		    <title id="my-icon-title">Texte alternatif</title>
		    <use xlink:href="bytesize-symbols.min.svg#search"></use>
	    </svg>
			<svg aria-hidden="true" role="img">
		    <use xlink:href="bytesize-symbols.min.svg#search"></use>
			</svg>
		 */
		// width="0" height="0" -> rien ne s'affiche si on a pas la CSS icons.css
		$svg = "<svg role=\"img\" width=\"0\" height=\"0\"";
		if ($alt) {
			$id = "icon-title-" . substr(md5("$name:$alt:$sprite_file"),0,4);
			$svg .= " aria-labelledby=\"$id\"><title id=\"$id\">" . entites_html($alt)."</title>";
		}
		else {
			$svg .= ">";
		}
		$icone_ancre_from_name = chercher_filtre("icone_ancre_from_name");
		$ancre = $icone_ancre_from_name($name);
		$svg .= "<use xlink:href=\"$sprite_file#$ancre\"></use>";
		$svg .= "</svg>";

		if ($class = trim($class)) {
			$class = preg_replace(",[^\w\s\-],", "", $class);
		}
		return "<i class=\"icon icon-$name" . ($class ? " $class" : "") . "\">$svg</i> ";
	}
	return "";
}

function filtre_icone_ancre_from_name_dist($name) {
	switch ($name) {
		case "comment":
			$ancre = 'msg';
			break;
		case "ok-circle":
			$ancre = 'compose';
			break;
		default:
			$ancre = $name;
			break;
	}
	return "i-$ancre";
}

function lister_icones_svg() {
	$sprite_file = afficher_icone_svg('');
	if ($sprite_file
		and $sprite_file = supprimer_timestamp($sprite_file)
	  and $sprite = file_get_contents($sprite_file)
	  and preg_match_all(',id="i-([\w\-]+)",', $sprite, $matches, PREG_PATTERN_ORDER)) {
		$icons = $matches[1];
		return $icons;
	}
	return array();
}



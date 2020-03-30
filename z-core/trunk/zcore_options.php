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

function zcore_recuperer_fond($flux) {
	if (!function_exists('zcore_recuperer_fond_detecter_404')) {
		include_spip('zcore_pipelines');
	}
	return zcore_recuperer_fond_detecter_404($flux);
}

if (
	defined('_SPIPR_AUTH_DEMO')?
		_SPIPR_AUTH_DEMO
		:
		(isset($GLOBALS['visiteur_session']['statut'])
    AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
    AND $GLOBALS['visiteur_session']['webmestre']=='oui')
	)
	_chemin(_DIR_PLUGIN_ZCORE."demo/");

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
 * .spip_logo {display:none}
 * Pour forcer une taille maxi :
 * .spip_logo {max-width:25%;float:right}
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

	return "<span class='logo-img-wrapper $class' style=\"width:{$w}px;\"><span class=\"img\" style=\"display:block;position:relative;height:0;width:100%;padding-bottom:{$ratio}%;overflow:hidden;background:url($src) no-repeat center;background-size:100%;\"$hover> </span></span>";
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
 * utiliser une icone standard du sprite par defaut :
 * #ICON{search,icon-sm,Rechercher}
 *
 * utiliser une icone #search definie dans un svg inline de la page
 * #ICON{#search,icon-sm,Rechercher}
 *
 * utiliser une l'icone #search definie dans un svg externe (qui sera resolu via #CHEMIN)
 * #ICON{img/sprite.svg#search,icon-sm,Rechercher}
 *
 * utiler une icone svg du path, sans connaitre son id
 * #ICON{img/mon_icone_search.svg,icon-sm,Rechercher}
 *
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

/**
 * Fonction interne utilisee par la balise #ICON
 * @param string $name
 * @param string $class
 * @param string $alt
 * @return string
 */
function afficher_icone_svg($name, $class = '', $alt = '') {
	$icone_href_class_from_name = chercher_filtre("icone_href_class_from_name");
	list($href, $class_base) = $icone_href_class_from_name($name);
	if (!$name) {
		return $href;
	}

	if ($href) {
		if ($class_base = trim($class_base)) {
			$class_base = ' icon-' . $class_base;
		}
		if ($class = trim($class)) {
			$class = preg_replace(",[^\w\s\-],", "", $class);
		}

		if (strpos($href, '#') === false) {
			$id = "icon-title-" . substr(md5("$name:$alt:$href"),0,4);
			$svg = afficher_icone_inline_svg(supprimer_timestamp($href), $id, $alt);
		}
		else {
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
				$id = "icon-title-" . substr(md5("$name:$alt:$href"),0,4);
				$svg .= " aria-labelledby=\"$id\"><title id=\"$id\">" . entites_html($alt)."</title>";
			}
			else {
				$svg .= ">";
			}
			$svg .= "<use xlink:href=\"$href\"></use>";
			$svg .= "</svg>";
		}

		if ($svg) {
			return "<i class=\"icon{$class_base}" . ($class ? " $class" : "") . "\">$svg</i> ";
		}
	}
	return "";
}

/**
 * function qui permet d'afficher une icone svg inline
 * La fonction supprime tout ce qui se trouve au dessus de la balise <svg>
 * et force un width=0 et un height=0 car ils seront definis en CSS
 * l'image sera toujours affichee au format carre
 *
 * @param string $svg_file
 *   chemin du fichier
 * @param string $id
 * @param string $title
 * @return string
 *  le code svg
 */
function afficher_icone_inline_svg($svg_file, $id = '', $title = ''){

	if (!file_exists($svg_file) or !$svg = file_get_contents($svg_file)) {
		return;
	}

	$svg = explode("<svg", $svg);
	array_shift($svg);
	array_unshift($svg, "");
	$svg = implode("<svg", $svg);

	$svg = explode(">", $svg, 2);
	$balise_svg = array_shift($svg);

	if ($title) {
		// on ajoute le aria-labelledby si besoin
		$balise_svg .= ' aria-labelledby="'.$id.'"';
		$title = "<title id='".$id."'>".entites_html($title)."</title>";
	}
	// on supprime id, width et height du svg
	$balise_svg = preg_replace('/(\s+(id|width|height)=["\'].*?["\'])/s', '', $balise_svg);
	// on ajoute le role, width et height
	// width="0" height="0" -> rien ne s'affiche si on a pas la CSS icons.css
	$balise_svg .= ' role="img" width="0" height="0">' . $title;

	$svg = $balise_svg . end($svg);

	return $svg;
}

/**
 * filtre surchargeable pour determiner le href et la class en fonction du nom de l'icone demandee
 * @param string $name
 * @return array
 */
function filtre_icone_href_class_from_name_dist($name) {
	static $sprite_files = array();

	if (strpos($name,'#') !== false or strpos($name,'/') !== false or strpos($name,'.svg') !== false) {
		// l'ancre est fournie explicitement (sprite inline)
		// voire le nom du fichier sprite svg
		list($filename, $anchor) = array_pad(explode('#', trim($name), 2), 2, null);
		// sanitizer l'ancre pour la class
		if ($anchor) {
			$class = preg_replace(",[^\w\-],", "", $anchor);
		}
		else {
			$class = preg_replace(",[^\w\-],", "", basename($filename, '.svg'));
		}

		if ($filename) {
			if (!isset($sprite_files[$filename])) {
				$sprite_files[$filename] = timestamp(find_in_path($filename));
			}
			$filename = $sprite_files[$filename];
			return array($filename . ($anchor ? '#' . $anchor : ''), $class);
		}
		else {
			return array($name, $class);
		}
	}
	else {
		// c'est le sprite par defaut avec un name qui correspond a l'ancre abregee
		// et la gestion de quelques historiques de nommage/renommage
		if (!isset($sprite_files[''])) {
			if (!defined('_ICON_SPRITE_SVG_FILE')) {
				define('_ICON_SPRITE_SVG_FILE', "css/bytesize/bytesize-symbols.min.svg");
				define('_ICON_SPRITE_SVG_ID_PREFIX', "i-");
			}
			$sprite_files[''] = timestamp(find_in_path(_ICON_SPRITE_SVG_FILE));
		}
		// sanitizer l'ancre pour la class
		$class = preg_replace(",[^\w\-],", "", $name);
		if (_ICON_SPRITE_SVG_ID_PREFIX) {
			$class .= " " . _ICON_SPRITE_SVG_ID_PREFIX . "icon";
		}
		if (!$name) {
			return array($sprite_files[''], $class);
		}
		$icone_anchor_from_name = chercher_filtre("icone_anchor_from_name");
		$anchor = $icone_anchor_from_name($name);
		return array($sprite_files[''] . '#' . $anchor, $class);
	}
}

/**
 * Filtre surchargeable pour renommer les icones a la volee quand on adapte le jeu d'icone
 * @param string $name
 * @return string
 */
function filtre_icone_anchor_from_name_dist($name) {
	if (_ICON_SPRITE_SVG_ID_PREFIX) {
		if (strpos($name, _ICON_SPRITE_SVG_ID_PREFIX) === 0) {
			$name = substr($name, strlen(_ICON_SPRITE_SVG_ID_PREFIX));
		}
	}
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
	return _ICON_SPRITE_SVG_ID_PREFIX . $ancre;
}

/**
 * Fonction utilisee par la page demo/icons
 * liste tous les ids d'un sprite svg
 * @param string $sprite_file
 * @return array
 */
function lister_icones_svg($sprite_file = '') {
	$trim_prefix = false;
	if (!$sprite_file) {
		$sprite_file = afficher_icone_svg('');
		$trim_prefix = true;
	}

	if ($sprite_file
		and $sprite_file = supprimer_timestamp($sprite_file)
	  and $sprite = file_get_contents($sprite_file)
	  and preg_match_all(',id="([\w\-]+)",', $sprite, $matches, PREG_PATTERN_ORDER)) {
		$icons = $matches[1];
		if ($trim_prefix and _ICON_SPRITE_SVG_ID_PREFIX){
			foreach ($icons as $k => $name){
				if (strpos($name, _ICON_SPRITE_SVG_ID_PREFIX)===0){
					$icons[$k] = substr($name, strlen(_ICON_SPRITE_SVG_ID_PREFIX));
				}
			}
		}
		sort($icons);
		return $icons;
	}
	return array();
}



<?php

$GLOBALS['marqueur_skel'] = (isset($GLOBALS['marqueur_skel']) ?  $GLOBALS['marqueur_skel'] : '').":bootstrap4";
// la puce sans image si jamais on est pas encore en SPIP 3.3+
$GLOBALS['puce'] = '<span class="spip-puce ltr"><b>–</b></span>';
$GLOBALS['puce_rtl'] = '<span class="spip-puce rtl"><b>–</b></span>';

// les icones BS4
// un fichier qui contient toutes les icones, utilise pour la page de demo uniquement
define('_ICON_SPRITE_SVG_FILE', "img/bi-all-symbols.svg");
// 2 sprites : celui de base + celui avec les variantes fill
define('_ICON_SPRITE_SVG_NOFILL_FILE', "img/bi-symbols.svg");
define('_ICON_SPRITE_SVG_FILL_FILE', "img/bi-fill-symbols.svg");

define('_ICON_SPRITE_SVG_ID_PREFIX', "bi-");

function filtre_icone_href_class_from_name($name) {
	static $sprite_files = [];
	if (strpos($name,'#') !== false or strpos($name,'/') !== false or strpos($name,'.svg') !== false){
		return filtre_icone_href_class_from_name_dist($name);
	}
	else {
		if (!$name) {
			return array(find_in_path(_ICON_SPRITE_SVG_FILE), '');
		}

		// c'est le sprite par defaut avec un name qui correspond a l'ancre abregee
		// et la gestion de quelques historiques de nommage/renommage
		if (strpos($name, '-fill') !== false){
			if (!isset($sprite_files['fill'])){
				$sprite_files['fill'] = timestamp(find_in_path(_ICON_SPRITE_SVG_FILL_FILE));
			}
			$file = $sprite_files['fill'];
		}
		else {
			if (!isset($sprite_files['nofill'])){
				$sprite_files['nofill'] = timestamp(find_in_path(_ICON_SPRITE_SVG_NOFILL_FILE));
			}
			$file = $sprite_files['nofill'];
		}
		// sanitizer l'ancre pour la class
		$class = preg_replace(",[^\w\-],", "", $name);
		if (_ICON_SPRITE_SVG_ID_PREFIX) {
			$class .= " " . _ICON_SPRITE_SVG_ID_PREFIX . "icon";
		}

		$icone_anchor_from_name = chercher_filtre("icone_anchor_from_name");
		$anchor = $icone_anchor_from_name($name);
		return array($file . '#' . $anchor, $class);
	}
}

function filtre_icone_anchor_from_name($name) {
	if (_ICON_SPRITE_SVG_ID_PREFIX) {
		if (strpos($name, _ICON_SPRITE_SVG_ID_PREFIX) === 0) {
			$name = substr($name, strlen(_ICON_SPRITE_SVG_ID_PREFIX));
		}
	}
	switch ($name) {
		case "comment":
		case "msg":
			$ancre = 'chat';
			break;
		case "ok-circle":
			$ancre = 'check-box';
			break;
		case "user":
			$ancre = 'person';
			break;
		case "end":
		case "start":
			$ancre = 'skip-' . $name;
			break;
		case "close":
			$ancre = 'x';
			break;
		default:
			$ancre = $name;
			break;
	}
	return _ICON_SPRITE_SVG_ID_PREFIX . $ancre;
}


function bootstrap4_affichage_final($flux){
	if (
		$GLOBALS['html']
		AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
		AND $GLOBALS['visiteur_session']['webmestre']=='oui'
		AND strpos($flux,"<!-- insert_head -->")!==false
		AND $p=stripos($flux,"</body>")
	) {
		if ($f = find_in_path("js/hashgrid.js")){
			$flux = substr_replace($flux,'<script type="text/javascript" src="'.$f.'"></script>',$p,0);
		}
		if ((_VAR_MODE === 'debug' || _request('var_profile'))
			AND $p=stripos($flux,"</head>")){
			$file_css = direction_css(scss_select_css('css/spip.admin.css'));
			$css = file_get_contents($file_css);
			$css = "<style type='text/css'>$css</style>";
			$flux = substr_replace($flux,$css,$p,0);
		}

	}
	return $flux;
}

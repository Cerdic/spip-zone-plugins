<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// On déclare les nouveaux modèles de documents pour qu'ils soient prix en compte avec le critère {vu}
function medias_responsive_mod_declarer_tables_objets_sql($tables) {
	$tables['spip_documents']['modeles'][] = 'ligne';
	$tables['spip_documents']['modeles'][] = 'slide';
	return $tables;
}

function medias_responsive_mod_insert_head_css($flux) {
	$flux = "\n<link rel='stylesheet' type='text/css' media='all' href='".direction_css(find_in_path("css/medias_responsive.css"))."'>\n".$flux;
	return $flux;
}

function medias_responsive_mod_insert_head($flux) {
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/rAF.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_ligne.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_slide.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	return $flux;
}

function medias_responsive_mod_header_prive($flux) {
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/rAF.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_ligne.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/portfolio_slide.js")."'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path("javascript/medias_responsive.js")."'></script>\n";
	$flux .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".find_in_path("css/medias_responsive.css")."\">\n";
	return $flux;
}

function medias_responsive_mod_post_echappe_html_propre($txt) {
	$txt = preg_replace (",</ul>[\r\n\ ]*<ul class=\"portfolio_ligne\">,", "", $txt);
	$txt = preg_replace (",</ul>[\r\n\ ]*<ul class=\"portfolio_slide\">,", "", $txt);
	
	$txt = preg_replace_callback(
			",<ul class=\"portfolio_slide\">(.*)<\/ul>,sU",
			function($matches) {
				$m = $matches[0];
				
				$rand = rand(0,100000);
				if (preg_match_all(",<li>,s", $m, $res)){
					$nombre = count($res[0]);
				}
				
				$chk = "";
				$nav = "";
				for ( $i = 0; $i < $nombre; $i++) {
					if ($i == 0) $checked=" checked";
					else $checked = "";
					$chk .= "<input type='radio' id='check_ligne_$rand$i' class='portfolio_slide_radio sel$i' name='check_ligne_$rand' value='$i'$checked>";
					if ($i > 0) $nav .= "<label for='check_ligne_$rand".($i-1)."' class='label_ligne label_ligne_precedent label_ligne_$i'><span>"._T('precedent')."</span></label>";
					if ($i < $nombre-1) $nav .= "<label for='check_ligne_$rand".($i+1)."' class='label_ligne label_ligne_suivant label_ligne_$i'><span>"._T('suivant')."</span></label>";
				}
				return "<div class=\"portfolio_slide_container\">".$chk.$m.$nav."</div>";
			},
			$txt);
	return $txt;
}
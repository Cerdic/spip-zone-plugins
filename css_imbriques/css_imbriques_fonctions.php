<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


if (!defined("_UTILISER_PIE_HTC")) define("_UTILISER_PIE_HTC", true);
if (!defined("_UTILISER_BOXSIZING_HTC")) define("_UTILISER_BOXSIZING_HTC", true);


$GLOBALS["css_imbriques_medias_queries"] = array();

// filtre couleur_rgba converti une mention de couleur hexadecimale
// en couleur semi_transparente rgba
// [(#COULEUR_HEX|couleur_rgba{0.5})]
function DEFINIR_couleur_rgba () {
	function couleur_rgba($couleur, $alpha) {
		include_spip("inc/filtres_images_lib_mini");
		$couleurs = _couleur_hex_to_dec($couleur);

		$red = $couleurs["red"];
		$green = $couleurs["green"];
		$blue = $couleurs["blue"];
		
		return "rgba($red, $green, $blue, $alpha)";
	}
}

if (!function_exists('couleur_rgba')) {
	DEFINIR_couleur_rgba ();
}



function css_inserer_tab($def) {
	$def = preg_replace(",\n,", "\n\t", $def);
	
	return "\t".$def;
}


function css_contruire($css, $niveau, $chemin, $classe, $enfants, $definition) {

	$intitule = trim($classe[$niveau]);
	$ret = '';

	if (substr($intitule, 0, 2) == ". ") {
		$intitule = substr($intitule, 2, strlen($intitule));
		$chemin = $chemin.$intitule;
	}
	else {
		$chemin = trim($chemin ." ".$intitule);
	}
	
	$def = $definition[$niveau];
	
	if (strlen($def) > 0) {
//		echo "<li><b>$chemin</b>";
//		echo "<br>$def";
		
		$def = css_inserer_tab($def);
	
		if ( strlen(trim($chemin)) > 0 ) $ret = "\n".$chemin." {\n".$def."\n}";
//		echo "<pre>$css</pre>";
	}
	
	if (preg_match(",^\@(-spip-)?keyframes,", $intitule)) {
		// autoriser @keyframes et @-spip-keyframes
		$intitule = str_replace("@-spip-keyframes", "@keyframes", $intitule);
	
		if (isset($enfants[$niveau]) and $enfants[$niveau]) {
			$def_keyframes = "";
			foreach($enfants[$niveau] as $num) {
				 $def_keyframes .= css_contruire($css, $num, "", $classe, $enfants, $definition);
			}
			
			$ret .= preg_replace(",^@keyframes,","@-moz-keyframes",$intitule)." { $def_keyframes }";
			$ret .= preg_replace(",^@keyframes,","@-webkit-keyframes",$intitule)." { $def_keyframes }";
			$ret .= preg_replace(",^@keyframes,","@-o-keyframes",$intitule)." { $def_keyframes }";
			$ret .= preg_replace(",^@keyframes,","@-ms-keyframes",$intitule)." { $def_keyframes }";
			$ret .= $intitule." { $def_keyframes }";
		}
	}
	else if (isset($enfants[$niveau]) and $enfants[$niveau]) {
		foreach($enfants[$niveau] as $num) {
			$ret .= css_contruire($css, $num, $chemin, $classe, $enfants, $definition);
	
		}
	}
	
	return $ret;
}

function css_imbriques_couleurs_ie ($coul) {
	if (preg_match(",^\#([0-9a-fA-F])([0-9a-fA-F])([0-9a-fA-F])$,", $coul, $conv)) {
		$coul = "#".$conv[1].$conv[1].$conv[2].$conv[2].$conv[3].$conv[3];
	}
	
	return $coul;
}

function css_imbriques_conv_dec255 ($coul) {
	$coul = trim ($coul);
	
	if (preg_match(",(.*)\%$,", $coul, $pourcent)) {
		$coul = round($pourcent[1] * 255 / 100);
	}
	return $coul;
}

function css_imbriques_traiter_spip($regs) {
	// -spip-font-smoothing
	
	if (_UTILISER_PIE_HTC) {
		if (function_exists("chemin")) $pie = chemin("PIE.htc");
		else $pie = find_in_path("PIE.htc");
	}

	if (_UTILISER_BOXSIZING_HTC) {
		if (function_exists("chemin")) $boxsizing = url_absolue(chemin("boxsizing.htc"));
		else $boxsizing = url_absolue(find_in_path("boxsizing.htc"));
	}
	
	$style = $regs[1];
	$val = trim($regs[2]);
	switch($style) {
		case "border-radius" :
			$ret = "-webkit-border-radius:$val;";
			$ret .= "-moz-border-radius:$val;";
			$ret .= "border-radius:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "border-top-right-radius" :
			$ret = "-webkit-border-top-right-radius:$val;";
			$ret .= "-moz-border-radius-topright:$val;";
			$ret .= "border-top-right-radius:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "border-top-left-radius" :
			$ret = "-webkit-border-top-left-radius:$val;";
			$ret .= "-moz-border-radius-topleft:$val;";
			$ret .= "border-top-left-radius:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "border-bottom-right-radius" :
			$ret = "-webkit-border-bottom-right-radius:$val;";
			$ret .= "-moz-border-radius-bottomright:$val;";
			$ret .= "border-bottom-right-radius:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "border-bottom-left-radius" :
			$ret = "-webkit-border-bottom-left-radius:$val;";
			$ret .= "-moz-border-radius-bottomleft:$val;";
			$ret .= "border-bottom-left-radius:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "box-sizing" :
			$ret = "-webkit-box-sizing:$val;";
			$ret .= "-moz-box-sizing:$val;";
			$ret .= "box-sizing:$val;";
			if (_UTILISER_BOXSIZING_HTC) $ret .= "*behavior: url($boxsizing);";
			break;
		case "opacity" :
			$val_ie = round($val * 100);
			$ret = "-webkit-opacity:$val;";
			$ret .= "-moz-opacity:$val;";
			$ret .= "opacity:$val;";
			$ret .= "filter:alpha(opacity=$val_ie);";
			//$ret .= "-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(opacity=$val_ie)\";zoom:1;";
			break; 
		case "text-shadow":
			$ret .= "text-shadow:$val;";
			if (preg_match(",(\-?[0-9]+)px\ *(\-?[0-9]+)px\ *([0-9]+)px\ *(#?[0-9a-zA-Z]*),", $val, $val_ie)) {
				$x = $val_ie[1];
				$y = $val_ie[2];
				$s = $val_ie[3];
				$coul = $val_ie[4];
			}
			
			if ($x == 0 && $y == 0) {
				$ret .= "zoom:1; filter:progid:DXImageTransform.Microsoft.Glow(Color=$coul,Strength=$s);";
			}
			
			break;
		case "box-shadow": 
			$ret = "-webkit-box-shadow:$val;";
			$ret .= "-moz-box-shadow:$val;";
			$ret .= "box-shadow:$val;";
			if (_UTILISER_PIE_HTC) $ret .= "behavior: url($pie);";
			break;
		case "background-color":
			if (preg_match(",(rgba?)[\ \t]*\(([^\,]*)\,([^\,]*)\,([^\,]*)\,?([^\,]*)?\),", $val, $couls)) {
				$rgba = trim($couls[1]);
				$r = css_imbriques_conv_dec255($couls[2]);
				$g = css_imbriques_conv_dec255($couls[3]);
				$b = css_imbriques_conv_dec255($couls[4]);
				$a = trim($couls[5]);

				$red = dechex($r);
				$green = dechex($g);
				$blue = dechex($b);
				$alpha = dechex(round($a * 255));
				
				if (strlen($red) == 1) $red = "0".$red;
				if (strlen($green) == 1) $green = "0".$green;
				if (strlen($blue) == 1) $blue = "0".$blue;
				if (strlen($alpha) == 1) $alpha = "0".$alpha;

				//$ret = "background-color: #$red$green$blue;";
				if ($rgba == "rgba") {
					$ret .= "background-color: rgba($r,$g,$b,$a);";
					$ret .= "filter:  progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#$alpha$red$green$blue', endColorstr='#$alpha$red$green$blue');";
				} else {
					$ret = "background-color: #$red$green$blue;";
				}
			}
			break;
		case "gradient": 
			// -spip-gradient: top, #000000, #ffffff;
			// directions: "top" (vertical) ou "left" (horizontal)
			if (preg_match("#\ ?(.*)\ ?\,\ ?(.*)\ ?\,\ ?(.*)\ ?#", $val, $conv)) {
				$dir = strtolower($conv[1]);
				$debut = $conv[2];
				$fin = $conv[3];
				
				$debut_ie = css_imbriques_couleurs_ie($debut);
				$fin_ie = css_imbriques_couleurs_ie($fin);

				// $ret = "background: $debut;";
				
				if ($dir == "top") {
					$ret = "background: -webkit-gradient(linear, left top, left bottom, from($debut), to($fin));";
					$ret .= "background-image: -webkit-linear-gradient(top, $debut, $fin);";
					$ret .= "background-image: -moz-linear-gradient(top, $debut, $fin);";
					$ret .= "background-image: -ms-linear-gradient(top, $debut, $fin);";
					$ret .= "background-image: -o-linear-gradient(top, $debut, $fin);";
					$ret .= "background-image: linear-gradient(top, $debut, $fin);";
					$ret .= "filter:  progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='$debut_ie', endColorstr='$fin_ie');";
					// La version IE8 n'a pas l'air necessaire
					//$ret .= "-ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='$debut_ie', endColorstr='$fin_ie')\";";
				}
				else {
					$ret = "background: -webkit-gradient(linear, left top, right top, from($debut), to($fin));";
					$ret .= "background-image: -moz-linear-gradient(left, $debut, $fin);";
					$ret .= "filter:  progid:DXImageTransform.Microsoft.gradient(GradientType=1,startColorstr='$debut_ie', endColorstr='$fin_ie');";
					//$ret .= "-ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=1,startColorstr='$debut_ie', endColorstr='$fin_ie')\";";
				}
			}
			break;
		case "clear";
			$ret = "zoom:1;\n";
			$ret .= ". :before,. :after  {content:\"\";\ndisplay:block;overflow:hidden;}\n";
			$ret .= ". :after{clear:both;}\n";
			break;
		default:
			$ret = "-moz-$style:$val;";
			$ret .= "-webkit-$style:$val;";
			$ret .= "-khtml-$style:$val;";
			$ret .= "-ms-$style:$val;";
			$ret .= "-o-$style:$val;";
			$ret .= "$style:$val;";
			
	}
	return $ret;

	
}

function css_imbriques_pseudo($css) {
	
	$css = preg_replace_callback(",\-spip\-([a-z\-]*)\ *\:\ *([^\;]*)\ *\;,", "css_imbriques_traiter_spip", $css);
	
	return $css;
}

function css_imbriques_forcer_position($css) {
	if (_UTILISER_PIE_HTC)  {
		if (preg_match("/border[a-z\-]*\-radius|box\-shadow/", $css) && !preg_match("/position\ ?\:/", $css)) {
			$css .= "position:relative;";
		}
	}
	return $css;
}


function extraire_filters_ie($css) {
	if (preg_match_all(",filter\:(.*)\;,", $css, $regs)) {
		$filtres = "filter:".join($regs[1], ", ").";";
		
		$css = preg_replace(",filter\:(.*)\;,", "", $css);
		$css .= $filtres;
	}
	return $css;
}

function css_imbriques_decouper ($css) {
	$compteur = 0;	
	
	$css = preg_replace(",\n[\t\ ]*,", "\n", trim($css));
	$css = preg_replace(",\n+,", "\n", $css);
	
	// Remettre les criteres multilignes sur une ligne
	$css = preg_replace("#\,\ *\n#", ", ", $css);
	

	// Virer les commentaires (source d'erreurs, et on ne sait plus ou les placer puisqu'on reorganise la bazar)
	$css = preg_replace('#(/\*[^*]*\*+([^/*][^*]*\*+)*/)#', '', $css);
	$css = preg_replace('#\n(\ \t)*\{#', ' {', $css);


	// placer l'ensemble dans une fausse classe globale pour pouvoir la traiter d'un coup a la fin
	$css = "   {\n$css\n}";
	
	$css = css_imbriques_pseudo($css);
	
	while (preg_match ("/([^\{\n]*)\{([^\{]*)\}/U", $css, $regs)) {
		
			$intitule = trim($regs[1]);

			if (preg_match(",^@media,", $intitule)) $intitule .= " media@";

			// le minifieur de CSS vire les intitulés "0%"
			if ($intitule == "0%") $intitule = "from";


						
			$def = trim($regs[2]);
			$def = css_imbriques_forcer_position($def);

			$chaine = $regs[0];
			$pos = strpos($css, $chaine);
			$debut = substr($css, 0, $pos);
			$fin = substr($css, $pos + strlen($chaine), strlen($css));


			if (preg_match("#\,#", $intitule)) {
				$entrees = explode(",", $intitule);
				
				$ret = "";
				
				foreach ($entrees as $intitule) {
					$intitule = trim($intitule);
					
//					echo "<li>$intitule</li>";
					
					$ret .= "$intitule { $def }\n";
					
					
				}
								
				$css = $debut.$ret.$fin;
				
//				echo "<pre>$css </pre><hr>";
				
			} else {
			
				$compteur ++;
			
				$classe[$compteur] = $intitule;
				$definition[$compteur] = trim($regs[2]);
				
//				echo "<li>".$classe[$compteur];
				
				
				preg_match_all("/\[\[([0-9]*)\]\]/", $definition[$compteur], $sous);
				foreach($sous[1] as $enfant) {
					$enfants[$compteur][] = $enfant;
				}
	
				$definition[$compteur] = extraire_filters_ie(css_imbriques_forcer_position(preg_replace("#[\ \n]*\[\[[0-9]*\]\][\ \n]*#", "", $definition[$compteur])));
//				echo "<li>".$definition[$compteur];
//				$def = extraire_filters_ie($def);
	
				
				
				
				
				$css = $debut."[[$compteur]]".$fin;
			}
			
//			$css = str_replace($regs[0][$num], "[[$compteur]]", $css);
			
		
	}
	
	$css = "";
	
	$css = css_contruire($css, $compteur, "", $classe, $enfants, $definition);

	
	
	// Derniere passe: "minifier" les CSS
	$css = preg_replace(",\n,", "", $css);
	$css = preg_replace(",\t,", "", $css);
	$css = preg_replace(",\},", "}\n", $css);



	// Rechercher les media_queries
	$css = preg_replace_callback(",(.*(@media .* media@).*)\{(.*)\}\n?,", "css_imbriques_traiter_media", $css);
	if (count($GLOBALS["css_imbriques_medias_queries"]) > 0) {
		foreach($GLOBALS["css_imbriques_medias_queries"] as $k=>$val) {
			$css .= $k ."{\n". $val . "}\n";
		}
		$GLOBALS["css_imbriques_medias_queries"] = array();
	}

	return $css;
}


function css_imbriques_traiter_media($reg) {
	$query = $reg[2];
	$query = trim(substr(trim($query), 0, strlen($query) - 6));
	
	$intitule = str_replace($reg[2], "", $reg[1]);
	$intitule = trim(preg_replace(",\ +,", " ", $intitule));
	
	
	$definition = $reg[3];

	$GLOBALS["css_imbriques_medias_queries"]["$query"] .= $intitule."{".$definition."}\n";
	return;
}
function css_imbriques_traiter_keyframe ($flux) {
	print_r($flux);
	die();
}

function css_imbriques ($css) {

	$path = dirname(url_absolue($css))."/"; // pour mettre sur les images	
	
		

	// 2.
	$dir_var = sous_repertoire (_DIR_VAR, 'cache-css');
	$f = $dir_var
		. substr(md5($css), 0,20) . '_imbriques.css';


	// la css peut etre distante (url absolue !)
	if (preg_match(",^http:,i",$css)){
		include_spip('inc/distant');
		$contenu = recuperer_page($css);
		if (!$contenu) return $css;
	}
	else {
		if ((@filemtime($f) > @filemtime($css))
			AND ($GLOBALS['var_mode'] != 'recalcul'))
			return $f;
		if (!lire_fichier($css, $contenu))
			return $css;
	}

	$contenu = css_imbriques_decouper ($contenu);
	

	// passer les url relatives a la css d'origine en url absolues
	$contenu = preg_replace(",url\s*\(\s*['\"]?([^'\"/][^:]*)['\"]?\s*\),UimsS",
		"url($path\\1)",$contenu);



	// virer les fausses url absolues que l'on a mis dans les import
	if (count($src_faux_abs))
		$contenu = str_replace(array_keys($src_faux_abs),$src_faux_abs,$contenu);

	ecrire_fichier ("$f.gz", $contenu, true);
	if (!ecrire_fichier($f, $contenu))
		return $css;

	return $f;
}

?>

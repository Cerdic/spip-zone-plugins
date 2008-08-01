<?php

function css_imbriques_enfants($compteur, $ajouter, $classe, $enfants) {
//	echo "<div><b>$compteur : ".$classe[$compteur]."</b></div><ul>";
	foreach($enfants[$compteur] AS $enfant) {
//		echo "<li>$compteur -> $enfant</li>";
		if (ereg("^\.\ ",$classe[$enfant])) {
			$classe[$enfant] = trim($ajouter.ereg_replace("^\.\ ", "", $classe[$enfant]));			
		} else {
			$classe[$enfant] = trim($ajouter." ".$classe[$enfant]);
		}
		$classe = css_imbriques_enfants($enfant, $ajouter, $classe, $enfants);
	}
	
	
//	echo "</ul>";


	return $classe;
}


function css_imbriques_decouper ($css) {
	
	$css = ereg_replace("\n[\t\ ]*", "\n", trim($css));
	$css = ereg_replace("\n+", "\n", $css);

	// Virer les commentaires (source d'erreurs, et on ne sait plus ou les placer puisqu'on reorganise la bazar)
	$css = preg_replace('#(/\*[^*]*\*+([^/*][^*]*\*+)*/)#', '', $css);

	
	while (preg_match_all ("/([^\{\n]*)\{([^\{]*)\}/U", $css, $regs)) {
		foreach ($regs[0] as $num=>$l) {
		
			$compteur ++;
		
			$classe[$compteur] = trim($regs[1][$num]);
			$definition[$compteur] = trim($regs[2][$num]);
			
			preg_match_all("/\[\[([0-9]*)\]\]/", $definition[$compteur], $sous);
			foreach($sous[1] as $enfant) {
				$enfants[$compteur][] = $enfant;
			}
			$classe = css_imbriques_enfants($compteur, $classe[$compteur], $classe, $enfants);

			$definition[$compteur] = ereg_replace("[\ \n]*\[\[[0-9]*\]\][\ \n]*", "", $definition[$compteur]);

			$css = str_replace($regs[0][$num], "[[$compteur]]", $css);
			
		}
		
	}


	$css = "";
	foreach($classe as $num=>$nom) {
		$def= $definition[$num];
		if (strlen($def) > 0) {
			$css .= "$nom {\n";
			$def = "\t".ereg_replace("\n", "\n\t", $def);
			$css .= $def;
			$css .= "\n}\n";
		}
	}


	return $css;
//	echo "<hr>$css<hr>";

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

	/*

	// reperer les @import auxquels il faut propager le direction_css
	preg_match_all(",\@import\s*url\s*\(\s*['\"]?([^'\"/][^:]*)['\"]?\s*\),Uims",$contenu,$regs);
	$src = array();$src_direction_css = array();$src_faux_abs=array();
	$d = dirname($css);
	foreach($regs[1] as $k=>$import_css){
		$css_direction = direction_css("$d/$import_css",$voulue);
		// si la css_direction est dans le meme path que la css d'origine, on tronque le path, elle sera passee en absolue
		if (substr($css_direction,0,strlen($d)+1)=="$d/") $css_direction = substr($css_direction,strlen($d)+1);
		// si la css_direction commence par $dir_var on la fait passer pour une absolue
		elseif (substr($css_direction,0,strlen($dir_var))==$dir_var) {
			$css_direction = substr($css_direction,strlen($dir_var));
			$src_faux_abs["/@@@@@@/".$css_direction] = $css_direction;
			$css_direction = "/@@@@@@/".$css_direction;
		}
		$src[] = $regs[0][$k];
		$src_direction_css[] = str_replace($import_css,$css_direction,$regs[0][$k]);
	}
	$contenu = str_replace($src,$src_direction_css,$contenu);
	*/
	// passer les url relatives a la css d'origine en url absolues
	$contenu = preg_replace(",url\s*\(\s*['\"]?([^'\"/][^:]*)['\"]?\s*\),UimsS",
		"url($path\\1)",$contenu);
	// virer les fausses url absolues que l'on a mis dans les import
	if (count($src_faux_abs))
		$contenu = str_replace(array_keys($src_faux_abs),$src_faux_abs,$contenu);

	if (!ecrire_fichier($f, $contenu))
		return $css;

	return $f;
}

?>
<?php

function image_responsive_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("image_responsive.css")."'>\n";

	return $flux;
}

function image_responsive_insert_head($flux) {
	$type_urls = lire_meta("type_urls");
	$htactif = 0;
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = 1;
	}
	
	$flux .= "<script>htactif=$htactif;document.createElement('picture');</script>";
	$flux .= "
<script type='text/javascript' src='".find_in_path("javascript/jquery.smartresize.js")."'></script>
<script type='text/javascript' src='".find_in_path("javascript/image_responsive.js")."'></script>
<script type='text/javascript' src='".find_in_path("javascript/picturefill.js")."'></script>
		";
	
	return $flux;
}

function image_responsive_header_prive($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("image_responsive.css")."'>\n";
	$flux .= "<script>htactif=false;document.createElement('picture');</script>";

	$flux .= "
<script type='text/javascript' src='".find_in_path("javascript/jquery.smartresize.js")."'></script>
<script type='text/javascript' src='".find_in_path("javascript/image_responsive.js")."'></script>
<script type='text/javascript' src='".find_in_path("javascript/picturefill.js")."'></script>
		";

	return $flux;
}


function _image_responsive($img, $taille=-1, $lazy=0, $vertical = 0, $medias="", $proportions="") {
	$taille_defaut = -1;
	
	if ($taille == -1) {
		$taille_defaut = 120;
		$taille = "";	
	}

	if (preg_match(",^0$|^0\/,", $taille)) {
		$taille_defaut = 0;
		$taille = preg_replace(",^0$|^0\/,", "", $taille);
	}


	$tailles = explode("/", $taille);
	
	if ($taille_defaut < 0) {
		if (count($tailles) > 0) $taille_defaut = $tailles[0];
		else $taille_defaut = $taille;
	}

//	$img = $img[0];
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	$source = extraire_attribut($img, "src");
	$source = preg_replace(",\?[0-9]*$,", "", $source);
	if (file_exists($source)) {
		$l = largeur($source);
		$h = hauteur($source);

		$img = vider_attribut($img, "width");
		$img = vider_attribut($img, "height");
		$img = vider_attribut($img, "style");


		// Récupérer les proportions et éventuellement recadrer
		$proportions = explode("/", $proportions);
		if (count($proportions) > 0) {
			$i = 0;
			foreach($proportions as $prop) {
				$i++;
				$prop = trim ($prop);
				$regs_l = false;
				$regs_h = false;
				if (preg_match(",^([0-9\.]+\%?)(x([0-9\.]+\%?))?(x([a-z]*))?(x([0-9\.]*))?$,", $prop, $regs)) {
				
					if ($regs[1] == "0") $regs[1] = $l;
					if ($regs[3] == "0") $regs[3] = $h;
				
					$p[$i]["l"] = $regs[1];
					$p[$i]["h"] = $regs[3];
					$p[$i]["f"] = $regs[5];
					$p[$i]["z"] = $regs[7];

					// Gérer les dimensions en pourcentages
					preg_match(",([0-9\.]+)\%$,", $regs[1],$regs_l);
					preg_match(",([0-9\.]+)\%$,", $regs[3],$regs_h);
					
					if ($regs_l[1]>0 OR $regs_h[1]>0) {
						if ($regs_l[1] > 0) $p[$i]["l"] = $l * $regs_l[1] / 100;
						else $p[$i]["l"] = $l;
						if ($regs_h[1] > 0) $p[$i]["h"] = $h * $regs_h[1] / 100;
						else $p[$i]["h"] = $h;
					}

					
					if (!$regs[5]) $p[$i]["f"] = "center";
					if (!$regs[7]) $p[$i]["z"] = 1;
				}
			}
		}
		if (count($p) == 1) {
			$source = image_proportions($source, $p[1]["l"], $p[1]["h"], $p[1]["f"], $p[1]["z"]);
			$source = extraire_attribut($source,"src");
		}

	
		//$img = inserer_attribut($img, "src", $src);
		$img = inserer_attribut($img, "data-src", $source);
		$classe = "image_responsive";
		
		if ($vertical == 1) {
			$classe .= " image_responsive_v";
			$v = "v";	
			if ($h < $taille_defaut) $taille_defaut = $h;
		} else {
			$v = "";
			if ($l < $taille_defaut) $taille_defaut = $l;
		}
		
		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $source);
		}
		else {
			$src = "index.php?action=image_responsive&amp;img=$source&amp;taille=$taille_defaut$v";
		}
		
		if ($taille_defaut == 0) $src = "rien.gif";
		if ($lazy == 1) $classe .= " lazy";
		$img = inserer_attribut($img, "data-l", $l);
		$img = inserer_attribut($img, "data-h", $h);
		
		// Gérer les tailles autorisées
		if (count($tailles) > 0) {
			sort($tailles);
			include_spip("inc/json");
			
			$img = inserer_attribut($img, "data-tailles", addslashes(json_encode($tailles)));


			// Fabriquer automatiquement un srcset s'il n'y a qu'une seule taille d'image (pour 1x et 2x)
			if (count($tailles) == 1 && $lazy != 1) { // Pas de srcset sur les images lazy
					$t = $tailles[0];
					if ($t != 0 && $t <= $l) {
					
						if ($htactif) {
							$srcset[] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v.$1", $source)." 1x";
							$srcset[] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v-2.$1", $source)." 2x";
						}
						else {
							$srcset[] = "index.php?action=image_responsive&amp;img=$source&amp;taille=$t$v 1x";
							$srcset[] = "index.php?action=image_responsive&amp;img=$source&amp;taille=$t$v&amp;dpr=2 2x";
						}
					}
			}

			
			// Fabriquer des <source> s'il y a plus d'une taille associée à des sizes
			if (count($tailles) > 1 && $lazy != 1) {
				$medias = explode("/", $medias);
				if (count($tailles) == count($medias)) {
					$i = 0;
					foreach($tailles as $t) {
						$m = trim($medias[$i]);
						$i++;
						
						$source_tmp = $source;
						
						if (count($p) > 1 && count($p[$i]) > 1) {
							$source_tmp = image_proportions($source_tmp, $p[$i]["l"], $p[$i]["h"], $p[$i]["f"], $p[$i]["z"]);
							$source_tmp = extraire_attribut($source_tmp,"src");
							
							$pad_bot_styles[$m] = "padding-bottom:" .(($p[$i]["h"]/$p[$i]["l"])*100)."%!important";
							
						}

						if ($htactif) {
							$set = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v.$1", $source_tmp)." 1x";
							$set .= ",".preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v-2.$1", $source_tmp)." 2x";
						}
						else {
							$set = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v 1x";
							$set .= ","."index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;dpr=2 2x";
						}
						
						if (strlen($m) > 0) {
							$insm = " media='$m'";
							$sources .= "<source$insm srcset='$set'>";
						}
						else {
							$src = find_in_path("rien.gif");
							$srcset[] = $set;
						}
						

					}
				
				}
			}
		}

		// Gérer le srcset
		if ($sources || $srcset) $classe .= " avec_picturefill";

		$img = inserer_attribut($img, "src", $src);
		
		$img = inserer_attribut($img, "class", $classe);
		if ($srcset) {
			$srcset = join($srcset, ",");				
			$img = inserer_attribut($img, "srcset", $srcset);
		}
		
		if ($sources) {
			$sources = "<!--[if IE 9]><video style='display: none;'><![endif]-->$sources<!--[if IE 9]></video><![endif]-->";
		}
		

		if ($pad_bot_styles) {
			foreach($pad_bot_styles as $m=>$pad) {
				$style = "##classe##{".$pad."}";
				if ($m) $style = "\n@media $m {".$style."}";
				$styles .= $style;
			}
			$styles = "<style>$styles</style>";
			$nom_class = "class".md5($styles);
			$styles = str_replace("##classe##", "picture.".$nom_class, $styles);
			// pour affichage dans la classe de picture
			$nom_class = " ".$nom_class; 
		}
		
		if ($vertical == 0) {
			if (count($p) == 1) $r = ($p[1]["h"]/$p[1]["l"]) * 100;
			else if (count($p) == 0) $r = (($h/$l)*100);
			
			if ($r) $aff_r = "padding-bottom:$r%";
			$img = "<picture style='padding:0;$aff_r' class='conteneur_image_responsive_h$nom_class'>$sources$img</picture>";
		} else {
			$r = (($h/$l)*100);
			$img = "<picture class='conteneur_image_responsive_v$nom_class'>$sources$img</picture>";
		}
		$img = $img.$styles;
	}
	
	return $img;
}




function image_responsive($texte, $taille=-1, $lazy=0, $vertical=0, $medias='', $proportions='') {
	if (!preg_match("/^<img /i", $texte)) {
		if (strlen($texte) < 256 && file_exists($texte)) $texte = "<img src='$texte'>";
		else return $texte;
	}
	return preg_replace_callback(",(<img\ [^>]*>),", create_function('$matches', 'return _image_responsive($matches[0],"'.$taille.'",'.$lazy.','.$vertical.',"'.$medias.'","'.$proportions.'");'), $texte);
}




function background_responsive($src, $taille=120, $lazy=0) {

	if (preg_match("/^<img /i", $src)) {
		$src = extraire_attribut($src, "src");
	}

	
		
	$tailles = explode("/", $taille);
	if (count($tailles) > 1) $taille_defaut = $tailles[0];
	else $taille_defaut = $taille;
	
//	$img = $img[0];
	$type_urls = lire_meta("type_urls");
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {	
		$htactif = true;
	}
	$src = preg_replace(",\?[0-9]*$,", "", $src);
		
	if (file_exists($src)) {
		include_spip("filtres/images_transforme");


		$l = largeur($src);
		$h = hauteur($src);
	
		
		//$img = inserer_attribut($img, "src", $src);
		
		if ($l > $h) {
			$ins = "data-italien-src='$src'";
			$ins .= " data-italien-l='$l'";
			$ins .= " data-italien-h='$h'";
			
			$srcp = image_reduire($src, 0, 2400);
			$srcp = image_proportions($srcp, 3, 4);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);
			
			$ins .= "data-portrait-src='$srcp'";
			$ins .= " data-portrait-l='$lp'";
			$ins .= " data-portrait-h='$hp'";
		
		} else {
			$ins = "data-portrait-src='$src'";
			$ins .= " data-portrait-l='$l'";
			$ins .= " data-portrait-h='$h'";


			$srcp = image_reduire($src, 2400, 0);
			$srcp = image_proportions($srcp, 4, 3);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);
			
			$ins .= " data-italien-src='$srcp'";
			$ins .= " data-italien-l='$lp'";
			$ins .= " data-italien-h='$hp'";
		}


		$ins .= " data-responsive='background'";
		

		if ($l < $taille_defaut) $taille_defaut = $l;
		$v = "";
		
		
		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $src);
		}
		else {
			$src = "index.php?action=image_responsive&amp;img=$src&amp;taille=$taille_defaut$v";
		}
		
		
		if ($taille_defaut == 0) $src = "rien.gif";
		if ($lazy == 1) $ins .= " data-lazy='lazy'";

		$ins .= " class='$class'";
		
		if (count($tailles) > 1) {
			sort($tailles);
			include_spip("inc/json");
			
			$ins .= " data-tailles='".addslashes(json_encode($tailles)) ."'";
		}
		
		$ins .= " style='background-image:url($src)'";
		
		return $ins;
	}
	

}


function image_proportions($img, $largeur=16, $hauteur=9, $align="center", $zoom=1) {
	$mode = $align;
	
	if (!$img) return;
	
	
	
	$l_img = largeur ($img);
	$h_img = hauteur($img);

	if ($largeur == 0 OR $hauteur == 0) {
		$largeur = $l_img;
		$hauteur = $h_img;
		
	}

	
	if ($l_img == 0 OR $h_img == 0) return $img;
	
	$r_img = $h_img / $l_img;	
	$r = $hauteur / $largeur;	
	
	if ($r_img < $r) {
		$l_dest = $h_img/$r;
		$h_dest = $h_img;
	} else if ($r_img > $r) {
		$l_dest = $l_img;
		$h_dest = $l_img*$r;
	}
	
	
	// Si align est "focus", on va aller chercher le «point d'intérêt» de l'image 
	// avec la fonction centre_image du plugin «centre_image»
	if ($align == "focus" && function_exists(centre_image)) {
		$dx = centre_image_x($img);
		$dy = centre_image_y($img);

		if ($r_img > $r) {
			$h_dest = round(($l_img * $r)/$zoom);
			$l_dest = round($l_img/$zoom);
		} else {
			$h_dest = round($h_img/$zoom);
			$l_dest = round(($h_img / $r)/$zoom);
		}
			$h_centre = $h_img * $dy;
			$l_centre = $l_img * $dx;
			$top = round($h_centre - ($h_dest/2));
			$l_centre = $l_img * $dx;
			$left = round($l_centre - ($l_dest/2));
			
			if ($top < 0) $top = 0;
			if ($top + $h_dest > $h_img ) $top = $h_img - $h_dest;
			if ($left < 0) $left = 0;
			if ($left + $l_dest > $l_img ) $left = $l_img - $l_dest;
			
			//echo "$dy - $l_img x $h_img - $h_dest x $l_dest - $h_centre x $l_centre - $top x $left"; 
			$align = "top=$top, left=$left";
	}

	include_spip("filtres/images_transforme");
	$img = image_recadre($img, $l_dest, $h_dest, $align);
	
	// Second passage si $zoom (on verra plus tard si c'est intéressant de le traiter en amont)
	if ($zoom > 1 && $mode != "focus") {
		$l_img = largeur ($img)/2;
		$h_img = hauteur($img)/2;
		
		$img = image_recadre($img, $l_img, $h_img);
		
	}
	
	return $img;
}


function image_responsive_affiche_milieu($flux, $effacer=false) {

	$exec = $flux["args"]["exec"];
	
	
	if ($exec == "admin_vider") {
		$retour = recuperer_fond("squelettes/admin_vider_responsive");

		$flux["data"] .= $retour;
	}

	return $flux;
}



?>

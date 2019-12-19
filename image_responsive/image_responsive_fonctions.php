<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function image_responsive_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='" . find_in_path("image_responsive.css") . "'>\n";
	return $flux;
}

/**
 * Indique si le fichier htaccess est actif
 *
 * Le fichier htaccess est considéré actif si un jeu d'URL de SPIP
 * qui nécessite la présente du fichier htaccess est utilisé
 * (arbo, libre, propre, etc). Dans ce cas le fichier
 * htaccess fourni par SPIP doit être complété par les instructions
 * présentes dans `ajouter_a_htaccess.txt` de ce plugin.
 *
 * Il est possible de forcer l'état (par exemple si on utilise
 * un jeu d'URL spécifique (et que l'on a bien modifié le htaccess),
 * en déclarant la constante `_IMAGE_RESPONSIVE_HTACCESS` à 1.
 *
 * @return int. 1 si le htaccess est utilisable. 0 sinon.
 */
function image_responsive_htaccess_actif() {
	if (defined('_IMAGE_RESPONSIVE_HTACCESS')) {
		return _IMAGE_RESPONSIVE_HTACCESS;
	}
	$type_urls = lire_meta("type_urls");
	$htactif = 0;
	if (preg_match(",^(arbo|libres|html|propres|propres2)$,", $type_urls)) {
		$htactif = 1;
	}
	return $htactif;
}

function image_responsive_insert_head($flux) {
	$htactif = image_responsive_htaccess_actif();
	$flux .= "<script>htactif=$htactif;document.createElement('picture'); var image_responsive_retina_hq = 0;</script>";
	if (_IMAGE_RESPONSIVE_RETINA_HQ) $flux .= "<script>image_responsive_retina_hq = 1;</script>";
	$flux .= "
<script type='text/javascript' src='" . find_in_path("javascript/rAF.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/jquery.smartresize.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/image_responsive.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/picturefill.js") . "'></script>
		";

	return $flux;
}

function image_responsive_header_prive($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='" . find_in_path("image_responsive.css") . "'>\n";
	$flux .= "<script>htactif=false;document.createElement('picture');</script>";
	if (_IMAGE_RESPONSIVE_RETINA_HQ) $flux .= "<script>image_responsive_retina_hq = 1;</script>";
	else 	$flux .= "<script>var image_responsive_retina_hq = 0;</script>";


	$flux .= "
<script type='text/javascript' src='" . find_in_path("javascript/rAF.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/jquery.smartresize.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/image_responsive.js") . "'></script>
<script type='text/javascript' src='" . find_in_path("javascript/picturefill.js") . "'></script>
		";

	return $flux;
}


function _image_responsive($img, $taille = -1, $lazy = 0, $vertical = 0, $medias = "", $proportions = "") {
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
	$htactif = (bool)image_responsive_htaccess_actif();
	$source = extraire_attribut($img, "src");
	$source = preg_replace(",\?[0-9]*$,", "", $source);
	
	
	if (file_exists($source)) {
		$l = largeur($source);
		$h = hauteur($source);
		
		// mime_content_type ne fonctionn pas sur certaines versions de PHP…
		// $mime = mime_content_type($source);
		if (preg_match("/png$/", $source)) $mime = "image/png";
		else if (preg_match("/gif$/", $source)) $mime = "image/gif";
		$mime = "image/jpeg";
		
		$timestamp = filemtime($source);

		$img = vider_attribut($img, "width");
		$img = vider_attribut($img, "height");
		$img = vider_attribut($img, "style");

		$alt = extraire_attribut($img, "alt");
		if (strlen($alt) == 0) $img = inserer_attribut($img, "alt", "");


		// Récupérer les proportions et éventuellement recadrer
		$proportions = explode("/", $proportions);
		$p = array();
		if (count($proportions) > 0) {
			$i = 0;
			foreach ($proportions as $prop) {
				$i++;
				$prop = trim($prop);
				$regs_l = false;
				$regs_h = false;
				if (preg_match(",^([0-9\.]+\%?)(x([0-9\.]+\%?))?(x([a-z]*))?(x([0-9\.]*))?$,", $prop, $regs)) {

					if ($regs[1] == "0") $p[$i]["l"] = $l;
					else $p[$i]["l"] = $regs[1];

					if ($regs[3] == "0") $p[$i]["h"] = $h;
					else $p[$i]["h"] = $regs[3];


					$p[$i]["f"] = $regs[5];
					$p[$i]["z"] = $regs[7];

					// Gérer les dimensions en pourcentages
					preg_match(",([0-9\.]+)\%$,", $regs[1], $regs_l);
					preg_match(",([0-9\.]+)\%$,", $regs[3], $regs_h);

					if ($regs_l[1] > 0 OR $regs_h[1] > 0) {
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
			$source = extraire_attribut($source, "src");
		}

		$medias = explode("/", $medias);
		$pad_bot_styles = array();

		if (count($p) > 1) {
			$i = 0;
			foreach ($tailles as $t) {
				$m = trim($medias[$i]);
				$i++;
				if (count($p[$i]) > 1) {
					$pad_bot_styles[$m] = "padding-bottom:" . (($p[$i]["h"] / $p[$i]["l"]) * 100) . "%!important";
				}
			}
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

		if ($taille_defaut == 0) {
			$src = find_in_path("rien.gif");
		} else {
			if (_IMAGE_RESPONSIVE_CALCULER) {
				$src = retour_image_responsive($source, $taille_defaut, 1, 0, "file");
			} else {
				if ($htactif) {
					$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1?$timestamp", $source);
				} else {
					$src = "index.php?action=image_responsive&amp;img=$source&amp;taille=$taille_defaut$v&amp;$timestamp";
				}
			}
		}

		if ($lazy == 1) $classe .= " lazy";
		$img = inserer_attribut($img, "data-l", $l);
		$img = inserer_attribut($img, "data-h", $h);
		$sources = '';
		$autorisees = array();

		// Gérer les tailles autorisées
		if (count($tailles) > 0) {
			include_spip("inc/json");

			$img = inserer_attribut($img, "data-tailles", addslashes(json_encode($tailles)));


			$i = 0;

			foreach ($tailles as $t) {
				$m = trim($medias[$i]);
				$i++;
				$source_tmp = $source;

				if (count($p) > 1 && count($p[$i]) > 1) {
					if ($p[$i]["l"] != $l || $p[$i]["h"] != $h) { // Pas de redimension si on est au format source (0x0)
						$source_tmp = image_proportions($source_tmp, $p[$i]["l"], $p[$i]["h"], $p[$i]["f"], $p[$i]["z"]);
						$source_tmp = extraire_attribut($source_tmp, "src");
					}
				}
				if ($vertical && $t > $h) $t = $h;
				else if (!$vertical && $t > $l) $t = $l;
				if (_IMAGE_RESPONSIVE_RETINA_HQ) {
					$t2  = $t*2;
					if ($vertical && $t2 > $h) $t2 = $h;
					else if (!$vertical && $t2 > $l) $t2 = $l;
				
				}

				if (_IMAGE_RESPONSIVE_CALCULER) {
					$fichiers[$i][1] = retour_image_responsive($source_tmp, "$t$v", 1, 0, "file");
					if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers[$i][2] = retour_image_responsive($source_tmp, "$t2$v", 1, 0, "file");
					else  $fichiers[$i][2] = retour_image_responsive($source_tmp, "$t$v", 2, 0, "file");
					
					if (_IMAGE_WEBP) {
						$fichiers_webp[$i][1] = retour_image_responsive($source_tmp, "$t$v", 1, 0, "file", "webp");
						if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers_webp[$i][2] = retour_image_responsive($source_tmp, "$t2$v", 1, 0, "file", "webp");
						else  $fichiers_webp[$i][2] = retour_image_responsive($source_tmp, "$t$v", 2, 0, "file", "webp");
					}
				} else {
					if ($htactif) {
						$fichiers[$i][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v.$1?$timestamp", $source_tmp);
						if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers[$i][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t2$v.$1?$timestamp", $source_tmp);
						else $fichiers[$i][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v-2.$1?$timestamp", $source_tmp);

						if (_IMAGE_WEBP) {
							$fichiers_webp[$i][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v.$1.webp?$timestamp", $source_tmp);
							if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers_webp[$i][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t2$v.$1.webp?$timestamp", $source_tmp);
							else $fichiers_webp[$i][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v-2.$1.webp?$timestamp", $source_tmp);
						}
					} else {
						$fichiers[$i][1] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;$timestamp";
						if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers[$i][2] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t2$v&amp;$timestamp";
						else $fichiers[$i][2] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;dpr=2&amp;$timestamp";

						if (_IMAGE_WEBP) {
							$fichiers_webp[$i][1] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;format=webp&amp;$timestamp&";
							if (_IMAGE_RESPONSIVE_RETINA_HQ) $fichiers_webp[$i][2] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t2$v&amp;format=webp&amp;$timestamp";
							else $fichiers_webp[$i][2] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;dpr=2&amp;format=webp&amp;$timestamp";
						}
					}
				}

			}
			// Fabriquer automatiquement un srcset s'il n'y a qu'une seule taille d'image (pour 1x et 2x)
			if (count($tailles) == 1 && $lazy != 1) { // Pas de srcset sur les images lazy
				$t = $tailles[0];
				if ($t != 0 ) {
						if (_IMAGE_WEBP) {
							$set = $fichiers[$i][1] . " 1x";
							$set .= "," . $fichiers[$i][2] . " 2x";
							$set_webp = $fichiers_webp[$i][1] . " 1x";
							$set_webp .= "," . $fichiers_webp[$i][2] . " 2x";
							$sources .= "<source srcset='$set_webp' type='image/webp'>";
							$sources .= "<source srcset='$set' type='$mime'>";
							
						} else {
							$srcset[] = $fichiers[1][1] . " 1x";
							$srcset[] = $fichiers[1][2] . " 2x";
						}

				}
			}


			// Fabriquer des <source> s'il y a plus d'une taille associée à des sizes
			if (count($tailles) > 1) {
				if (count($tailles) == count($medias) && $lazy != 1) {
					$i = 0;
					foreach ($tailles as $t) {
						$m = trim($medias[$i]);
						$i++;

						if ($vertical) $t = min($t, $h);
						else $t = min($t, $l);


						$source_tmp = $source;
						$set = $fichiers[$i][1] . " 1x";
						$set .= "," . $fichiers[$i][2] . " 2x";
						if (_IMAGE_WEBP) {
							$set_webp = $fichiers_webp[$i][1] . " 1x";
							$set_webp .= "," . $fichiers_webp[$i][2] . " 2x";
						}

						if (strlen($m) > 0) {
							$insm = " media='$m'";

							if (_IMAGE_WEBP) {
								$sources .= "<source$insm srcset='$set_webp' type='image/webp'>";
							}
							$sources .= "<source$insm srcset='$set' type='$mime'>";
							
							
							
						} else {
							if (_IMAGE_WEBP) {
								$sources .= "<source srcset='$set_webp' type='image/webp'>";
								$sources .= "<source srcset='$set' type='$mime'>";
							} else {
								$srcset[] = $set;
							}
						}


					}
				} else {
					// Tailles déterminées, pas de @media
					// dans le cas où l'on force précalcule
					$i = 0;
					foreach ($tailles as $t) {
						$i++;
						if ($vertical && $t > $h) $t = $h;
						else if (!$vertical && $t > $l) $t = $l;


						$autorisees[$t][1] = $fichiers[$i][1];
						$autorisees[$t][2] = $fichiers[$i][2];
						if (_IMAGE_WEBP) {
							$autorisees_webp[$t][1] = $fichiers_webp[$i][1];
							$autorisees_webp[$t][2] = $fichiers_webp[$i][2];
						}
					}
				}
			}
		}


		// Gérer le srcset
		if ($sources || $srcset) $classe .= " avec_picturefill";

		if ($autorisees) {
			$autorisees = json_encode($autorisees);
			$img = inserer_attribut($img, "data-autorisees", $autorisees);
			if (_IMAGE_WEBP) {
				$autorisees_webp = json_encode($autorisees_webp);
				$img = inserer_attribut($img, "data-autorisees_webp", $autorisees_webp);
			}
		}


		$img = inserer_attribut($img, "src", $src);
		if ($lazy) $img = inserer_attribut($img, "data-src-lazy", $src);

		$img = inserer_attribut($img, "class", $classe);
		if ($srcset) {
			$srcset = join($srcset, ",");
			$img = inserer_attribut($img, "srcset", $srcset);
		}

		if ($sources) {
			$sources = "<!--[if IE 9]><video style='display: none;'><![endif]-->$sources<!--[if IE 9]></video><![endif]-->";
		}

		$styles = $nom_class = '';
		if ($pad_bot_styles) {

			ksort($pad_bot_styles);

			foreach ($pad_bot_styles as $m => $pad) {
				$style = "##classe##{" . $pad . "}";
				if ($m) $style = "\n@media $m {" . $style . "}";
				$styles .= $style;
			}
			$styles = "<style>$styles</style>";
			$nom_class = "class" . md5($styles);
			$styles = str_replace("##classe##", "picture." . $nom_class, $styles);
			// pour affichage dans la classe de picture
			$nom_class = " " . $nom_class;
		}
		$r = false;
		
		if ($vertical == 0) {
			if (count($p) == 1 && $p[1]["l"]>0) $r = ($p[1]["h"] / $p[1]["l"]) * 100;
			else if (count($p) == 0 && $l > 0) $r = (($h / $l) * 100);
			if ($r) {
				$aff_r = "padding-bottom:$r%";
			}
				$img = "<picture style='padding:0;$aff_r' class='conteneur_image_responsive_h$nom_class'>$sources$img</picture>";
		} else {
			if ($l > 0) {
				$r = (($h / $l) * 100);
			}
				$img = "<picture class='conteneur_image_responsive_v$nom_class'>$sources$img</picture>";
		}
		$img = $img . $styles;
	}

	if (_SPIP_LIER_RESSOURCES && $fichiers) {
		foreach ($fichiers as $f) {
			$img .= "\n<link href='" . $f[1] . "' rel='attachment' property='url'>"
				. "\n<link href='" . $f[2] . "' rel='attachment' property='url'>";
		}
		foreach ($fichiers_webp as $f) {
			$img .= "\n<link href='" . $f[1] . "' rel='attachment' property='url'>"
				. "\n<link href='" . $f[2] . "' rel='attachment' property='url'>";
		}
	}


	return $img;
}


function image_responsive($texte, $taille = -1, $lazy = 0, $vertical = 0, $medias = '', $proportions = '') {
	if (!preg_match("/^<img/i", $texte)) {
		if (strlen($texte) < 256 && file_exists($texte)) $texte = "<img src='$texte'>";
		else return $texte;
	}

	if (defined('PHP_VERSION_ID') and PHP_VERSION_ID > 50300) {
		
		/* ATTENTION, cette version plante les scripts en PHP 5.2.6. Page blanche. 
		return preg_replace_callback(
			",(<img\ [^>]*>),",
			function($matches) use ($taille, $lazy, $vertical, $medias, $proportions) {
				return _image_responsive($matches[0], $taille, $lazy, $vertical, $medias, $proportions);
			},
			$texte
		);*/
		return preg_replace_callback(
			",(<img[^>]*>),",
			create_function('$matches',
				'return _image_responsive($matches[0],"' . $taille . '",' . $lazy . ',' . $vertical . ',"' . $medias . '","' . $proportions . '");'
			),
			$texte
		);

	} else {
		return preg_replace_callback(
			",(<img[^>]*>),",
			create_function('$matches',
				'return _image_responsive($matches[0],"' . $taille . '",' . $lazy . ',' . $vertical . ',"' . $medias . '","' . $proportions . '");'
			),
			$texte
		);
	}
}


function image_responsive_svg($img, $taille = -1, $lazy = 0, $vertical = 0) {
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

	$htactif = (bool)image_responsive_htaccess_actif();

	if (preg_match("/^<img/i", $img)) {
		$img = extraire_attribut($img, "src");
	}
	$img = preg_replace(",\?[0-9]*$,", "", $img);

	$source = $img;

	$classe = "image_responsive_svg";


	if (file_exists($source)) {
		$l = largeur($source);
		$h = hauteur($source);
		if ($vertical == 1) {
			$classe .= " image_responsive_svg_v";
			$v = "v";
			if ($h < $taille_defaut) $taille_defaut = $h;
		} else {
			$v = "";
			if ($l < $taille_defaut) $taille_defaut = $l;
		}
		if ($taille_defaut == 0) {
			$src = find_in_path("rien.gif");
		} else {
			if (_IMAGE_RESPONSIVE_CALCULER) {
				$src = retour_image_responsive($source, $taille_defaut, 1, 0, "file");
			} else {
				if ($htactif) {
					$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $source);
				} else {
					$src = "index.php?action=image_responsive&amp;img=$source&amp;taille=$taille_defaut$v";
				}
			}
		}

		if ($lazy == 1) $classe .= " lazy";

		if (count($tailles) > 0) {
			sort($tailles);
			include_spip("inc/json");

			$data_tailles = " data-tailles='" . addslashes(json_encode($tailles)) . "'";


			$i = 0;

			foreach ($tailles as $t) {
				$m = trim($medias[$i]);
				$i++;
				$source_tmp = $source;

				if (count($p) > 1 && count($p[$i]) > 1) {
					$source_tmp = image_proportions($source_tmp, $p[$i]["l"], $p[$i]["h"], $p[$i]["f"], $p[$i]["z"]);
					$source_tmp = extraire_attribut($source_tmp, "src");
				}

				if ($vertical && $t > $h) $t = $h;
				else if (!$vertical && $t > $l) $t = $l;


				if (_IMAGE_RESPONSIVE_CALCULER) {
					$fichiers[$t][1] = retour_image_responsive($source_tmp, "$t$v", 1, 0, "file");
					$fichiers[$t][2] = retour_image_responsive($source_tmp, "$t$v", 2, 0, "file");
				} else {
					if ($htactif) {
						$fichiers[$t][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v.$1", $source_tmp);
						$fichiers[$t][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t$v-2.$1", $source_tmp);
					} else {
						$fichiers[$t][1] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v";
						$fichiers[$t][2] = "index.php?action=image_responsive&amp;img=$source_tmp&amp;taille=$t$v&amp;dpr=2";
					}
				}

			}
			foreach ($tailles as $t) {
				if ($vertical && $t > $h) $t = $h;
				else if (!$vertical && $t > $l) $t = $l;


				$autorisees[$t][1] = $fichiers[$t][1];
				$autorisees[$t][2] = $fichiers[$t][2];
			}

			if ($autorisees) {
				$data_autorisees = " data-autorisees='" . json_encode($autorisees) . "'";
			}

		}

		$ret = "<image class='$classe' width='100%' height='100%' preserveAspectRatio='xMinYMin meet' xlink:href='$src' data-src='$source' data-l='$l' data-h='$h'$data_tailles$data_autorisees></image>";

	}


	return $ret;
}


function background_responsive($src, $taille = 120, $lazy = 0, $align = "") {

	if (preg_match("/^<img/i", $src)) {
		$src = extraire_attribut($src, "src");
	}


	$tailles = explode("/", $taille);
	if (count($tailles) > 1) $taille_defaut = $tailles[0];
	else $taille_defaut = $taille;

//	$img = $img[0];
	$htactif = (bool)image_responsive_htaccess_actif();
	$src = preg_replace(",\?[0-9]*$,", "", $src);

	if (file_exists($src)) {
		include_spip("filtres/images_transforme");

		$l = largeur($src);
		$h = hauteur($src);

		$mtime = filemtime($src);
		$ins .= " data-mtime='$mtime'";


		$img = $src;

		if ($l > $h) {
			$ins .= " data-italien-src='$src'";
			$ins .= " data-italien-l='$l'";
			$ins .= " data-italien-h='$h'";

			$srcp = image_proportions($srcp, 3, 4, $align);
			$srcp = image_reduire($src, 0, 2400);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);

			$ins .= " data-portrait-src='$srcp'";
			$ins .= " data-portrait-l='$lp'";
			$ins .= " data-portrait-h='$hp'";

			$l_italien = $l;
			$s_italien = $src;
			$l_portrait = $lp;
			$s_portrait = $srcp;

		} else {
			$ins .= " data-portrait-src='$src'";
			$ins .= " data-portrait-l='$l'";
			$ins .= " data-portrait-h='$h'";


			$srcp = image_proportions($srcp, 4, 3, $align);
			$srcp = image_reduire($src, 2400, 0);
			$srcp = extraire_attribut($srcp, "src");
			$lp = largeur($srcp);
			$hp = hauteur($srcp);

			$ins .= " data-italien-src='$srcp'";
			$ins .= " data-italien-l='$lp'";
			$ins .= " data-italien-h='$hp'";

			$l_italien = $lp;
			$s_italien = $srcp;
			$l_portrait = $l;
			$s_portrait = $src;

		}

		$ins .= " data-responsive='background'";


		if ($l < $taille_defaut) $taille_defaut = $l;
		$v = "";


		if ($htactif) {
			$src = preg_replace(",\.(jpg|png|gif)$,", "-resp$taille_defaut$v.$1", $src);
		} else {
			$src = "index.php?action=image_responsive&amp;img=$src&amp;taille=$taille_defaut$v";
		}


		if ($taille_defaut == 0) $src = find_in_path("rien.gif");
		if ($lazy == 1) $ins .= " data-lazy='lazy'";

		if ($class) $ins .= " class='$class'";

		if (count($tailles) > 1) {
			sort($tailles);
			include_spip("inc/json");


			foreach ($tailles as $t) {

				$t_italien = min($t, $l_italien);
				if (_IMAGE_RESPONSIVE_CALCULER) {
					$fichiers[$t_italien]["i"][1] = retour_image_responsive($s_italien, "$t_italien", 1, 0, "file");
					$fichiers[$t_italien]["i"][2] = retour_image_responsive($s_italien, "$t_italien", 2, 0, "file");
					if (_IMAGE_WEBP) {
						$fichiers_webp[$t_italien]["i"][1] = retour_image_responsive($s_italien, "$t_italien", 1, 0, "file", "webp");
						$fichiers_webp[$t_italien]["i"][2] = retour_image_responsive($s_italien, "$t_italien", 2, 0, "file", "webp");
					}
				} else {
					if ($htactif) {
						$fichiers[$t_italien]["i"][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_italien.$1", $s_italien) . "?$mtime";
						$fichiers[$t_italien]["i"][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_italien-2.$1", $s_italien) . "?$mtime";
						if (_IMAGE_WEBP) {
							$fichiers_webp[$t_italien]["i"][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_italien.$1.webp", $s_italien) . "?$mtime";
							$fichiers_webp[$t_italien]["i"][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_italien-2.$1.webp", $s_italien) . "?$mtime";
						}
					} else {
						$fichiers[$t_italien]["i"][1] = "index.php?action=image_responsive&amp;img=$s_italien&amp;taille=$t_italien&mtime=$mtime";
						$fichiers[$t_italien]["i"][2] = "index.php?action=image_responsive&amp;img=$s_italien&amp;taille=$t_italien&amp;dpr=2&mtime=$mtime";
						if (_IMAGE_WEBP) {
							$fichiers_webp[$t_italien]["i"][1] = "index.php?action=image_responsive&amp;img=$s_italien&amp;taille=$t_italien&amp;format=web&mtime=$mtime";
							$fichiers_webp[$t_italien]["i"][2] = "index.php?action=image_responsive&amp;img=$s_italien&amp;taille=$t_italien&amp;dpr=2&amp;format=web&mtime=$mtime";
						}
					}
				}

				$t_portrait = min($t, $l_portrait);
				if (_IMAGE_RESPONSIVE_CALCULER) {
					$fichiers[$t_portrait]["p"][1] = retour_image_responsive($s_portrait, "$t_portrait", 1, 0, "file");
					$fichiers[$t_portrait]["p"][2] = retour_image_responsive($s_portrait, "$t_portrait", 2, 0, "file");
					if (_IMAGE_WEBP) {
						$fichiers_webp[$t_italien]["p"][1] = retour_image_responsive($s_italien, "$t_portrait", 1, 0, "file", "webp");
						$fichiers_webp[$t_italien]["p"][2] = retour_image_responsive($s_italien, "$t_portrait", 2, 0, "file", "webp");
					}
				} else {
					if ($htactif) {
						$fichiers[$t_portrait]["p"][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_portrait.$1", $s_portrait) . "?$mtime";
						$fichiers[$t_portrait]["p"][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_portrait-2.$1", $s_portrait) . "?$mtime";
						if (_IMAGE_WEBP) {
							$fichiers_webp[$t_italien]["p"][1] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_portrait.$1.webp", $s_italien) . "?$mtime";
							$fichiers_webp[$t_italien]["p"][2] = preg_replace(",\.(jpg|png|gif)$,", "-resp$t_portrait-2.$1.webp", $s_italien) . "?$mtime";
						}
					} else {
						$fichiers[$t_portrait]["p"][1] = "index.php?action=image_responsive&amp;img=$s_portrait&amp;taille=$t_portrait&mtime=$mtime";
						$fichiers[$t_portrait]["p"][2] = "index.php?action=image_responsive&amp;img=$s_portrait&amp;taille=$t_portrait&amp;dpr=2&mtime=$mtime";
						if (_IMAGE_WEBP) {
							$fichiers_webp[$t_italien]["p"][1] = "index.php?action=image_responsive&amp;img=$s_portrait&amp;taille=$t_portrait&amp;format=web&mtime=$mtime";
							$fichiers_webp[$t_italien]["p"][2] = "index.php?action=image_responsive&amp;img=$s_portrait&amp;taille=$t_portrait&amp;dpr=2&amp;format=web&mtime=$mtime";
						}
					}
				}

			}

			$ins .= " data-tailles='" . addslashes(json_encode($tailles)) . "'";
			$ins .= " data-autorisees='" . addslashes(json_encode($fichiers)) . "'";
			if (_IMAGE_WEBP) {
				$ins .= " data-autorisees_webp='" . addslashes(json_encode($fichiers_webp)) . "'";
			}

			if (_SPIP_LIER_RESSOURCES && $fichiers) {
				foreach ($fichiers as $f) {
					$links .= "background-image:url(" . $f["i"][1] . ");"
						. "background-image:url(" . $f["i"][2] . ");"
						. "background-image:url(" . $f["p"][1] . ");"
						. "background-image:url(" . $f["p"][2] . ");";
				}
			}

			if ($align) {
				if ($align == "focus") {
					$style_align = "background-position: " . (centre_image_x($img) * 100) . "% " . (centre_image_y($img) * 100) . "%;";
				} else {
					$style_align = "background-position: $align;";
				}
			}

		}

		$ins .= " style='" . $style_align . $links . "background-image:url($src)'";

		return $ins;
	}


}


function image_proportions($img, $largeur = 16, $hauteur = 9, $align = "center", $zoom = 1) {
	$mode = $align;

	if (!$img) return;


	$l_img = largeur($img);
	$h_img = hauteur($img);

	if ($largeur == 0 OR $hauteur == 0) {
		$largeur = $l_img;
		$hauteur = $h_img;
	}


	if ($l_img == 0 OR $h_img == 0) return $img;

	$r_img = $h_img / $l_img;
	$r = $hauteur / $largeur;

	if ($r_img < $r) {
		$l_dest = $h_img / $r;
		$h_dest = $h_img;
	} else if ($r_img > $r) {
		$l_dest = $l_img;
		$h_dest = $l_img * $r;
	}


	// Si align est "focus" ou "focus-center", on va aller chercher le «point d'intérêt» de l'image 
	// avec la fonction centre_image du plugin «centre_image»

	// Avec "focus", point d'intérêt reste décentré
	// Avec "focus-center", point d'intérêt aussi centré que possible
	if (($align == "focus" || $align == "focus-center") && function_exists('centre_image')) {
		$dx = centre_image_x($img);
		$dy = centre_image_y($img);

		if ($r_img > $r) {
			$h_dest = round(($l_img * $r) / $zoom);
			$l_dest = round($l_img / $zoom);
		} else {
			$h_dest = round($h_img / $zoom);
			$l_dest = round(($h_img / $r) / $zoom);
		}
		$h_centre = $h_img * $dy;
		$l_centre = $l_img * $dx;
		if ($align == "focus-center") $top = round($h_centre - ($h_dest * 0.5));
		// ici on n'applique pas *$dy directement, car effet exagéré,
		// alors on pondère
		else  $top = round($h_centre - ($h_dest * ((2 * $dy + 0.5) / 3)));

		$l_centre = $l_img * $dx;
		if ($align == "focus-center") $left = round($l_centre - ($l_dest * 0.5));
		else  $left = round($l_centre - ($l_dest * ((2 * $dx + 0.5) / 3)));


		if ($top < 0) $top = 0;
		if ($top + $h_dest > $h_img) $top = $h_img - $h_dest;
		if ($left < 0) $left = 0;
		if ($left + $l_dest > $l_img) $left = $l_img - $l_dest;

		//echo "<li>$dx x $dy - $l_img x $h_img - $l_dest x $h_dest - $l_centre x $h_centre - $left x $top</li>";
		$align = "top=$top, left=$left";
	}

	include_spip("filtres/images_transforme");
	$img = image_recadre($img, $l_dest, $h_dest, $align);

	// Second passage si $zoom (on verra plus tard si c'est intéressant de le traiter en amont)
	if ($zoom > 1 && $mode != "focus" && $mode != "focus-center") {
		$l_img = largeur($img) / 2;
		$h_img = hauteur($img) / 2;

		$img = image_recadre($img, $l_img, $h_img);

	}

	return $img;
}


function image_responsive_affiche_milieu($flux, $effacer = false) {

	$exec = $flux["args"]["exec"];


	if ($exec == "admin_vider") {
		$retour = recuperer_fond("squelettes/admin_vider_responsive");

		$flux["data"] .= $retour;
	}

	return $flux;
}



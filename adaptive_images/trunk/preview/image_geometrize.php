<?php
/**
 * Generation d'une preview svg
 *
 * @plugin     Adaptive Images
 * @copyright  2013-2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Adaptive_Images\Preview
 */


/**
 * Generation d'une preview svg a base de geometrize
 *
 * @param string $img
 * @param array $options
 * @return string
 */
function preview_image_geometrize_dist($img, $options){
	static $deja = [];

	// seulement un calcul par image par hit pour ne pas surcharger si la meme image apparait plusieurs fois dans la page
	if (isset($deja[$img])) {
		return $deja[$img];
	}

	include_spip("inc/filtres_images_lib_mini");
	include_spip('filtres/couleurs');
	include_spip('lib/geometrize/geometrize.init');

	// dimension de la miniature a vectoriser
	$geometrize_options = [
		// toutes ces shapes sont viables : rapides a calculer et compact a l'export SVG
		// "shapeTypes" => [geometrize_shape_ShapeTypes::T_TRIANGLE,geometrize_shape_ShapeTypes::T_RECTANGLE,geometrize_shape_ShapeTypes::T_LINE],
		// mais c'est plus joli avec juste des triangles :)
		"shapeTypes" => [geometrize_shape_ShapeTypes::T_TRIANGLE],
		"alpha" => 255, // beaucoup plus rapide qu'avec une transparence
		"candidateShapesPerStep" => 200,
		"shapeMutationsPerStep" => 75,
		"steps" => 85, // budget pour une taille acceptable de miniature (~4ko en texte, 2ko en Base 64+Gzip)
	];
	if (defined('_ADAPTIVE_IMAGES_GEOMETRIZE_SHAPES')) {
		$geometrize_options['shapeTypes'] = explode(',', _ADAPTIVE_IMAGES_GEOMETRIZE_SHAPES);
	}

	$time_budget = 5; // temps maxi en secondes par iteration

	// le premieres iterations sont sur une petite miniature
	// et plus on veut de details plus on augmente la taille de l'image de travail
	// les sizes sont tricky pour avoir un x rescale = 2 = (129 - 1) / (65 - 1) car on rescale de 0 à 64px -> 0 à 128px
	$resize_strategy = [
		65 => 20,
		129 => 100,
		257 => 1000,
		513 => 5000,
	];

	$cache = _image_valeurs_trans($img, "svg-thumb-geometrize-" . json_encode($geometrize_options), "svg");
	if (!$cache){
		return false;
	}

	$fichier = $cache["fichier"];
	$dest = $cache["fichier_dest"];

	if ($cache["creer"]){
		if (!@file_exists($fichier)){
			return false;
		}

		$runner = false;
		$results = [];
		$couleur_bg = _image_couleur_moyenne($fichier);
		//$couleur_bg = couleur_extraire($fichier);
		$width_thumb = array_keys($resize_strategy);
		$width_thumb = reset($width_thumb);

		if (file_exists("$dest.runner")){
			lire_fichier("$dest.runner", $r);
			if ($r = unserialize($r)
				and $version = array_shift($r)
				and $version === geometrize_runner_ImageRunner::Version){
				list($runner, $results) = $r;
				$w = $runner->getModel()->getWidth();
				$h = $runner->getModel()->getHeight();

				foreach ($resize_strategy as $wt => $n){
					if ($wt<=$w){
						$width_thumb = $wt;
					}
				}
			}
			unset($r);
		}

		if (!$runner){
			list($runner, $results) = _init_geometrize_runner($img, $width_thumb, $couleur_bg);
			$w = $runner->getModel()->getWidth();
			$h = $runner->getModel()->getHeight();
		}

		//var_dump("WIDTHUMB $width_thumb");

		$start_time = time();
		spip_timer('runner');
		for ($i = count($results); $i<$geometrize_options['steps']; $i++){

			// faut-il passer a une taille de vignette superieure ?
			if ($i>$resize_strategy[$width_thumb]){

				foreach ($resize_strategy as $wt => $n){
					$width_thumb = $wt;
					if ($n>$i){
						break;
					}
				}

				//var_dump("NEW WIDTHUMB $width_thumb");
				if ($width_thumb>$runner->getModel()->getWidth()){
					// reinit le modele et resizer les shapes au passage
					list($runner, $results) = _init_geometrize_runner($img, $width_thumb, $couleur_bg, $results);
					$w = $runner->getModel()->getWidth();
					$h = $runner->getModel()->getHeight();
				}
			}

			$r = $runner->step($geometrize_options);
			$results = array_merge($results,$r);
			if (time()>$start_time+$time_budget){
				break;
			}
		}
		$time_compute = spip_timer('runner');
		$last = end($results);
		$approx = 100.0 - round($last['score'] * 100,2);

		// DEBUG
		#$png_file = substr($dest, 0, -4) . ".png";
		#if ($runner->getModel()->getCurrent()->exportToImageFile($png_file, 'png')) {
		#	echo("<img src='$png_file' style='width:1200px;height: auto'/>");
		#}
		#$last = end($results);
		#var_dump(100.0 - round($last['score'] * 100,2));
		//var_dump($r,'<hr/>',$results);

		$svg_image = trim(geometrize_exporter_SvgExporter::export($results, $w, $h));

		$svg_image = explode('>', $svg_image, 2);
		if (strpos($svg_image[0], "<" . "?xml")===0){
			$svg_image = explode('>', trim($svg_image[1]), 2);
		}

		$t = $svg_image[0] . '>';
		$w = extraire_attribut($t, "width")-1;
		$h = extraire_attribut($t, "height")-1;

		$svg_image[0] = "<svg viewBox=\"0 0 $w $h\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"$w\" height=\"$h\" fill=\"#$couleur_bg\"/>";

		// optimize the size :
		$svg_image[1] = str_replace(' fill-opacity="1"/>', '/>', $svg_image[1]);
		$svg_image[1] = str_replace(' />', '/>', $svg_image[1]);
		$svg_image[1] = preg_replace_callback("/rgb\((\d+),(\d+),(\d+)\)/ims", "svg_color_hexa", $svg_image[1]);

		#$svg_image[1] = preg_replace(',fill="(#[0-9a-f]+)",Uims', 'fill=\\1', $svg_image[1]);

		$svg_image[1] = str_replace(">\n", ">", $svg_image[1]);
		$svg_image[1] = trim($svg_image[1]);


		//$svg_image[1] = str_replace("black", "#".$couleur_dark, $svg_image[1]);
		$svg_image = $svg_image[0] . $svg_image[1];

		#		var_dump(entites_html($svg_image));
		#var_dump(strlen($svg_image),strlen(base64_encode($svg_image)),strlen(gzdeflate(base64_encode($svg_image))));


		ecrire_fichier($dest, $svg_image);
		$nsteps = count($results);
		if ($nsteps<$geometrize_options['steps']){
			@touch($dest, 1); // on antidate l'image pour revenir ici au prochain affichage
			ecrire_fichier("$dest.runner", serialize([geometrize_runner_ImageRunner::Version, $runner, $results]));
			spip_log("PROGRESS: $fichier t=$time_compute Steps:$nsteps approx:$approx% length:" . strlen($svg_image), 'ai_geometrize');
			//var_dump("STEPS:" . $nsteps);
		} else {
			@unlink("$dest.runner");
			//var_dump("FINISHED:" . $results->length);
			spip_log("FINISHED: $fichier t=$time_compute Steps:$nsteps approx:$approx% length:" . strlen($svg_image), 'ai_geometrize');
		}
	}

	if (!@file_exists($dest)){
		return $deja[$img] = false;
	}

	return $deja[$img] = $dest;
}

function _init_geometrize_runner($img, $width_thumb, $couleur_bg, $results = null){
	$thumb = image_reduire($img, $width_thumb);
	$source = extraire_attribut($thumb, 'src');
	$bitmap = geometrize_bitmap_Bitmap::createFromImageFile($source);
	$runner = new geometrize_runner_ImageRunner($bitmap, _couleur_to_geometrize($couleur_bg));

	$new_results = [];
	if ($results){
		$w = $bitmap->width;
		$h = $bitmap->height;
		foreach ($results as $result) {
			$alpha = $result['shape']->color & 255;
			$result['shape']->rescale($w, $h); // rescale on new bounds
			$new_results[] = $runner->getModel()->addShape($result['shape'], $alpha);
		}
	}
	return [$runner, $new_results];
}

function svg_color_hexa($m){
	$c = _couleur_dec_to_hex($m[1], $m[2], $m[3]);
	return "#$c";
}

function _couleur_to_geometrize($c){
	if (is_string($c)){
		$c = _couleur_hex_to_dec($c);
	}
	if (isset($c['alpha'])){
		// alpha definition is the opposite (255 is opaque, 0 is transparent)
		$c['alpha'] = round((127-$c['alpha'])*255/127);
	} else {
		$c['alpha'] = 255;
	}
	$couleur = ($c['red'] << 24)+($c['green'] << 16)+($c['blue'] << 8)+$c['alpha'];
	return $couleur;
}


if (!function_exists('_image_couleur_moyenne')){

// A partir d'une image,
// calcule la couleur moyenne sur une version reduire de l'image
	function _image_couleur_moyenne($img, $w = 20, $h = 20){
		static $couleur_moyenne = array();

		if (isset($couleur_moyenne["$img-$w-$h"])){
			return $couleur_moyenne["$img-$w-$h"];
		}

		// valeur par defaut si l'image ne peut etre lue
		$defaut = "F26C4E";

		$cache = _image_valeurs_trans($img, "coul-moyenne-$w-$h", "txt");
		if (!$cache){
			return $couleur_moyenne["$img-$w-$h"] = $defaut;
		}


		$fichier = $cache["fichier"];
		$dest = $cache["fichier_dest"];

		if (isset($couleur_moyenne["$fichier-$w-$h"])){
			return $couleur_moyenne["$fichier-$w-$h"];
		}

		$creer = $cache["creer"];

		if ($creer){
			if (@file_exists($fichier)){
				$width = $cache["largeur"];
				$height = $cache["hauteur"];

				$newwidth = $w;
				$newheight = $h;

				$thumb = imagecreate($newwidth, $newheight);

				$source = $cache["fonction_imagecreatefrom"]($fichier);

				imagepalettetotruecolor($source);

				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

				$moyenne = null;
				$nb_points = 0;
				for ($x = 0; $x<$w; $x++){
					for ($y = 0; $y<$h; $y++){
						// get a color
						$color_index = imagecolorat($thumb, $x, $y);
						// make it human readable
						$color_tran = imagecolorsforindex($thumb, $color_index);
						if ($color_tran['alpha']!=127){
							if (is_null($moyenne)){
								$moyenne = $color_tran;
							} else {
								$moyenne['red'] += $color_tran['red'];
								$moyenne['green'] += $color_tran['green'];
								$moyenne['blue'] += $color_tran['blue'];
							}
							$nb_points++;
						}
					}
				}
				if (is_null($moyenne)){
					$couleur = $defaut;
				} else {
					if ($nb_points>1){
						$moyenne['red'] = round($moyenne['red']/$nb_points);
						$moyenne['green'] = round($moyenne['green']/$nb_points);
						$moyenne['blue'] = round($moyenne['blue']/$nb_points);
					}

					$couleur = _couleur_dec_to_hex($moyenne["red"], $moyenne["green"], $moyenne["blue"]);
				}
			} else {
				$couleur = $defaut;
			}

			// Mettre en cache le resultat
			$couleur_moyenne["$fichier-$w-$h"] = $couleur;
			ecrire_fichier($dest, $couleur_moyenne["$fichier-$w-$h"]);
		} else {
			lire_fichier($dest, $couleur_moyenne["$fichier-$w-$h"]);
		}

		return $couleur_moyenne["$img-$w-$h"] = $couleur_moyenne["$fichier-$w-$h"];
	}
}

<?php
/**
 * Fonctions du plugin Filtres Images Vectorise
 *
 * @plugin     Filtres Images Vectorise
 * @copyright  2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Filtres Images Vectorise\Filtres
 */

use \Cerdic\Geometrize\Shape\ShapeTypes;
use \Cerdic\Geometrize\Bitmap;
use \Cerdic\Geometrize\ImageRunner;
use \Cerdic\Geometrize\Version;

/**
 * Generation d'une Image SVG a partir d'un bitmap
 * en utilisant geometrize
 * https://github.com/Cerdic/geometrize-php/
 *
 * @param string $img
 * @param string|int $nb_shapes
 *   'auto' pour un nombre de shape calcule automatiquement en fonction de l'image
 *   ou 'x2.5' pour utiliser le nombre de shape auto * 2.5 (ameliorer la qualite automatique donc)
 *   ou un nombre de formes a utiliser pour geometrizer l'image
 * @param array $options
 *   string shapes : liste des formes utilisables (separee par des virgules) (par defaut triangles)
 *   int alpha : transparence des formes (de 0 = opaque a 127 = transparente) (par defaut 0)
 *   string background : couleur de fond pour initialiser l'image (par defaut extraite de l'image)
 *   int candidateShapesPerStep : nombres de formes aleatoires testees a chaque iteration (par defaut 200)
 *   int shapeMutationsPerStep : nombres de tentatives de mutation pour ameliorer la forme trouvee (par defaut 85)
 *   int maxShapes : nombre maximum de shapes si auto est utilise
 *
 * @return string
 * @throws Exception
 */
function image_geometrize($img, $nb_shapes='auto', $options = []){
	static $deja = [];

	// seulement un calcul par image par hit pour ne pas surcharger si la meme image apparait plusieurs fois dans la page
	// et est trop lourde pour etre calculee en 1 fois
	if (isset($deja[$img])) {
		return $deja[$img];
	}

	$fonction = array('image_geometrize', func_get_args());
	$opt = json_encode($options);
	$cache = _image_valeurs_trans($img, "geometrize-$nb_shapes-$opt", 'svg', $fonction, '', _SVG_SUPPORTED);
	if (!$cache) {
		return ("");
	}

	// facile !
	if ($cache['format_source'] === 'svg'){
		return $img;
	}

	if ($cache["creer"]) {
		include_spip('filtres/couleurs');
		include_spip('filtres/images_lib');
		include_spip('lib/geometrize/geometrize.init');

		// temps maxi en secondes par iteration
		// si on a pas fini on renvoie une image incomplete et on finira au calcul suivant
		$time_budget = 20;
		$time_out = $_SERVER['REQUEST_TIME']+25;
		if (time()>$time_out) {
			return $img;
		}

		if ($nb_shapes === 'auto' or !intval($nb_shapes)) {
			$auto = round(sqrt($cache['largeur'] * $cache['hauteur']) / 2);
			$max_shapes = (isset($options['maxShapes']) ? $options['maxShapes'] : 1200);
			$auto = min($auto, $max_shapes);
			if (strpos($nb_shapes, 'x') === 0 and is_numeric($coeff = substr($nb_shapes,1))) {
				$auto = round($auto * $coeff);
			}
			$nb_shapes = $auto;
		}

		$geometrize_options = [
			// toutes ces shapes sont viables : rapides a calculer et compact a l'export SVG
			// "shapeTypes" => [ShapeTypes::T_TRIANGLE,ShapeTypes::T_RECTANGLE,ShapeTypes::T_LINE],
			// mais c'est plus joli avec juste des triangles :)
			"shapeTypes" => [ShapeTypes::T_TRIANGLE],
			"alpha" => 255, // beaucoup plus rapide qu'avec une transparence
			"candidateShapesPerStep" => 100,
			"shapeMutationsPerStep" => 50,
			"steps" => $nb_shapes,
		];

		if (isset($options['shapes']) and $shapes = _geometrize_shapes_option($options['shapes'])) {
			$geometrize_options['shapeTypes'] = $shapes;
			unset($options['shapes']);
		}
		if (isset($options['alpha'])) {
			$alpha = min(intval($options['alpha']), 96); // si plus transparent que 96 ca devient assez inutilement couteux en temps
			$alpha = max($options['alpha'], $alpha);
			$geometrize_options['alpha'] = round(255 * (127 - $alpha) / 127.0);
			unset($options['alpha']);
		}
		if (isset($options['background'])) {
			$couleur_bg = $options['background'];
			if ($couleur_bg and !in_array($couleur_bg, ['auto', 'transparent']) and !is_bool($couleur_bg)) {
				$couleur_bg = couleur_html_to_hex($options['background']);
				$couleur_bg = '#' . ltrim($couleur_bg, '#');
			}
			unset($options['background']);
		}
		// les autres options
		foreach ($options as $k=>$v) {
			if (isset($geometrize_options[$k]) and is_scalar($geometrize_options[$k]) and is_scalar($v)) {
				$geometrize_options[$k] = $v;
			}
		}

		// le premieres iterations sont sur une petite miniature
		// et plus on veut de details plus on augmente la taille de l'image de travail
		// les sizes sont tricky pour avoir un x rescale = 2 = (129 - 1) / (65 - 1) car on rescale de 0 à 64px -> 0 à 128px
		$resize_strategy = [
			65 => 20,
			129 => 100,
			257 => 1000,
			513 => 5000,
		];

		$fichier = $cache["fichier"];
		$dest = $cache["fichier_dest"];

		if (!isset($couleur_bg)) {
			$couleur_bg = 'auto';
		}

		$runner = null;
		$width_thumb = array_keys($resize_strategy);
		$width_thumb = reset($width_thumb);

		// si on avait pas fini au calcul suivant on a stocke un runner ici pour continuer le calcul
		if (file_exists("$dest.runner")){
			lire_fichier("$dest.runner", $r);
			if ($r = unserialize($r)
				and $version = array_shift($r)
				and $version === Version::VERSION){
				list($runner) = $r;

				$widthModel = $runner->getModel()->getWidth();

				foreach ($resize_strategy as $wt => $n){
					if ($wt<=$widthModel){
						$width_thumb = $wt;
					}
				}
			}
			unset($r);
		}

		if (!$runner){
			$runner = _init_geometrize_runner($img, $width_thumb, $couleur_bg);
		}

		// ne pas commencer le calcul si c'est trop tard
		if (time()>$time_out) {
			return $img;
		}


		$start_time = time();
		spip_timer('runner');
		for ($i = $runner->getNbSteps(); $i<$geometrize_options['steps']; $i++){

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
					$runner = _init_geometrize_runner($img, $width_thumb, $couleur_bg, $runner);
				}
			}

			$runner->steps($geometrize_options, 1);

			if (time()>$start_time+$time_budget or time()>$time_out){
				break;
			}
		}

		$time_compute = spip_timer('runner');
		$approx = 100.0 - round($runner->getScore() * 100,2);

		// DEBUG : export au format png pour verifier l'image interne de Geometrize
		#$png_file = substr($dest, 0, -4) . ".png";
		#if ($runner->getModel()->getCurrent()->exportToImageFile($png_file, 'png')) {
		#	echo("<img src='$png_file' style='width:1200px;height: auto'/>");
		#}

		// Exporter l'image SVG
		$source_width = $cache['largeur'];
		$source_height = $cache['hauteur'];
		$svg_image = $runner->exportToSVG($source_width, $source_height);

		// optimize the size :
		$svg_image = str_replace(' />', '/>', $svg_image);
		$svg_image = str_replace(">\n", ">", $svg_image);
		$svg_image = trim($svg_image);

		ecrire_fichier($dest, $svg_image);

		$nsteps = $runner->getNbSteps();
		if ($nsteps<$geometrize_options['steps']){
			@touch($dest, 1); // on antidate l'image pour revenir ici au prochain affichage
			ecrire_fichier("$dest.runner", serialize([Version::VERSION, $runner]));
			spip_log("PROGRESS: $fichier t=$time_compute Steps:$nsteps/".$geometrize_options['steps']." approx:$approx% length:" . strlen($svg_image), 'image_geometrize');
		} else {
			@unlink("$dest.runner");
			spip_log("FINISHED: $fichier t=$time_compute Steps:$nsteps approx:$approx% length:" . strlen($svg_image), 'image_geometrize');
		}

	}

	return $deja[$img] = image_graver(_image_ecrire_tag($cache, array('src' => $cache["fichier_dest"])));

}


/**
 * Convertir l'option shapes au format chaine en tableau de formes selon la convention geometrize
 * @param string $shapes_str
 * @return array
 */
function _geometrize_shapes_option($shapes_str) {
	$shapes_str = explode(',', $shapes_str);
	$shapes_str = array_map('trim', $shapes_str);
	$shapes = [];
	foreach ($shapes_str as $s) {
		$s = str_replace(' ', '_', $s);
		switch ($s) {
			case 'rectangle':
				$shapes[] = ShapeTypes::T_RECTANGLE;
				break;
			case 'rotated_rectangle':
			case 'rectangle_tourne':
				$shapes[] = ShapeTypes::T_ROTATED_RECTANGLE;
				break;
			case 'triangle':
				$shapes[] = ShapeTypes::T_TRIANGLE;
				break;
			case 'ellipse':
				$shapes[] = ShapeTypes::T_ELLIPSE;
				break;
			case 'rotated_ellipse':
			case 'ellipse_tournee':
				$shapes[] = ShapeTypes::T_ROTATED_ELLIPSE;
				break;
			case 'circle':
			case 'cercle':
				$shapes[] = ShapeTypes::T_CIRCLE;
				break;
			case 'line':
			case 'ligne':
				$shapes[] = ShapeTypes::T_LINE;
				break;
			case 'bezier':
				$shapes[] = ShapeTypes::T_QUADRATIC_BEZIER;
				break;

			default:
				break;
		}
	}

	return $shapes;
}

/**
 * Initialize un runner avec une miniature dont les dimensions sont donnees
 * Redimensionne les resultats exitants si besoin
 *
 * @param $img
 * @param $width_thumb
 * @param $couleur_bg
 * @param null ImageRunner
 * @return ImageRunner
 * @throws HException
 */
function _init_geometrize_runner($img, $width_thumb, $couleur_bg, $runner = null){
	$thumb = image_reduire($img, $width_thumb);
	$source = extraire_attribut($thumb, 'src');
	$bitmap = Bitmap::createFromImageFile($source);
	if (is_null($runner)) {
		if ($couleur_bg === true or $couleur_bg === 'auto') {
			// perf issue : on utilise le filtre SPIP qui opere sur une vignette de 32px et a un cache
			$palette = extraire_palette_couleurs($img, 3, 32);
			$couleur_bg = _couleur_to_geometrize(reset($palette));
		}
		elseif (!$couleur_bg or $couleur_bg === 'transparent') {
			$couleur_bg = false;
		}
		else {
			$couleur_bg = _couleur_to_geometrize($couleur_bg);
		}
		$runner = new ImageRunner($bitmap, $couleur_bg);
	}
	else {
		$runner->reScale($bitmap);
	}

	return $runner;
}


/**
 * Convertir une couleur hexa ou rgb SPIP en couleur geometrize encodee en entier sur 32bits
 * @param string|array $c
 * @return int
 */
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

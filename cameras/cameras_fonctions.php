<?php


function modifier_apparence_camera($new, $id_objet)
{
	global $formats_logos;
	
	//quelques include
	include_spip('inc/chercher_logo');
	include_spip('inc/flock');
	include_spip('action/iconifier');
	include_spip('inc/documents');
	
	$arg = 'cameraon'.$id_objet;
	
	//initialistation des répertoires et fichiers de copie
	$f =_DIR_LOGOS . $arg . '.tmp';
	if (is_dir(_DIR_PLUGINS.'cameras/img/'))
		$path_logocam = _DIR_PLUGINS.'cameras/img/';
	else $path_logocam = _DIR_PLUGINS.'auto/cameras/img/';
	
	$chercher_logo = charger_fonction('chercher_logo','inc');
	$logo = $chercher_logo($id_objet, 'id_camera', 'on');
	if ($logo) spip_unlink($logo[0]);
	
	//On récupére le fichier correspondant au champ
	switch($new){
		case 'nue':
			$source = @copy($path_logocam.'nue.png', $f);
			break;
		case 'dome':
			$source = @copy($path_logocam.'dome.png', $f);
			break;
		case 'encastre':
			$source = @copy($path_logocam.'encastre.png', $f);
			break;
		case 'boite':
			$source = @copy($path_logocam.'boite.png', $f);
			break;
		case 'radar':
			$source = @copy($path_logocam.'radar.png', $f);
			break;
		default:
			$source = @copy($path_logocam.'radar.png', $f);
			break;
	}
	
	if ($source AND $f) {
		$size = @getimagesize($f);
		$type = !$size ? '': ($size[2] > 3 ? '' : $formats_logos[$size[2]-1]);
		if ($type) {
			$poids = filesize($f);

			if (_LOGO_MAX_SIZE > 0
			AND $poids > _LOGO_MAX_SIZE*1024) {
				spip_unlink ($f);
				check_upload_error(6,
				_T('info_logo_max_poids',
					array('maxi' => taille_en_octets(_LOGO_MAX_SIZE*1024),
					'actuel' => taille_en_octets($poids))));
			}

			if (_LOGO_MAX_WIDTH * _LOGO_MAX_HEIGHT
			AND ($size[0] > _LOGO_MAX_WIDTH
			OR $size[1] > _LOGO_MAX_HEIGHT)) {
				spip_unlink ($f);
				check_upload_error(6, 
				_T('info_logo_max_poids',
					array(
					'maxi' =>
						_T('info_largeur_vignette',
							array('largeur_vignette' => _LOGO_MAX_WIDTH,
							'hauteur_vignette' => _LOGO_MAX_HEIGHT)),
					'actuel' =>
						_T('info_largeur_vignette',
							array('largeur_vignette' => $size[0],
							'hauteur_vignette' => $size[1]))
				)));
			}
			@rename ($f, _DIR_LOGOS . $arg . ".$type");
		}
		else {
			spip_unlink ($f);
			check_upload_error(6,_T('info_logo_format_interdit',
						array('formats' => join(', ', $formats_logos))));
			return false;
		}
		return true;
	}
	else return false;
}




/********************************************************

	Fonctions de rendu des tuiles
	
********************************************************/


function servir_tuile($ti, $tpad=0, $tpx){
	$type = $ti['type']; $x = $ti['x']; $y = $ti['y']; $z = $ti['z'];
	$src = sous_repertoires('cache-carto/'.$type.'-vue-'.$tpad.'/'.$z.'/'.$x).$y.'.png';

	if( $recalcul_tuile
		OR !file_exists($src)
		OR ( ($z!=18) && tuiles_enfant_plus_recentes($src, $ti, $tpad) )
	){
		calculer_tuile($src, $tpx, $tpad, $ti);	
	}
	readfile($src);
}

/*
	Calcul de tuile générique
*/
function calculer_tuile($fp_tile, $tpx, $tpad, $ti){
	if($ti['z'] == 18){ // si c'est une tile de base (zoom max)
		calculer_tuile_base($fp_tile, $tpx, $tpad, $ti);
	}else{
		calculer_tuile_echelle($fp_tile, $tpx, $tpad, $ti);
	}
}

/*
	Calcul pour les tuiles de base (niveau de zoom maximum)
*/
function calculer_tuile_base($fp_tile, $tpx, $tpad, $ti){
	$tw = $tpx; $th = $tpx;
	$tside = $tpad*2+1; // nombre de "tiles" de coté, en fonction du padding
	$type = $ti['type']; $x = $ti['x']; $y = $ti['y']; $z = $ti['z'];
	

	$osm_src = tileInfoToOsmSrc($ti);//'http://a.tile.openstreetmap.org/'.$z.'/'.$x.'/'.$y.'.png';

	// pour chaque tile alentour
	// TODO: utiliser la fonction tuiles_adjacentes()
	$hitmap = imagecreatetruecolor($tw*$tside, $th*$tside);
	// on charge les src / hitmap
	for ($i = 0; $i < pow($tside, 2); $i++) {
		$ix = $i%$tside;
		$iy = floor($i/$tside);
		$fp_src = sous_repertoires('cache-carto/'.$type.'/'.$z.'/'.($x+$ix-$tpad) ).($y+round($iy-$tpad)).'.png';

		$fp_hit = sous_repertoires('cache-carto/'.$type.'-hit/'.$z.'/'.($x+$ix-$tpad) ).($y+round($iy-$tpad)).'.png';
		// si la hitmap existe pas on la calcule
		if (!file_exists($fp_hit)){
			// si la tile source n'existe pas on la télécharge
			if (!file_exists($fp_src)){
				copie_locale(
					str_replace($z.'/'.$x.'/'.$y, $z.'/'.($x+$ix-$tpad).'/'.($y+round($iy-$tpad)), $osm_src),
					'auto',
					$fp_src
				);
			}
			// calcule la hitmap
			$im = filtrer_obstacles( $fp_src, $GLOBALS['hit_osm']);
			imagepng($im, $fp_hit );
		}
		// add to stitched hitmap
		$hitpart = imagecreatefrompng($fp_hit);
		imagecopyresampled($hitmap, $hitpart, $ix*$tw, $iy*$th, 0, 0, $tw, $th, $tw, $th);
		imagedestroy($hitpart);
	}
	
	// ecriture de la tuile
	$fp_hitmap = sous_repertoires('cache-carto/'.$type.'-hitmap-'.$tpad.'/'.$z.'/'.$x).$y.'.png';
	imagepng($hitmap, $fp_hitmap );

	// calcule la tuile finale
	$t1 = longlat_pour_tuile($ti['z'], $ti['x'], $ti['y']);
	$cams = cameras_pour_tuile($t1["lat"], $t1["lon"], $tpad);
	$hittest = hitTest($hitmap, 100, 170, 2, 360, $cams);
	imagedestroy($hitmap);

	$final = imagecreatetransparent($tw, $th);
	imagecopyresampled($final, $hittest, 0, 0, $tw*$tpad, $th*$tpad, $th, $tw, $th, $tw);
	imagepng($final, $fp_tile );
	imagedestroy($final);
}

/*
	Calcul pour les tuiles de zoom (composite de ses enfants)
*/
function calculer_tuile_echelle($fp_tile, $tpx, $tpad, $ti){
		$tw = $tpx;
		$th = $tpx;
		
		$subs = tuiles_enfant($ti, $tpad);
		for ($i = 0; $i < count($subs); $i++) {
			if (!file_exists($subs[$i])){
				$subs[$i] = sous_repertoires('cache-carto/').'tile_vide.png';
				if (!file_exists($subs[$i])){
					imagepng( imagecreatetransparent($tw, $th), $subs[$i] );
				}
			}
		}
		$final = imagecreatetransparent($tw, $th);
		imagealphablending($final, false);
		imagecopyresampled($final, imagecreatefrompng($subs[0]), 0, 0, 0,  0,  $th, $tw, $th*2, $tw*2);
		imagecopyresampled($final, imagecreatefrompng($subs[1]), $th/2, 0, 0, 	0,  $th, $tw, $th*2, $tw*2);
		imagecopyresampled($final, imagecreatefrompng($subs[2]), 0, $tw/2, 0,  0, 	$th, $tw, $th*2, $tw*2);
		imagecopyresampled($final, imagecreatefrompng($subs[3]), $th/2, $tw/2, 0,	0, 	$th, $tw, $th*2, $tw*2);
		imagepng($final, $fp_tile );
}
	
	
/********************************************************

	Fonctions Utilitaires

********************************************************/

/*
	Supprime la tuile d'une caméra et les tuiles adjacentes, qui seront recalculée avec la
	prochaine requête
*/
function invalider_tuiles_camera($id_camera){
	$tuile_camera = tuile_camera($id_camera);
	$tuiles_a_invalider = tuiles_adjacentes($tuile_camera['z'], $tuile_camera['x'], $tuile_camera['y']);
	array_push( $tuiles_a_invalider, $tuile_camera );
	foreach ($tuiles_a_invalider as &$t) {
		$src = sous_repertoires('cache-carto/openstreetmap.org-vue-1/'.$t['z'].'/'.($t['x']).'/').$t['y'].'.png';
		if( file_exists($src) ) supprimer_fichier( $src );
	}
}

/*
	Retourne la tuile dans laquelle est la caméra
*/
function tuile_camera($id_camera){
	$tileH_deg = 0.000958157676997;
	$tileW_deg = 0.00137329101562;
	$latlon = sql_fetsel('lat, lon', 'cameras', 'id_camera = '.intval($id_camera));
	return tuile_pour_longlat(18, $latlon['lon']-($tileW_deg/2) , $latlon['lat']+($tileH_deg/2));
}

/*
	Retourne les tuiles adjacentes d'une tuile,
	le padding correspond au nombre de tuiles de large que l'on veut
*/
function tuiles_adjacentes($z, $x, $y, $padding=2){
	$tuiles = array();
	$tside = $padding*2+1; // nombre de tuiles de coté
	for ($i = 0; $i < pow($tside, 2); $i++) {
		$dx = $i%$tside;
		$dy = floor($i/$tside);
		$new_x = $x+$dx-$padding;
		$new_y = $y+round($dy-$padding);
		array_push($tuiles, array( "z"=>$z, "x"=>round($new_x), "y"=>round($new_y) ) );
	}
	return $tuiles;
}

/*
	Determine si les enfants d'une tuile ont été mis à jour plus récemment qu'elle
*/
function tuiles_enfant_plus_recentes($src, $ti, $tpad){
	return filemtime($src) < mdate_plus_recente(tuiles_enfant($ti, $tpad));
}

/*
	Retourne les tuiles enfants d'une tuile de zoom
*/
function tuiles_enfant($ti, $tpad){
	$type = $ti['type']; $x = $ti['x']; $y = $ti['y']; $z = $ti['z'];
	return array(
		sous_repertoires('cache-carto/'.$type.'-vue-'.$tpad.'/'.($z+1).'/'.round($x*2) ).($y*2).'.png',
		sous_repertoires('cache-carto/'.$type.'-vue-'.$tpad.'/'.($z+1).'/'.round(($x*2)+1) ).($y*2).'.png',
		sous_repertoires('cache-carto/'.$type.'-vue-'.$tpad.'/'.($z+1).'/'.round($x*2) ).(($y*2)+1).'.png',
		sous_repertoires('cache-carto/'.$type.'-vue-'.$tpad.'/'.($z+1).'/'.round(($x*2)+1) ).(($y*2)+1).'.png'
	);	
}

/*
	Retoune les caméras présentes dans une tuile
	TODO: refactor
*/
function cameras_pour_tuile($lat, $lon, $tpad){
	$dlat = 0.000958174129103; // pour 256px
	$dlon = 0.00137329101562;

	$res = sql_allfetsel('lat, lon, apparence, direction', 'cameras',
		'lat >= '.($lat-$dlat -$tpad*$dlat).' and lat <= '.($lat +$tpad*$dlat)
		.' and  lon >= '.($lon -$tpad*$dlon).' and lon <= '.($lon+$dlon +$tpad*$dlon)
		.' and  statut = "publie"'
		);

	$cams = array();
	for ($i = 0; $i < count($res); $i++) {
		$camX = round( ($res[$i]["lon"] +$dlon*$tpad -$lon) / $dlon*256 );
		$camY = round( ($res[$i]["lat"] -$dlat*$tpad -$lat) / (-$dlat)*256 );
		array_push($cams, array($camX, $camY, $res[$i]["apparence"], $res[$i]["direction"]) );
	}

	return $cams;
}

/*
	Retourne les coordonnées d'une tuile pour une coordonnées et un niveau de zoom donnée
 	http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#lon.2Flat_to_tile_numbers
*/
function tuile_pour_longlat($zoom, $lon_deg, $lat_deg){
	$lat_rad = $lat_deg * pi() / 180;
	$n = pow(2, $zoom);
	$xtile = ( ($lon_deg + 180) / 360) * $n;
	$ytile = (1 - ( log(tan($lat_rad) + (1/cos($lat_rad)) ) / pi())) / 2 * $n;
	return array( "z"=>$zoom, "x"=>round($xtile), "y"=>round($ytile) );
}

/*
	Retourne la longitude et latitude correspondant à une tuile
 	http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#lon.2Flat_to_tile_numbers
*/
function longlat_pour_tuile($zoom, $x, $y){
	$n = pow(2, $zoom);
	$lon_deg = $x / $n * 360 - 180;
	$lat_rad = atan(sinh(pi()*(1 - 2 * $y / $n)));
	$lat_deg = $lat_rad * 180 / pi();
	return array( "lon"=>$lon_deg, "lat"=>$lat_deg );
}

/*
	Retourne la date de modif la plus récente d'une série de fichiers
*/
function mdate_plus_recente($fp){
	$dates = array();
	for ($i = 0; $i < count($fp); $i++) {
		$f = $fp[$i];
		if (file_exists($f)){
			array_push($dates, filemtime($f));
		}else{
			array_push($dates, 0);
		}
	}
	return max($dates);
}

/*
	Similaire à sous_repertoire de Spip mais récursif
*/
function sous_repertoires($path){
	$path = ($path[strlen($path)-1] == "/") ? substr( $path, 0, strlen($path)-1) : $path;
	$dirs = explode('/', $path);
	$dir = _DIR_VAR;//$dirs[0];
	foreach ($dirs as &$value) {
		$dir = sous_repertoire($dir, $value);
	}
	return _DIR_VAR.$path.'/';
}

function tileInfoToOsmSrc($ti){ return 'http://tile.openstreetmap.org/'.$ti['z'].'/'.$ti['x'].'/'.$ti['y'].'.png'; }

function fileName($url){ return basename($url); }

function removeExt($fn){ return pathinfo($fn, PATHINFO_FILENAME); }

function getExt($fn){ return array_pop(explode('.', $fn)); }

function trace($m){ if( $GLOBALS['debug'] ) echo($m."\n"); }
	

/********************************************************

	Fonctions Graphiques

********************************************************/


function filtrer_obstacles($path, $couleurs_obstacles){
	$im_src = @imagecreatefrompng($path);
	if( $im_src == false) $im_src = imagecreatefromjpeg($path);
	$size = getimagesize($path);
	// TODO : passer direct en truecolors ?
	$im = imagecreatetruecolor($size[0], $size[1]);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $size[0], $size[1], $size[0], $size[1]);

	$im_colors = array();
	foreach ($couleurs_obstacles as &$v) {
		array_push($im_colors, imagecolorallocate( $im,  $v[0],$v[1],$v[2] ) );
	}

	$white = imagecolorallocate($im, 255,255,255);
	$black = imagecolorallocate($im, 0,0,0);

	for ($i = 0; $i < $size[0]*$size[1]; $i++) {
		$x = $i%$size[0];
		$y = floor($i/$size[0]);
		$colInfo = imagecolorat($im, $x, $y);

		// vire les pixels isolés
		$voisinsHit = 0;
		$voisins = array(
			imagecolorat($im, $x-1, $y-1),
			imagecolorat($im, $x, $y-1),
			imagecolorat($im, $x+1, $y-1),

			imagecolorat($im, $x-1, $y),
			imagecolorat($im, $x+1, $y),

			imagecolorat($im, $x-1, $y+1),
			imagecolorat($im, $x, $y+1),
			imagecolorat($im, $x+1, $y+1)
		);
		foreach ($voisins as &$value) {
			if( in_array($value, $im_colors) || $value == $black ){
				$voisinsHit++;
			}
		}

		// colorise
		if ( in_array($colInfo, $im_colors) && $voisinsHit > 2 ){
			imagesetpixel($im, $x, $y, $black);
		}else{
			imagesetpixel($im, $x, $y, $white);
		}
	}
	return $im;
}

/*
	Hit test sur une image n&b depuis un point

	$img : image, noir = obstacle
	$cy, $cx : coordonnées du point de vue
	$ratio : antialising, 1 = pixelisé
	$sample : nombre de rayons lancés
*/

function hitTest($img, $cx, $cy, $ratio=1, $samples=360, $optCams){
	
	$samples=360;
	
	$w = imagesx($img);
	$h = imagesy($img);

	$red = imagecolorallocate($img, 255, 0, 0); 
	$green = imagecolorallocate($img, 0, 255, 0);
	$blue = imagecolorallocate($img, 0, 0, 255);
	$black = imagecolorallocate($img, 0, 0, 0);
	$white = imagecolorallocate($img, 255, 255, 255);

	$camHitPts = array();
	foreach ($optCams as &$value) {
		$cx = $value[0];
		$cy = $value[1];
		$type = $value[2];
		$fov = ($type == "dome") ? 360 : 40; // champ de vision par defaut de la cam
		$direction = intval($value[3])-$fov/2-45-90;
		$samples = $fov;
		
		$hitPts = array();
		array_push($hitPts, $cx, $cy); // ajout du point d'origine
		// test d'intersection sur basse def
		for ($ai = $direction; $ai <= $direction+$fov; $ai++) {
			
				$pt;
				$angle = 45+$ai;
				$i = 0; $x; $y;
				$hit = imagecolorat($img, $cx, $cy);
				$x = $cx; $y = $cy;
				while($i < round($w/3) && /*$x <= $w && $y <= $h && */$hit != 0){
					$x = $cx + cos(deg2rad($angle))*$i;
					$y = $cy + sin(deg2rad($angle))*$i;
					$hit = @imagecolorat($img, $x, $y);
					$i++;
				}
				array_push($hitPts, $x, $y);
		}
		array_push($camHitPts, array( array($cx, $cy), $hitPts) );
	}

	// canvas high def pour antialias
	$canvas = imagecreatetransparent($w*$ratio, $h*$ratio);

	$red = imagecolorallocate($canvas, 255, 0, 0); 
	$red80 = imagecolorallocatealpha($canvas, 255,0,0, 80);
	$red100 = imagecolorallocatealpha($canvas, 255,0,0, 100);
	$red120 = imagecolorallocatealpha($canvas, 255,0,0, 120);
	$green = imagecolorallocate($canvas, 0, 255, 0);
	$transparent = imagecolorallocatealpha($canvas, 255,255,255, 64);
	$blue = imagecolorallocate($canvas, 0, 0, 255);
	$black = imagecolorallocate($canvas, 0, 0, 0);
	$white = imagecolorallocate($canvas, 255, 255, 255);

	//if ( count($camHitPts) > 5 ){ // evite de rendre celles qui sont dans des murs
		foreach ($camHitPts as &$camHitPt) {
			$cx = $camHitPt[0][0];
			$cy = $camHitPt[0][1];
			$hitPts = $camHitPt[1];

			for ($i = 0; $i < count($hitPts); $i++) $hitPts[$i] = $hitPts[$i]*$ratio;
			//poly
			//array_push($hitPts, $cx*$ratio, $cy*$ratio);
			imagefilledpolygon($canvas, $hitPts, count($hitPts)/2, $red80);
			// lignes
			for ($i = 0; $i < count($hitPts); $i = $i+2)
				imageline($canvas, $cx*$ratio, $cy*$ratio, $hitPts[$i], $hitPts[$i+1], $red80);
		}
	//}

	// dessine les cams
	foreach ($optCams as &$value) {
		$cx = $value[0]; $cy = $value[1];
		imagefilledellipse($canvas, $cx*$ratio, $cy*$ratio, 6*$ratio, 6*$ratio, $black);
		imagefilledellipse($canvas, $cx*$ratio, $cy*$ratio, 2*$ratio, 2*$ratio, $white);
	}

	$final = imagecreatetransparent($w, $h);
	imagecopyresampled($final, $canvas, 0, 0, 0, 0, $w, $h, $w*$ratio, $h*$ratio);
	//filter_opacity( $final, 70 );
	return $final;
}


function setGlobals(){
	$GLOBALS['hit_osm'] = array(
		array(190,173,173), // couleur batiments
		array(148,122,155), // contour violet batiments
		array(162,139,162), // contour violet batiments
		array(155,130,157), // contour violet batiments
		array(181,162,169), // contour violet batiments
		array(177,167,157), // contour violet batiments
		array(166,145,165), // contour violet batiments
		array(182,163,155), // contour violet batiments
		array(184,166,170), // contour violet batiments
		array(188,170,172), // contour violet batiments
		
		array(193,176,173), // couleur groupe scolaire
		
		array(174,174,174), // couleur chapelle
		array(129,111,135), // couleur contour chapelle
		array(131,131,131), // couleur contour chapelle
		
		// certaines tuiles sont "assombries" - bug qu'on avait vers saint jean
		array(192,176,174),
		array(193,176,174),
		array(193,176,175) // originale : 190 173 173	
	);
}

/*
	Crée une image transparente pour GD
*/
function imagecreatetransparent($w, $h){
	$canvas = imagecreatetruecolor($w, $h);
	imagealphablending($canvas, false);
	$transparent = imagecolorallocatealpha($canvas, 255,255,255, 127);
	imagefill($canvas, 0, 0, $transparent);
	imagealphablending($canvas, true);
	imagesavealpha($canvas,true);
	return $canvas;
}

/*
	Filtre d'opacité pour GD
	http://www.php.net/manual/en/function.imagefilter.php#82162
	params: image resource id, opacity in percentage (eg. 80)
*/
function filter_opacity( &$img, $opacity ){
    if( !isset( $opacity ) )
        { return false; }
    $opacity /= 100;

    //get image width and height
    $w = imagesx( $img );
    $h = imagesy( $img );

    //turn alpha blending off
    imagealphablending( $img, false );

    //find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
    for( $x = 0; $x < $w; $x++ )
        for( $y = 0; $y < $h; $y++ )
            {
                $alpha = ( imagecolorat( $img, $x, $y ) >> 24 ) & 0xFF;
                if( $alpha < $minalpha )
                    { $minalpha = $alpha; }
            }

    //loop through image pixels and modify alpha for each
    for( $x = 0; $x < $w; $x++ )
        {
            for( $y = 0; $y < $h; $y++ )
                {
                    //get current alpha value (represents the TANSPARENCY!)
                    $colorxy = imagecolorat( $img, $x, $y );
                    $alpha = ( $colorxy >> 24 ) & 0xFF;
                    //calculate new alpha
                    if( $minalpha !== 127 )
                        { $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha ); }
                    else
                        { $alpha += 127 * $opacity; }
                    //get the color index with new alpha
                    $alphacolorxy = imagecolorallocatealpha( $img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
                    //set pixel with the new color + opacity
                    if( !imagesetpixel( $img, $x, $y, $alphacolorxy ) )
                        { return false; }
                }
        }
    return true;
}

?>
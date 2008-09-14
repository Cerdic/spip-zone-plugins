<?php

/*
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteur :
 * Quentin Drouet
 *
 * © 2008 - Distribue sous licence GNU/GPL
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_IMG_GD_QUALITE', lire_config('photospip/compression_rendu') ? lire_config('photospip/compression_rendu') : 85);
	
function action_photospip_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		spip_log("action_photospip_dist $arg pas compris","photospip");
	} else {
		action_photospip_post($r);
	}
}

function action_photospip_post($r){
	global $visiteur_session;
	$id_auteur = $visiteur_session['id_auteur'];
	spip_log($r,'photospip');
	
	//on récup l'id_document
	$arg = $r[1];
	spip_log("on travail sur l'id_document $arg","photospip");
	
	// Fichier destination : on essaie toujours de repartir de l'original

	//Quelle redirection finale?
	if (_SPIP_AJAX === 1 ){
		$redirect = _request('redirect_ajax');
	}
	else{
		$redirect = _request('redirect');
	}
	
	// Quel filtre lui appplique-t-on?
	$var_filtre = _request('filtre');
	
	if(!$var_filtre){
		$redirect = parametre_url($redirect,'message','sansfiltre');
		redirige_par_entete(str_replace("&amp;","&",$redirect));
		spip_log("on a oublié de choisir le filtre donc  on retourne rien...", "photospip");
	}
	
	// On vérifie si le document existe
	$result = sql_select("*", "spip_documents", "id_document=$arg");

	if (!$row = sql_fetch($result))
		return;
	
	spip_log("On lui applique le filtre $var_filtre","photospip");	

	$validation = _request('validation');

	if ($var_filtre == "tourner"){
		$params = _request('params_tourner');
		spip_log("params tourner = $params", "photospip");
	}
	else if ($var_filtre == "image_recadre"){
		$param1 = _request('recadre_width');
		$param2 = _request('recadre_height');
		$param_left = _request('recadre_x1');
		$param_top = _request('recadre_y1');
		$param3 = 'left='.$param_left.' top='.$param_top;
		$params = array($param1,$param2,$param3);
		spip_log("params image_recadre = print_r($params)", "photospip");
	}
	else if ($var_filtre == 'image_sepia'){
		$params = _request('params_image_sepia');
		$params = str_replace('#','',$params);
		spip_log("params image_sepia = $params", "photospip");
	}
	else if($var_filtre == 'image_gamma'){
		$params = _request('params_image_gamma');
		spip_log("params image_gamma = $params", "photospip");
	}
	else if($var_filtre == 'image_flou'){
		$params = _request('params_image_flou');
		spip_log("params image_image_flou = $params", "photospip");
	}
	else if($var_filtre == 'image_saturer'){
		$params = _request('params_image_saturer');
		spip_log("params image_saturer = $params", "photospip");
	}
	else if($var_filtre == 'image_rotation'){
		$params = _request('params_image_rotation');
		spip_log("params params_image_rotation = $params", "photospip");
	}
	else if($var_filtre == 'image_niveaux_gris_auto'){
		$params = '';
		spip_log("params image_niveaux_gris_auto = $params", "photospip");
	}

	
	if($validation == "appliquer"){
		include_spip('inc/charsets');	# pour le nom de fichier
		include_spip('inc/documents'); 
		$src = _DIR_RACINE . get_spip_doc($row['fichier']);
		if (preg_match(',^(.*)-photospip(\d+).([^.]+)$,', $src, $match)) {
			$version = $match[2];
			$orig_src = $match[1].'.'.$match[3];
			spip_log("nouvel src $src","photospip");
			spip_log("version = $version","photospip");
			$newversion = ++$version;
			spip_log("La nouvelle version sera $newversion","photospip");
		}
	
		if($version){
			// $dest = preg_replace(',\.[^.]+$,', '-r'.$var_rot.'$0', $src); //original
			$dest = preg_replace(",\.[^.]+$,", "-photospip".($newversion)."$0", $orig_src);
			spip_log("la destination sera $dest","photospip");
			spip_log("application du filtre $var_filtre $src : $dest","photospip");
		
		}
		else{
			$dest = preg_replace(',\.[^.]+$,', '-photospip1.png', $src);
			// on transforme l'image en png non destructif
			include_spip('inc/filtres_images');
			spip_log("On transforme l'image source en PNG non destructif","photospip");
			$src = extraire_attribut(image_alpha($src,0),'src');
			spip_log("application du filtre $var_filtre $src : $dest","photospip");
		}
		$process = $GLOBALS['meta']['image_process'];
	
		// imagick (php4-imagemagick)
		if ($process == 'imagick') {
			spip_log("Vous utilisez imagick, veuillez utiliser GD pour le traitement d'images","photospip");
			return;
		}
		else if ($process == "gd2") {
			spip_log("Vous utilisez gd2, tout est ok jusque là","photospip");
			if($var_filtre == "tourner"){
				include_spip('action/tourner');
				$dst_img = gdRotate ($src, $dest, $params);
			}
			else{
				photospipfiltre($src, $dest, $var_filtre,$params);
			}
		}
		else if ($process = "convert") {
			spip_log("Vous utilisez convert, veuillez utiliser GD pour le traitement d'images","photospip");
			return;
		}
		else{
			spip_log("Veuillez utiliser GD pour le traitement d'images","photospip");
			return;
		}
	
		$size_image = @getimagesize($dest);
		spip_log("taille de l'image $size_image[0] x $size_image[1]","photospip");
		$largeur = $size_image[0];
		$hauteur = $size_image[1];
		$ext = substr($dest, strpos($dest, ".")+1, strlen($dest));
		$poids = filesize($dest);
		
		// succes !
		if ($largeur>0 AND $hauteur>0) {
			sql_insertq("spip_documents_inters",array("id_document" => $row['id_document'],"id_auteur" => $id_auteur,"extension" => $row['extension'], "fichier" => $row['fichier'], "taille" => $row['taille'],"hauteur" => $row['hauteur'], "largeur" => $row['largeur'],"mode" => $row['mode'], "version" => ($version? $version:1), "filtre" => $var_filtre, "param" => $params));
			sql_updateq('spip_documents', array('fichier' => set_spip_doc($dest), 'taille' => $poids, 'largeur'=>$largeur, 'hauteur'=>$hauteur, 'extension' => $ext), "id_document=$arg");
			spip_log("Update de l'image dans la base poid= $poids, extension = $ext, hauteur= $hauteur, largeur = $largeur, fichier = $dest","photospip");
			if ($effacer) {
				spip_log("j'efface $effacer","photospip");
				spip_unlink($effacer);
			}
			redirige_par_entete(str_replace("&amp;","&",$redirect));
		}
	}
	else if($validation == "tester"){
		// Si on fait simplement un test on se tappe pas tout le traitement sur l'image de base
		if(($var_filtre == 'tourner') || ($var_filtre == 'image_recadre')){
			$redirect = parametre_url($redirect,'message','sanstest');
			redirige_par_entete(str_replace("&amp;","&",$redirect));
			spip_log("on est dans un filtre tourner que l'on ne peut pas tester donc on retourne rien...", "photospip");
		}
		else{
			$redirect = parametre_url($redirect,'message','previsu');
			$redirect = parametre_url($redirect,'filtre',$var_filtre);
			if($var_filtre == "image_recadre"){
				if (!$param1){
					$redirect = parametre_url($redirect,'filtre','');
					$redirect = parametre_url($redirect,'message','sansconf');
				}
				else{
					$redirect = parametre_url($redirect,'param',$param1);
				}
			}
			else{
				$redirect = parametre_url($redirect,'param',$params);
			}
			if($param2){
				$redirect = parametre_url($redirect,'param2',$param2);
			}
			if($param3){
				$redirect = parametre_url($redirect,'param3',$param3);
			}
			spip_log("on est dans un test, on fait simplement un retour avec des paramètres dans l'ajax : filtre = $var_filtre, param = $params", "photospip");
			redirige_par_entete(str_replace("&amp;","&",$redirect));
		}
	}
}


/////////////////////////////////////////////////////////////////////
//
// Appliquer le filtre image
//
/////////////////////////////////////////////////////////////////////

function photospipfiltre ($src, $dest, $filtre,$params){
	spip_log("src = $src","photospip");
	spip_log("dest = $dest","photospip");
	spip_log("filtre = $filtre","photospip");
	spip_log("params = $params","photospip");
	
	include_spip('inc/filtres_images');
	include_spip('fonctions_images_fonctions');
		
	$src_img = '';
	
	if ($filtre){
		if($params){
			if($filtre == 'image_recadre'){
				$dst_img = $filtre($src,$params[0],$params[1],$params[2]);
				spip_log("$filtre($src,$params[0],$params[1],$params[2]);","photospip");
			}
			else{
				$dst_img = $filtre($src,$params);		
			}
		}
		else{
			$dst_img = $filtre($src);
		}
		$dst_img = extraire_attribut($dst_img,'src');
		spip_log("après le filtre $filtre dst_img = $dst_img","photospip");		
	}

		if(preg_match("/\.(png|gif|jpe?g|bmp)$/i", $src, $regs)) {
		switch($regs[1]) {
			case 'png':
			  if (function_exists('ImageCreateFromPNG')) {
				$src_img=ImageCreateFromPNG($dst_img);
				spip_log("creation png from $dst_img","photospip");
				$save = 'imagepng';
			  }
			  break;
			case 'gif':
			  if (function_exists('ImageCreateFromGIF')) {
				$src_img=ImageCreateFromGIF($dst_img);
				$save = 'imagegif';
			  }
			  break;
			case 'jpeg':
			case 'jpg':
			  if (function_exists('ImageCreateFromJPEG')) {
				$src_img=ImageCreateFromJPEG($dst_img);
				spip_log("creation jpg from $dst_img","photospip");
				$save = 'Imagejpeg';
			  }
			  break;
			case 'bmp':
			  if (function_exists('ImageCreateFromWBMP')) {
				$src_img=@ImageCreateFromWBMP($dst_img);
				$save = 'imagewbmp';
			  }
			  break;
		}
	}

	if (!$src_img) {
		spip_log("photospipfiltre : image non lue, $src","photospip");
		return false;
	}

	$size=@getimagesize($src);
	if (!($size[0] * $size[1])) return false;

	//ImageDestroy($src_img);
	ImageInterlace($src_img,0);

	$save($src_img,$dest);
	spip_log("dest $dest","photospip");
}

?>
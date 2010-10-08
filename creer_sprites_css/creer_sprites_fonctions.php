<?php


function sprite ($img, $nom) {
	global $sprites;

	if (@file_exists($img)) $src = $img;	
	else $src = extraire_attribut($img, "src");
	$sprites["$nom"]["fichiers"][] = $src;
	
	$largeur = largeur($img);
	$hauteur = hauteur($img);
	
	if ($largeur > $sprites["$nom"]["largeur"]) $sprites["$nom"]["largeur"] = $largeur;
	$hauteur_old = max(0, $sprites["$nom"]["hauteur"]);
	$sprites["$nom"]["hauteur"] += $hauteur;
	
	$alt = extraire_attribut($img, "alt");
	$class = extraire_attribut($img, "class");

	$fichier = sous_repertoire(_DIR_VAR, 'cache-sprites').$nom;
	
	$date_src = @filemtime($src);
	if ($date_src > $sprites["$nom"]["date"]) $sprites["$nom"]["date"] = $date_src;


	
	return "<img src='rien.gif' style='width: ".$largeur."px; height: ".$hauteur."px; background: url($fichier) 0px -".$hauteur_old."px;' alt='$alt' class='$class' />";
}

function _terminaison_fichier_image($fichier) {
	if (preg_match(",^(?>.*)(?<=\.(gif|jpg|png)),", $fichier, $regs)) {
		$terminaison = $regs[1];
		return $terminaison;
	} else {
		return false;
	}

}

function creer_sprites_recuperer_fond ($flux) {
	global $sprites;
	
	if ($sprites) {
	
		foreach($sprites as $key => $sprite) {
			$fichier = sous_repertoire(_DIR_VAR, 'cache-sprites').$key;
			
			
			$date_max = $sprite["date"];
			$date_src = @filemtime($fichier);
			$largeur = $sprite["largeur"];
			$hauteur = $sprite["hauteur"];
	
			$creer = false;
			if ($date_src < $date_max) $creer = true;
			if ($largeur != largeur($fichier) || $hauteur != hauteur ($fichier)) $creer = true;
			
			
			if ($creer) { 
				$im = imagecreatetruecolor($largeur, $hauteur);		
				imagepalettetotruecolor($im);
				@imagealphablending($im, false); 
				@imagesavealpha($im,true); 
				$color_t = imagecolorallocatealpha( $im, 0, 0, 0 , 127 );
				imagefill ($im, 0, 0, $color_t);
	
				$y_total = 0;
				foreach($sprite["fichiers"] as $img) {
				
					$f = "imagecreatefrom".str_replace("jpg","jpeg",_terminaison_fichier_image($img));
					$im_ = $f($img);
					@imagepalettetotruecolor($im_);
					
					$x = imagesx($im_);
					$y = imagesy($im_);
					
					
					@ImageCopy($im, $im_, 0, $y_total, 0, 0, $x, $y);
					$y_total += $y;
				}
	
				$nom_fichier = substr($fichier, 0, strlen($fichier) - 4);
				_image_imagepng($im, "$nom_fichier.png");
				$f = _terminaison_fichier_image($fichier);
				if ($f != "png") {
					$new = extraire_attribut( image_aplatir("$nom_fichier.png", $f, "ffffff"), "src");
					@copy($new, $fichier);
						
				}
				
				imagedestroy($im);
				imagedestroy($im_);
	
			}
		}
	}
		
	return $flux;
}


?>
<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_smush_image_dist($im){
	if(!_IS_BOT){
		if(defined(_SMUSH_API) && _SMUSH_API){
			$image = _image_valeurs_trans($im, "smush");
			$im = $image["fichier"];
			$dest = $image["fichier_dest"];
			$creer = $image["creer"];
			include_spip('inc/smush_php_compat');
			if(!file_exists($im))
				return $im;

			// L'adresse de l'API que l'on utilise
			$url_smush = 'http://www.smushit.com/ysmush.it/ws.php';

			// On ajoute les paramètres nécessaires pour l'API
			$url_smush_finale = parametre_url($url_smush,'img',url_absolue($im));
			spip_log("SMUSH : recuperation du contenu de $url_smush_finale","smush");

			$content = file_get_contents($url_smush_finale);
			$newcontent = json_decode($content, true);
			if(!$newcontent['error']){
				include_spip('inc/distant');
				$new_url = $newcontent['dest'];
				spip_log("SMUSH : recuperation du fichier $new_url","smush");
				$contenu = recuperer_page($new_url,false,false,_COPIE_LOCALE_MAX_SIZE);
				if ($contenu)
					ecrire_fichier($im, $contenu);
			}else{
				spip_log('SMUSH en erreur','smush.'._LOG_ERREUR);
				spip_log($newcontent['error'],'smush.'._LOG_ERREUR);
			}
			return $im;
		}else
			return image_smush($im);
	}

	return $im;
}

/**
 * Fonction de réduction d'image
 * Nécessite que la fonction exec() soit utilisable
 * Nécessite certains binaires sur le serveur :
 * -* identify : apt-get install imagemagick
 * -* convert : apt-get install imagemagick
 * -* pngnq : apt-get install pngnq
 * -* pngoptim : apt-get install pngoptim
 * -* jpegtran : apt-get install libjpeg-progs
 * -* gifsicle : apt-get install gifsicle
 * 
 * @param string $im
 * 		Le tag image (<img src...>) à réduire
 * @return string
 * 		Le nouveau tag image
 */
function image_smush($im) {
	$fonction = array('smush', func_get_args());
	$image = _image_valeurs_trans($im, "smush",false,$fonction);
	if (!$image) $im;

	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];

	// Methode precise
	// resultat plus beau, mais tres lourd
	// Et: indispensable pour preserver transparence!
	if ($creer) {
		$format = trim(exec('identify -format %m '.$im));
		/**
		 * On récupère le nom de fichier sans extension
		 */
		$tmp = explode('.',$dest);
		array_pop($tmp);
		$tmp = join('.',$tmp);
		
		/**
		 * Si on est sur un GIF, on le transforme en PNG
		 * On utilise la commande convert pour cela
		 */
		if ($format == 'GIF') {
			$dest = $tmp.'.png';
			exec('convert '.$im.' '.$dest);
			$im = $dest;
			$format = 'PNG';
		}
		
		/**
		 * On est sur un PNG
		 */
		if ($format == 'PNG') {
			$nq = substr($im,0,-4).'-nq8.png';
			exec('pngnq '.$im.' && optipng -o5 '.$nq.' -out '.$dest,$out);
			if(file_exists($nq))
				spip_unlink($nq);
		}
		
		/**
		 * On est sur un JPEG
		 */
		else if ($format == 'JPEG') {
			$fsize = filesize($im);
			$dest = $tmp.'.jpg';
			if ($fsize < 10*1024)
				exec('jpegtran -copy none -optimize '.$im.' > '.$dest);
			else
				exec('jpegtran -copy none -progressive '.$im.' > '.$dest);
		}

		/**
		 * On est sur un GIF animé
		 */
		else if(preg_match('/^GIFGIF/',$format)){
			$dest = $tmp.'.gif';
			exec('gifsicle -O3 '.$im.' -o '.$dest);
		}
		
		/**
		 * Si la taille du résultat est supérieure à l'original,
		 * on retourne l'original en supprimant le fichier temporaire créé
		 */
		if(filesize($dest) > filesize($im)){
			spip_unlink($dest);
			$dest = $im;
		}
	}
	return _image_ecrire_tag($image,array('src'=>$dest));
}
?>
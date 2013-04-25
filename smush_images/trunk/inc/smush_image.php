<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_smush_image_dist($im){
	
	$image = _image_valeurs_trans($im, "smush");
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	$creer = $image["creer"];
	if($creer && !_IS_BOT){
		if(defined(_SMUSH_API) && _SMUSH_API){
			//spip_log($image,'test.'._LOG_ERREUR);
			include_spip('inc/smush_php_compat');
			if(!file_exists($im)){
				spip_log("SMUSH : mauvais chemin pour la fonction smush_image","test."._LOG_ERREUR);
				return '';
			}
			spip_log("SMUSH : smush_image pour $im","smush");
			
			// L'adresse de l'API que l'on utilise
			$url_smush = 'http://www.smushit.com/ysmush.it/ws.php';
			
			// On ajoute les paramètres nécessaires pour l'API
			$url_smush_finale = parametre_url($url_smush,'img',url_absolue($im));
			spip_log("SMUSH : recuperation du contenu de $url_smush_finale","smush");
			
			$content = file_get_contents($url_smush_finale);
			$newcontent = json_decode($content, true);
			
			spip_log($newcontent,"smush."._LOG_ERREUR);
			
			if(!$newcontent['error']){
				include_spip('inc/distant');
				$new_url = $newcontent['dest'];
				spip_log("SMUSH : recuperation du fichier $new_url","smush");
				$contenu = recuperer_page($new_url,false,false,_COPIE_LOCALE_MAX_SIZE);
				if (!$contenu) return false;
				ecrire_fichier($im, $contenu);
			}else{
				spip_log('SMUSH en erreur','test.'._LOG_ERREUR);
				spip_log($newcontent['error'],'test.'._LOG_ERREUR);
			}
		}else{
			if ($dest = image_smush($im, _DIR_TMP.basename($im).'-smush-'.getmypid())
				AND is_readable($dest)
				AND filesize($dest) > 0
				AND filesize($dest) < filesize($im)
				){
					rename($dest,$im);
				}else if(file_exists($dest)){
					spip_unlink($dest);
				}else{
					spip_log('Fichier n existe pas','test.'._LOG_ERREUR);
				}
		}
	}
	
	return _image_ecrire_tag($image,array('src'=>$dest));
}

function image_smush($im) {
	$fonction = array('smush', func_get_args());
	$image = _image_valeurs_trans($im, "",false,$fonction);
	if (!$image) return("");
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	
	$creer = $image["creer"];
	
	// Methode precise
	// resultat plus beau, mais tres lourd
	// Et: indispensable pour preserver transparence!

	if ($creer) {
		$format = trim(exec('identify -format %m '.$source));
	
		if ($format == 'GIF') {
			$dest = $tmp.'.png';
			exec('convert '.$source.' '.$dest);
			$source = $dest;
			$format = 'PNG';
		}
	
		else if ($format == 'PNG') {
			$nq = substr($source,0,-4).'-nq8.png';
			exec('pngnq '.$source.' && optipng -o5 '.$nq.' -out '.$dest,$out);
			if(file_exists($nq))
				spip_unlink($nq);
			return $dest;
		}
	
		else if ($format == 'JPEG') {
			$fsize = filesize($source);
			$dest = $tmp.'.jpg';
			if ($fsize < 10*1024) {
				exec('jpegtran -copy none -optimize '.$source.' > '.$dest);
			}
			else {
				exec('jpegtran -copy none -progressive '.$source.' > '.$dest);
			}
		}
	}
	return _image_ecrire_tag($image,array('src'=>$dest));
}
?>
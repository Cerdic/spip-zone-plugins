<?php


function action_image_responsive() {

	$img = _request("img");
	$taille = _request("taille");
	$dpr = _request("dpr");
	$xsendfile = _request("xsendfile");

	if (!preg_match(',\.(gif|jpe?g|png)$,i', $img)
	OR !preg_match(',^\d+v?$,', $taille)
	OR !preg_match(',^[\d\.]*$,', $dpr)
	OR !file_exists($img)) {
		header('HTTP/1.1 500 Internal Server Error');
		die( "Erreur" );
	} else {
		$terminaison = substr($img, strlen($img)-3, 3);
		$base = sous_repertoire(_DIR_VAR, "cache-responsive");
		$base = sous_repertoire($base, "cache-".$taille);
		$dest = md5($img);
		if ($dpr > 1) $dest .= "$dest-$dpr";
		else $dpr = false;
		
		$dest = $base.$dest.".".$terminaison;

		if (file_exists($dest)) {
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
				strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($dest))
			{
				header('HTTP/1.0 304 Not Modified');
				exit;
			}
		}
		
		
		if (!file_exists($dest) OR filemtime($dest) < filemtime($img)) {
			// Là on fabrique l'image
			// et on la recopie vers $dest
			//
			//cette méthode permet d'accélérer par rapport à SPIP
			// parce qu'on connait le nom du fichier à l'avance
			// et on fait donc les tests sans déclencher la cavalerie
			
			if (preg_match("/([0-9]+)v$/", $taille, $regs)) {
				$taille = $regs[1];
				$img_new = image_reduire_net ($img, 0, $taille, $dpr);
			} else {
				$img_new = image_reduire_net ($img, $taille, 0, $dpr);
			}
			$img_new = extraire_attribut($img_new, "src");
			
			copy($img_new, $dest);
			if ($img_new != $img) unlink ($img_new);
		}
		$extension = str_replace("jpg", "jpeg", $terminaison);
		$expires = 60*60*24*14;
	
		if ($xsendfile == 1) {	
			//$dest = "/var/www/beach-fashion/$dest";
			//die($dest);
			header("X-Sendfile: $dest");
			header("Content-Type: image/".$extension);
			exit;
		} else {
			header("Content-Type: image/".$extension);
			header("Pragma: public");
			header("Cache-Control: maxage=".$expires);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
			header('Content-Length: '.filesize($dest));
	
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($dest)).' GMT', true, 200);
			readfile($dest);
		}

	}
	
}

?>
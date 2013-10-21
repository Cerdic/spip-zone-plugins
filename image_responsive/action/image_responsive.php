<?
function action_image_responsive() {

	$img = _request("img");
	$taille = _request("taille");

	if (file_exists($img)) {
		$terminaison = substr($img, strlen($img)-3, 3);
		$base = sous_repertoire(_DIR_VAR, "cache-responsive");
		$base = sous_repertoire($base, "cache-".$taille);
		$dest = md5($img);
		$dest = $base."/".$dest.".".$terminaison;

		if (file_exists($dest)) {
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
				strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($dest))
			{
				header('HTTP/1.0 304 Not Modified');
				exit;
			}
		}
		
		
		if (!file_exists($dest) OR filemtime($dest) < filemtime($img)) {
			include_spip("filtres/images_transforme");
			$img = image_reduire($img, $taille, 0);
			
			if (largeur($img) > 1.5*$taille) $img = image_renforcement($img, 0.1);
			
			$img = extraire_attribut($img, "src");
			
			copy($img, $dest);
		}
		$extension = str_replace("jpg", "jpeg", $terminaison);
		$browser_cache = 60*60*24*7;


		// Getting headers sent by the client.
		$headers = apache_request_headers(); 
	
		header("Content-Type: image/".$extension);

		header("Cache-Control: private, max-age=".$browser_cache);
		header('Expires: '.gmdate('D, d M Y H:i:s', time()+$browser_cache).' GMT');
		header('Content-Length: '.filesize($dest));

		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($dest)).' GMT', true, 200);
		readfile($dest);
	
				
	} else {
		return "Erreur";
	}
}

?>
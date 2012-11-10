<?php


function embed_url($url) {
	$max_w = 440;
	$max_i = 300;

	$url = str_replace("/#/", "/", $url);
	$url = str_replace("/#!/", "/", $url);


	$p = parse_url($url);
	$host = $p["host"];
	$host = preg_replace(",^www\.,", "", $host);
	$host = str_replace(".", "-", $host);

	$fichier = md5($url).".php";
	$dossier = substr(md5($url), 0, 3);
	
	// Si l'embed a deja été sauvegardé
	if (file_exists(_DIR_CACHE."$host/$dossier/$fichier")) {
		$html = implode("", file(_DIR_CACHE."$host/$dossier/$fichier"));
		if (strlen($html) > 0) return $html;
	} else { // Si pas sauvegardé
	
		// Créer dossier si nécessaire
		if (!is_dir(_DIR_CACHE."$host")) mkdir(_DIR_CACHE."$host");
		if (!is_dir(_DIR_CACHE."$host/$dossier")) mkdir(_DIR_CACHE."$host/$dossier");
	
		$f = fopen(_DIR_CACHE."$host/$dossier/$fichier", "w");
		
		// Gérer les images Flickr à part
		// car autoembed ne gère que les vidéos de Flickr
		if (preg_match("/^http\:\/\/(www\.)?flickr\.com/i", $url)) {
			$oembed = "http://www.flickr.com/services/oembed/?format=json&url=".$url;
			$json = @join("",file($oembed));
			
			$json = @json_decode($json);
			$img = $json->{'url'};
			if ($img) $code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Flickr' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
		}
		else if (preg_match("/^http\:\/\/(www\.)?pastebin\.com\/(.*)/i", $url, $regs)) {
			$val = $regs[2];

			
			/*
			$html = join("", file($url));
			$html = substr($html, strpos($html, '<div id="code_frame">'), strlen($html));
			$html = substr($html, 0, strpos($html, '<div class="content_title">'));
			
			$html = preg_replace(',<div id\=\"code_buttons\">(.*)<\/div>,sU', '', $html);
			if (preg_match(",<ol>(.*)<\/ol>,s", $html, $regs))
					$html = $regs[0];
			
			$html = trim($html);
			*/
			$html = "<iframe src='http://pastebin.com/embed_iframe.php?i=".$val."' style='border:none;width:100%;'></iframe>";
			//$html = "<script src='http://pastebin.com/embed_js.php?i=".$val."'></script>";
			$code_ae = "<div class='oembed-container oembed-code'>$html</div>";
			
		}
		else if (preg_match("/^https?\:\/\/gist\.github\.com\/(.*)/i", $url, $regs)) {
			$html = join("", file($url));
			
			$html = substr($html, strpos($html, '<pre>'), strlen($html));
			$html = substr($html, 0, strpos($html, '</pre>'));
			$html = trim($html);
			
			$code_ae = "<div class='oembed-container oembed-code'>$html</div>";
			
		}
		else if (preg_match("/^http\:\/\/(www\.)?yfrog\.com/i", $url)) {
			$oembed = "http://www.yfrog.com/api/oembed?url?format=json&url=".$url;
			$json = join("",file($oembed));
			$json = json_decode($json);
			$img = $json->{'url'};
			if ($img) $code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Flickr' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
		}
		else if (preg_match("/^http\:\/\/(www\.)?soundcloud\.com/i", $url)) {
			$oembed = "http://soundcloud.com/oembed/?format=json&url=".$url;
			$json = join("",file($oembed));
			$json = json_decode($json);
			$html = $json->{'html'};
			if ($html) $code_ae = "<div class='oembed-container'>$html</div>";	
		} 
		else if (preg_match("/^http\:\/\/(www\.)?slideshare\.net/i", $url)) {
			// Le JSON ne se décode pas correction,
			// je passe donc en XML
			$oembed = "http://www.slideshare.net/api/oembed/2?format=xml&url=".$url."&maxwidth=".$max_w;
			$xml = trim(join("",file($oembed)));
			if (preg_match(",<html>(.*)</html>,i", $xml, $regs)){
				$html = $regs[1];
				$html = html_entity_decode($html, ENT_QUOTES, "UTF-8");
				if ($html) $code_ae = "<div class='oembed-container'>$html</div>";	
				
			}
		} 
		else {
			require_once "AutoEmbed.class.php";
			$AE = new AutoEmbed();
	
	
			// load the embed source from a remote url
			if (!$AE->parseUrl($url)) {
				$code_ae = "";
				// No embeddable video found (or supported)
			} else {
				$AE->setParam('autoplay','false');
				
				
				$attributs = $AE->getObjectAttribs();
				$w = $attributs["width"];
				$h = $attributs["height"];
				
				//print_r($attributs);
				if ($w > $max_w) {
					$rapport = $w / $max_w;
					
					$w = round($w / $rapport);
					$h = round($h / $rapport);
					
					$AE->setWidth($w);
					$AE->setHeight($h);
				}	
				

				$embed = $AE->getEmbedCode();
				$vignette = $AE->getImageURL();
				
				$source = $AE->getStub("title");
								
				if ($source == "YouTube") {
					$embed = rawurlencode($embed);
					$embed = "<div onclick=\"$(this).html(decodeURIComponent('$embed'));\" style='width: ".$w."px; height: ".$h."px; background: url($vignette) center center; cursor: pointer;'></div>";
				} else if ($source == "Twitpic" OR $source == "500px") {
					$embed = "<a href='$url'><img src='$vignette' alt='' style='max-width:200px; max-height: 200px;' /></a>";
				}
				
				$code_ae = "<div class='oembed-container'>".$embed."</div>";	
				
				
			}
		}
		
		fwrite($f, $code_ae);
		fclose($f);

		return $code_ae;
	}
}

?>
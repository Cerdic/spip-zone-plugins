<?php


function embed_url($url) {
	$max_w = 440;
	$max_i = 300;


	$p = parse_url($url);
	$host = $p["host"];
	$host = preg_replace(",^www\.,", "", $host);
	$host = str_replace(".", "-", $host);

	$fichier = md5($url).".php";
	$dossier = substr(md5($url), 0, 3);
	
	// Si l'embed a deja été sauvegardé
	if (file_exists("cache/$host/$dossier/$fichier")) {
		$html = implode("", file("cache/$host/$dossier/$fichier"));
		echo $html;
		
	} else { // Si pas sauvegardé
	
		// Créer dossier si nécessaire
		if (!is_dir("cache/$host")) mkdir("cache/$host");
		if (!is_dir("cache/$host/$dossier")) mkdir("cache/$host/$dossier");
	
		$f = fopen("cache/$host/$dossier/$fichier", "w");
		
		// Gérer les images Flickr à part
		// car autoembed ne gère que les vidéos de Flickr
		if (preg_match("/^http\:\/\/(www\.)?flickr\.com/i", $url)) {
			$oembed = "http://www.flickr.com/services/oembed/?format=json&url=".$url;
			$json = join("",file($oembed));
			
			$json = json_decode($json);
			$img = $json->{'url'};
			if ($img) $code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Flickr' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
		}
		else if (preg_match("/^http\:\/\/(www\.)?pastebin\.com\/(.*)/i", $url, $regs)) {
			$val = $regs[2];
			$html = join("", file($url));
			
			$html = substr($html, strpos($html, '<div id="code_frame">'), strlen($html));
			$html = substr($html, 0, strpos($html, '<div class="content_title">'));
			$html = trim($html);
			
			$code_ae = "<div class='oembed-container oembed-code'>$html</div>";
			
		}
		else if (preg_match("/^https?\:\/\/gist\.github\.com\/(.*)/i", $url, $regs)) {
			$html = join("", file($url));
			
			$html = substr($html, strpos($html, '<pre>'), strlen($html));
			$html = substr($html, 0, strpos($html, '</pre>'));
			$html = trim($html);
			
			$code_ae = "<div class='oembed-container oembed-code'>$html</div>";
			
		}
		else if (preg_match("/^http\:\/\/(www\.)?twitpic\.com/i", $url)) {
			$html = join("", file($url));
			

			if (preg_match(",http://(hot)?proxy[0-9]+.twitpic.com/photos/(full|large)/[a-z0-9]+.(jpg|gif|png),i", $html, $regs)){
				$img =$regs[0];	
				$code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Twitpic' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
			}
			else if (preg_match(",http://s[0-9]+.amazonaws.com/twitpic/photos/(full|large)/[a-z0-9]+.(jpg|gif|png)\?[^'\"]*,i", $html, $regs)){
				$img =$regs[0];	
				$code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Twitpic' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
			}
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
			$url = str_replace("/#/", "/", $url);
		
			include "AutoEmbed.class.php";
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
				
				if ($w > $max_w) {
					$rapport = $w / $max_w;
					
					$w = round($w / $rapport);
					$h = round($h / $rapport);
					
					$AE->setWidth($w);
					$AE->setHeight($h);
					
				}	
				
				
				$embed = $AE->getEmbedCode();
				$vignette = $AE->getImageURL();
				if (strlen($vignette) >  5) {
					$embed = rawurlencode($embed);
					$embed = "<div onclick=\"$(this).html(decodeURIComponent('$embed'));\" style='width: ".$w."px; height: ".$h."px; background: url($vignette) center center; cursor: pointer;'></div>";
				}
				
				$code_ae = "<div class='oembed-container'>".$embed."</div>";	
				
				
			}
		}
		echo $code_ae;
		
		fwrite($f, $code_ae);
		fclose($f);
	}
}

//header('Content-Type: text/xml');


$url = $_GET["url"];
embed_url($url);


?>
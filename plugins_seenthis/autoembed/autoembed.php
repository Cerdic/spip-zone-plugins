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

	// Gérer les elements de dropbox (remplacer www par dl)
	$url = preg_replace("/^(https\:\/\/)(www)(\.dropbox\.com\/.*\/.*\/.*?)(\?dl=[01])?$/", '\1dl\3', $url);
	
	// Gérer les elements de commons.wikimedia
	if (preg_match("/^https?\:\/\/commons\.wikimedia\.org\/wiki\/File\:(.*)/i", $url, $regs)) {
		$md5 = md5($regs[1]);
		$url = 'https://upload.wikimedia.org/wikipedia/commons/' . $md5[0] . '/' . $md5[0] . $md5[1] . '/' . urlencode($regs[1]);
	}
	
	// Si l'embed a deja été sauvegardé
	if (file_exists(_DIR_CACHE."$host/$dossier/$fichier")) {
		$html = implode("", file(_DIR_CACHE."$host/$dossier/$fichier"));
		if (strlen($html) > 0) return $html;
	} else { // Si pas sauvegardé
		// Gérer les images Flickr à part
		// car autoembed ne gère que les vidéos de Flickr
		// sets
		if (preg_match(",^https?\://(www\.)?flickr\.com/+photos/+[^/]*/+sets/[^/]+,i", $url, $r)) {
			if ($page = @join("",file($r[0]))) {
				if (preg_match(',<meta property="og:image" content="(.*)" />,', $page, $i1)) {
					$img = $i1[1];
					$code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Flickr' style='max-width: ".$max_w."px; max-height: ".$max_i."px;'/></a></div>";
				}
			}
		}
		if (preg_match("/^https?\:\/\/(www\.)?flickr\.com/i", $url)) {
			$oembed = "http://www.flickr.com/services/oembed/?format=json&url=".$url;
			$json = @join("",file($oembed));
			
			$json = @json_decode($json);
			$img = $json->{'url'};
			if ($img) $code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Flickr' style='max-width: ".$max_i."px; max-height: ".$max_i."px;'/></a></div>";	
		}
		else if (preg_match(",^https?\://(www\.)?instagram\.com/p/([a-z0-9]*)/,i", $url, $r)) {
			if ($page = @join("",file($r[0]))) {
				if (preg_match(',<meta property="og:image" content="(.*)" />,', $page, $i1)) {
					$img = $i1[1];
					$code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' alt='Instagram' style='max-width: ".$max_w."px; max-height: ".$max_i."px;'/></a></div>";
				}
			}
		}
		else if (preg_match("/^http\:\/\/(www\.)?pastebin\.com\/(.*)/i", $url, $regs)) {
			$val = $regs[2];

			$html = "<iframe src='http://pastebin.com/embed_iframe.php?i=".$val."' style='border:none;width:100%;'></iframe>";
			//$html = "<script src='http://pastebin.com/embed_js.php?i=".$val."'></script>";
			$code_ae = "<div class='oembed-container oembed-code'>$html</div>";
			
		}
		else if (preg_match("/^https?\:\/\/gist\.github\.com\/(.*)/i", $url, $regs)) {
			$html = file_get_contents($url);
			$tag = 'pre'; # extraire_balise
			if (preg_match(
			",<$tag\b[^>]*(/>|>.*</$tag\b[^>]*>|>),UimsS",
			$html, $regs)) {
				$pre = $regs[0];
				$code_ae = "<div class='oembed-container oembed-code'>$pre</div>";
			}
		}
		else if (preg_match("/^https?\:\/\/(bl\.ocks|blockbuilder)\.org\/(\w+\/\w+)/i", $url, $regs)) {
			$urlb = "https://bl.ocks.org/".$regs[2];
			$page = file_get_contents($urlb);
			if ($page) {
				if (preg_match(',<meta property="og:image" content="(.*)",Uims', $page, $i1)) {
					$thumbnail = $i1[1];
				}
				if (preg_match(',<meta property="og:title" content="(.*)",Uims', $page, $i1)) {
					$title = $i1[1];
				}
				if (preg_match(',<meta property="og:description" content="(.*)",Uims', $page, $i1)) {
					$author = preg_replace('/’s block.*$/i', '', $i1[1]);
				}
				
				if ($thumbnail) {
					$pre = "<figure>
					<a href=\"$url\" target=\"_blank\"><img src=\"$thumbnail\"></a>
					<figcaption>
						<a href=\"$url\">$title</a> <em class=\"author\"> - $author</em>
					</figcaption>
					</figure>
					";
				} else {
					$pre = "<a href=\"$url\" target=\"_blank\">$title</a> <em class=\"author\"> - $author</em>";
				}
				
				$code_ae = "<div class='oembed-container oembed-block'>$pre</div>";
			}
		}
		else if (preg_match("/^https?\:\/\/(www\.)?soundcloud\.com/i", $url)) {
			$oembed = "http://soundcloud.com/oembed?format=json&url=".$url;
			$json = join("",file($oembed));
			$json = json_decode($json);
			$html = $json->{'html'};
			if ($html) $code_ae = "<div class='oembed-container'>$html</div>";	
		} 
		else if (preg_match("/^http\:\/\/(www\.)?prezi\.com\/([^\/]+)\//i", $url, $r)) {
			$oembed = "http://prezi.com/api/embed/?id=".$r[2];
			$json = join("",file($oembed));
			$json = json_decode($json);
			$img = $json->{'embed_preview'};
			if ($img) $code_ae = "<div class='oembed-container oembed-img'><a href='$url'><img src='$img' title='".str_replace("'", "&#39;", $json->{'presentation'}->{'title'})."' /></a></div>";
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
		else if (preg_match(",^https?://[^\"\'\`\<\>\@\*\$]*?\.mp3(\?.*)?$,i", $url)) {
			$html = file_get_contents(dirname(__FILE__).'/modeles/mp3.html');
			$html = str_replace('{source}', htmlspecialchars($url), $html);
			$url_dewplayer = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/autoembed/modeles/dewplayer.swf';
			$html = str_replace('{dewplayer}', $url_dewplayer, $html);
			if ($html) $code_ae = "<div class='oembed-container'>$html</div>";
		}
		else if (preg_match(",^https?://[^\"\'\`\<\>\@\*\$]*?\.ogg(\?.*)?$,i", $url)) {
			$html = "<div class='audio'><audio controls><source src='$url' rel='enclosure'></audio></div>";
			if ($html) $code_ae = "<div class='oembed-container'>$html</div>";
		}
		else if (preg_match(",^https?://[^\"\'\`\<\>\@\*\$]*?\.mp4(\?.*)?$,i", $url)) {
			$html = "<div class='video' style='height:0;position: relative; padding-bottom: 56.25%;'><video controls style='max-width: 100%;max-height: 100%;position:absolute'><source src='$url' rel='enclosure'></video></div>";
			if ($html) $code_ae = "<div class='oembed-container'>$html</div>";

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
					$embed = "<div onclick=\"this.innerHTML = (decodeURIComponent('$embed'));\" style='position: relative; width:100%; height:0; padding-bottom:56.3636363636%; background: url($vignette) center center; cursor: pointer;'></div>"; 
				}
								
				if ($source == "Twitpic" OR $source == "500px") {
					$embed = "<a href='$url'><img src='$vignette' alt='' style='max-width:200px; max-height: 200px;' /></a>";
				}

				// inserer une "class=oembed-source-mp3audio" 
				$src = preg_replace(',[^\w]+,', '', strtolower($source));

				$code_ae = "<div class='oembed-container oembed-source-$src'>".$embed."</div>";
				
				
			}
		}
		
		if ($code_ae) {
			// Créer dossier si nécessaire
			if (!is_dir(_DIR_CACHE."$host")) mkdir(_DIR_CACHE."$host");
			if (!is_dir(_DIR_CACHE."$host/$dossier")) mkdir(_DIR_CACHE."$host/$dossier");
		
			$f = fopen(_DIR_CACHE."$host/$dossier/$fichier", "w");

			fwrite($f, $code_ae);
			fclose($f);
		}

		return $code_ae;
	}
}

?>
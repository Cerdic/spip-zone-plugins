<?php

// http://doc.spip.org/@compacte_css
function compacte_css ($contenu) {
	// nettoyer la css de tout ce qui sert pas
	$contenu = preg_replace(",/\*.*\*/,Ums","",$contenu); // pas de commentaires
	$contenu = preg_replace(",\s(?=\s),Ums","",$contenu); // pas d'espaces consecutifs
	$contenu = preg_replace("/\s?({|;|,|:)\s?/ms","$1",$contenu); // pas d'espaces dans les declarations css
	$contenu = preg_replace("/\s}/ms","}",$contenu); // pas d'espaces dans les declarations css
	$contenu = preg_replace(",#([0-9a-f])(\\1)([0-9a-f])(\\3)([0-9a-f])(\\5),i","#$1$3$5",$contenu); // passser les codes couleurs en 3 car si possible
	$contenu = preg_replace(",([^{}]*){},Ums"," ",$contenu); // supprimer les declarations vides
	$contenu = trim($contenu);

	return $contenu;
}

// Compacte du javascript grace a Dean Edward's JavaScriptPacker
// utile pour prive/jquery.js par exemple
// http://doc.spip.org/@compacte_js
function compacte_js($flux) {
	if (!strlen($flux))
		return $flux;
	include_spip('lib/JavascriptPacker/class.JavaScriptPacker');
	$packer = new JavaScriptPacker($flux, 0, true, false);

	// en cas d'echec (?) renvoyer l'original
	if (strlen($t = $packer->pack()))
		return $t;

	// erreur
	spip_log('erreur de compacte_js');
	return $flux;
}


// Appelee par compacte_head() si le webmestre le desire, cette fonction
// compacte les scripts js dans un fichier statique pose dans local/
// en entree : un <head> html.
// http://doc.spip.org/@compacte_head_js
function compacte_head_js($flux) {
	$url_base = url_de_base();
	$url_page = substr(generer_url_public('A'), 0, -1);
	$dir = preg_quote($url_page,',').'|'.preg_quote(preg_replace(",^$url_base,",_DIR_RACINE,$url_page),',');

	$scripts = array();
	$flux_nocomment = preg_replace(",<!--.*-->,Uims","",$flux);
	foreach (extraire_balises($flux_nocomment,'script') as $s) {
		if (extraire_attribut($s, 'type') === 'text/javascript'
		AND $src = extraire_attribut($s, 'src')
		AND !strlen(strip_tags($s))
		AND (
			preg_match(',^('.$dir.')(.*)$,', $src, $r)
			OR (
				// ou si c'est un fichier
				$src = preg_replace(',^'.preg_quote(url_de_base(),',').',', '', $src)
				// enlever un timestamp eventuel derriere un nom de fichier statique
				AND $src2 = preg_replace(",[.]js[?].+$,",'.js',$src)
				// verifier qu'il n'y a pas de ../ ni / au debut (securite)
				AND !preg_match(',(^/|\.\.),', substr($src,strlen(_DIR_RACINE)))
				// et si il est lisible
				AND @is_readable($src2)
			)
		)) {
			if ($r)
				$scripts[$s] = explode('&',
					str_replace('&amp;', '&', $r[2]), 2);
			else
				$scripts[$s] = $src;
		}
	}
	
	if (list($src,$comms) = filtre_cache_static($scripts,'js')){
		$scripts = array_keys($scripts);
		$flux = str_replace(reset($scripts),
			$comms
			."<script type='text/javascript' src='$src'></script>\n",$flux);
		$flux = str_replace($scripts,"",$flux);
	}

	return $flux;
}

// Appelee par compacte_head() si le webmestre le desire, cette fonction
// compacte les feuilles de style css dans un fichier statique pose dans local/
// en entree : un <head> html.
// http://doc.spip.org/@compacte_head_css
function compacte_head_css($flux) {
	$url_base = url_de_base();
	$url_page = substr(generer_url_public('A'), 0, -1);
	$dir = preg_quote($url_page,',').'|'.preg_quote(preg_replace(",^$url_base,",_DIR_RACINE,$url_page),',');

	$css = array();
	$flux_nocomment = preg_replace(",<!--.*-->,Uims","",$flux);
	foreach (extraire_balises($flux_nocomment, 'link') as $s) {
		if (extraire_attribut($s, 'rel') === 'stylesheet'
		AND (!($type = extraire_attribut($s, 'type'))
			OR $type == 'text/css')
		AND is_null(extraire_attribut($s, 'name')) # css nommee : pas touche
		AND is_null(extraire_attribut($s, 'id'))   # idem
		AND !strlen(strip_tags($s))
		AND $src = preg_replace(",^$url_base,",_DIR_RACINE,extraire_attribut($s, 'href'))
		AND (
			// regarder si c'est du format spip.php?page=xxx
			preg_match(',^('.$dir.')(.*)$,', $src, $r)
			OR (
				// ou si c'est un fichier
				// enlever un timestamp eventuel derriere un nom de fichier statique
				$src2 = preg_replace(",[.]css[?].+$,",'.css',$src)
				// verifier qu'il n'y a pas de ../ ni / au debut (securite)
				AND !preg_match(',(^/|\.\.),', substr($src2,strlen(_DIR_RACINE)))
				// et si il est lisible
				AND @is_readable($src2)
			)
		)) {
			$media = strval(extraire_attribut($s, 'media'));
			if ($r)
				$css[$media][$s] = explode('&',
					str_replace('&amp;', '&', $r[2]), 2);
			else
				$css[$media][$s] = $src;
		}
	}

	// et mettre le tout dans un cache statique
	foreach($css as $m=>$s){
		// si plus d'une css pour ce media ou si c'est une css dynamique
		if (count($s)>1 OR is_array(reset($s))){
			if (list($src,$comms) = filtre_cache_static($s,'css')){
				$s = array_keys($s);
				$flux = str_replace(reset($s),
					$comms
					."<link rel='stylesheet'".($m?" media='$m'":"")." href='$src' type='text/css' />\n",$flux);
				$flux = str_replace($s,"",$flux);
			}
		}
	}

	return $flux;
}


// http://doc.spip.org/@filtre_cache_static
function filtre_cache_static($scripts,$type='js'){
	$nom = "";
	if (!is_array($scripts) && $scripts) $scripts = array($scripts);
	if (count($scripts)){
		$dir = sous_repertoire(_DIR_VAR,'cache-'.$type);
		$nom = $dir . md5(serialize($scripts)) . ".$type";
		if (
		  $GLOBALS['var_mode']=='calcul'
		  OR $GLOBALS['var_mode']=='recalcul'
		  OR !file_exists($nom)){
		  	$fichier = "";
		  	$comms = array();
		  	$total = 0;
		  	foreach($scripts as $script){
		  		if (!is_array($script)) {
		  			// c'est un fichier
		  			$comm = $script;
		  			// enlever le timestamp si besoin
		  			$script = preg_replace(",[?].+$,",'',$script);
				  	if ($type=='css')
				  		$script = url_absolue_css($script);
		  			lire_fichier($script, $contenu);
		  		}
		  		else {
		  			// c'est un squelette
		  			$comm = _SPIP_PAGE . "=$script[0]"
		  				. (strlen($script[1])?"($script[1])":'');
		  			parse_str($script[1],$contexte);
		  			$contenu = recuperer_fond($script[0],$contexte);
		  			if ($type=='css')
						$contenu = urls_absolues_css($contenu, self('&'));
		  		}
				$f = 'compacte_'.$type;
	  			$fichier .= "/* $comm */\n". $f($contenu) . "\n\n";
				$comms[] = $comm;
				$total += strlen($contenu);
		  	}

			// calcul du % de compactage
			$pc = intval(1000*strlen($fichier)/$total)/10;
			$comms = "compact [\n\t".join("\n\t", $comms)."\n] $pc%";
			$fichier = "/* $comms */\n\n".$fichier;

		  	// ecrire
		  	ecrire_fichier($nom,$fichier);
		  	// ecrire une version .gz pour content-negociation par apache, cf. [11539]
		  	ecrire_fichier("$nom.gz",$fichier);
		  }
	}

	// Le commentaire detaille n'apparait qu'au recalcul, pour debug
	return array($nom, (isset($comms) AND $comms) ? "<!-- $comms -->\n" : '');
}

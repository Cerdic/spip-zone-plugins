<?php
include_spip('inc/config') ;
include_spip('inc/texte') ; // nécessaire pour disposer de la fonction 'propre'
include_spip('outils/smileys');
include_spip('inc/ckeditor_lire_config') ;
include_spip('inc/ckeditor_constantes');

$closedtags = $protectedtags = array() ;
global $cke_tags ; $cke_tags = lire_config('ckeditor/tags') ;
if (! is_array($cke_tags)) { $cke_tags = unserialize(_CKE_TAGS_DEF) ; ecrire_config('ckeditor/tags', $cke_tags) ; }
foreach($cke_tags as $tagname => $tagdesc) {
	switch($tagdesc['type']) {
		case 'num-obligatoire': $num = '\\d+' ; break ;
		case 'num-facultatif': $num = '\\d*' ; break ;
		default: $num = '' ; break ;
	} 
	$protectedtags[] = $tagname.$num ;
	if ($tagdesc['fermante']) { $protectedtags[] = '\\/'.$tagname ; $closedtags[] = $tagname ; }
}
define( 'PROTECTED_SPIP_TAGS', "(?:".join('|', $protectedtags).")" );unset($protectedtags);
define( 'CLOSED_PROTECTED_SPIP_TAGS', "(?:".join('|', $closedtags).")" );unset($closedtags);

function ckeditor_efface_repertoire($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? ckeditor_efface_repertoire("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  } 

function ckeditor_ecrire_protectedtags($tags=null) {
		if (! is_array($tags)) { $tags = lire_config('ckeditor/tags') ; }
		if (! is_array($tags)) { $tags = unserialize(_CKE_TAGS_DEF) ; }

		$ptags= array() ;
		foreach($tags as $tag => $desc) {
			$ptags[] = $tag . ($desc['type']=='num-obligatoire'?'XX':($desc['type']=='num-facultatif'?'xx':'')) ;
			if ($desc['fermante']) $ptags[] = '/'.$tag ;
		}
		ecrire_config('ckeditor/protectedtags', join(';', $ptags)) ;
}

global $cke_conf_cs ; $cke_conf_cs = unserialize($GLOBALS['meta']['tweaks_actifs']) ; // pour éviter les unserialize à répétition

function ckeditor_tweaks_actifs($tweak) { // pour accéder aux outils du couteau suisse
	global $cke_conf_cs ;
	return is_array($cke_conf_cs) && is_array($cke_conf_cs[$tweak]) && $cke_conf_cs[$tweak]['actif'] ;
}

function ckeditor_dump($var, $html = true ) { // pour afficher le contenu d'une variable (pour débuggage)
	ob_start() ;
		var_dump($var);
		$content = ob_get_contents() ;
	ob_end_clean() ;
	if ($html) 
		return "<pre>".htmlentities($content)."</pre>" ;
	else 
		return $content ;
}

function ckeditor_traite_lien_html($texte, $lien, $avant, $apres) {
	/* 
	 * Recuperation d'un eventuel title="whatever"
	 */
	$titre = '';
	$title_regex = "/\s?title=[\"']([^\"']*)[\"']\s?/i";

	if (($avant && preg_match($title_regex, stripslashes($avant), $m)) ||
	    ($apres && preg_match($title_regex, stripslashes($apres), $m))) {
		$titre = "|".htmlspecialchars_decode(str_replace("\\'",
								 "'", $m[1]),
						     ENT_QUOTES);
		/* 
		 * Et si le avant/apres ne contenait que 
		 * ca, on purge pour eviter le cas 2 dans
		 * le test qui suit.
		 */
		if ($avant == $m[0])
			$avant = '';

		if ($apres == $m[0])
			$apres = '';
	}

	$texte=preg_replace('~\\\(.)~',"$1",$texte);
	if (preg_match("~spip\.php\?page=(\w+)(?:&amp;amp;|&amp;|&)id_\\1=(\d+)(#\w+)?$~", $lien, $match)) {
		return "[".strip_tags($texte,"<strong><em><i><span><img><sub><sup>").$titre."->".$match[1]." ".$match[2].$match[3]."]" ;
	} else if ($avant || $apres) {
		return "<a ".($avant?stripslashes($avant).' ':'')."href='$lien'".($apres?' '.stripslashes($apres):'').">$texte</a>" ;
	} else {
		return "[".strip_tags($texte,"<strong><em><i><span><img><sub><sup>").$titre."->".$lien."]" ;
	}
}

function ckeditor_traite_img_html($docid, $doctype, $docparam, $avant, $apres) {
	$regex = '~align=(["\'])(\w+)\\1~' ;
	$align = '' ; // par défaut : rien
	if (preg_match($regex, stripslashes($avant), $match) || preg_match($regex, stripslashes($apres), $match)) {
		switch ($match[2]) { // on accepte gauche et droite
			case 'middle':
				$align = '|center';
				break;
			case 'left':
			case 'right':
				$align = '|'.$match[2] ;
				break ;
		}
	}else{
		$regex = '~style=(["\'])(.*?)\\1~' ;
		if (preg_match($regex, stripslashes($avant), $match) || preg_match($regex, stripslashes($apres), $match)) {
			// on a un style
			if (preg_match('~\bfloat\s*:\s*(\w+)\s*;~', $match[2], $match)) {
				switch ($match[1]) {
					case 'left':
					case 'right':
						$align = '|'.$match[1] ;
						break ;
				}
			}
		}
	}
	return "<${doctype}${docid}".($docparam?'|'.urldecode($docparam):'').$align.">" ;
}

function ckeditor_traite_img_data($data, $avant, $apres) {
	// gestion des images incorporées dans le html
	$regex = '~^image/([^;]+);base64,(.*)$~' ;
	if (preg_match($regex, stripslashes($data), $match)) {
		if (!is_dir(_DIR_IMG.$match[1])) {
			mkdir(_DIR_IMG.$match[1]) ;
		}
		while (file_exists($filename=_DIR_IMG.$match[1].'/ckeditor-img-'.md5(date('l j F Y, G:i:s.u').rand()).'.'.$match[1])) { srand(); }
		$fh = @fopen($filename, 'w');
		if ($fh) {
			fwrite($fh, base64_decode(preg_replace('~\s~s','',trim($match[2])))) ;
			fclose($fh);
			return '<img '.stripslashes($avant).' src="'.preg_replace("~^https?://[^/]*~",'',url_absolue($filename)).'" '.stripslashes($apres).' />' ; // on retourne l'image sauvegardée
		} else {
			return '' ; // on supprime l'image s'il est impossible de la sauvegarder : on ne sais pas quoi en faire...
		}
	} else {
		return '' ; // on supprime une image non correctement encodée en base64
	}
}

function ckeditor_traite_lien_spip($texte,$lien,$titre = '') {
	if ($titre)
		$titre = sprintf(" title='%s'", 
				 htmlspecialchars(str_replace('\\\'', "'", 
							      $titre),
						  ENT_QUOTES));

	$texte = preg_replace("~\\\'~","'",$texte);
	if (preg_match("~^(art(?:icle)?|br(?:eve)?|rub(?:rique)?|[a-zA-Z]+)\s*(\d+)\s*(#\w+)?$~", $lien, $match)) {
		switch ($match[1]) {
			case 'rub':
				$type = 'rubrique' ;
				break ;
	 		case 'art':
				$type = 'article' ;
				break ;
			case 'br':
				$type = 'breve' ;
				break ;
			default:
				$type = $match[1] ;
		}


		return "<a href='"._DIR_RACINE."spip.php?page=$type&amp;id_$type=".$match[2].$match[3]."'".$titre.">".$texte."</a>" ;
	} else {
		return "<a href='$lien'$titre>$texte</a>" ;
	}
}

function ckeditor_traite_img_spip($doctype, $docid, $align) {
	static $cache = array() ;
	if (! $row = $cache[$docid]) { // on limite les accès à la db
		$cache[$docid] = $row = sql_fetsel("fichier,largeur,hauteur,extension", "spip_documents", "id_document=$docid");
	}
	switch ($row['extension']) {
		case 'jpg':
		case 'jpeg' :
		case 'gif':
		case 'png':
			if(!preg_match(',^\w+://,',$row['fichier'])) // s'il y a déjà un protocole, il ne faut pas ajouter le chemin vers $dir_img
				$row['fichier'] = url_absolue(_DIR_IMG.$row['fichier']) ;
			break ;
		default:
			$f = charger_fonction('vignette','inc');
                        $v = $f($row['extension'], true);
			if ($v[0]) {
				$row['fichier'] = url_absolue($v[0]) ;
				if (!$row['largeur'])
					$row['largeur'] = $v[1] ;
				if (!$row['hauteur'])
					$row['hauteur'] = $v[2] ;
			}
			break ;
	}
	$preview = ckeditor_lire_config('vignette', _CKE_VIGNETTE_DEF) ;
	if ($preview && $row['largeur'] && $row['hauteur']){
		if (($row['largeur'] > $row['hauteur']) && ($row['largeur'] > $preview)) {
			$larg = ' width="'.$preview.'px"' ;
			$haut = sprintf(" height=\"%.0dpx\"",$preview * $row['hauteur'] / $row['largeur']) ;
		} else
	  	if (($row['largeur'] < $row['hauteur']) && ($row['hauteur'] > $preview))	{
			$haut = ' height="'.$preview.'px"' ;
			$larg = sprintf(" width=\"%.0dpx\"",$preview * $row['largeur'] / $row['hauteur']) ;
		} else {
			$haut = ' height="'.$row['hauteur'].'px"' ;
			$larg = ' width="'.$row['largeur'].'px"' ;
		}
	} else {
		$larg = '' ;
		$haut = '' ;
	}
	$params = preg_split("/\|/", $align) ;
	$align = '' ;
	$docparams = array() ;
	foreach($params as $param) {
		switch ($param) {
			case 'center': 
				$align = 'middle' ;
				$center= ' style="display: block; margin-left: auto; margin-right: auto;"' ;
				break ;
			case 'left':
				$center= '' ;
				$align = $param ;
				break ;
			case 'right':
				$center= '' ;
				$align = $param ;
				break ;
			default:
				$docparams[] = $param ;
				break ;
		}
	}
	if (count($docparams)) {
		$docparam='&docparam='.join('%7C', $docparams) ;
	} else {
		$docparam='' ;
	}
	return '<img'.$larg.$haut.' align="'.$align.'" src="'.$row['fichier'].'?docid='.$docid.'&doctype='.$doctype.$docparam.'"'.$center.'/>' ;
}

function ckeditor_wrap_callback($matches) {
	$replace = sprintf("<script type=\"ckeditor_wrap\">%s</script>",
			   urlencode($matches[0]));
	return $replace;
}

function ckeditor_unwrap_callback($matches) {
	$replace = urldecode($matches[1]);
	return $replace;
}


function ckeditor_html2spip_pre_dist($texte) {
	return $texte;
}

function ckeditor_html2spip_post_dist($texte) {
	return $texte;
}

function ckeditor_tag_protect($code,$tag,$params) {
	return "<".stripslashes($tag).stripslashes($params).">".
		preg_replace(
		array(
			/* 1 */ '~<br/?>(\n|\r|\s)*~is',
			/* 2 */ '~(&nbsp;|&#160;)~is'
		),
		array(
			/* 1 */ "\n",
			/* 2 */ ' '
		),
		stripslashes($code)).
		"</".stripslashes($tag).">";
}

function ckeditor_outtag_protect($code,$echappe) {
	return (echappe?preg_replace(
			array(
				/* 1 */ "~(".join('|',array_map('preg_quote',str_split($echappe._CKE_DOUBLE_PONCTUATION))).")~e",
				/* 2 */ "~(\n\r|\r\n|\n|\r)~s",
				/* 3 */ "~[ \t](?!;)~",
				/* 4 */ "~&nbsp;~"
			),
			array(
				/* 1 */ "'&#'.ord('$1').';'",
				/* 2 */ "<br/>",
				/* 3 */ "&#160;", /* espace insécable codé en utf8, &nbsp; semble 'mangé' par le filtre */
				/* 4 */ "&#160;"
			), $code):$code) ;
}

function ckeditor_tag_unprotect($code,$tag,$params) {
	global $cke_tags ;

	$prefix = '<p>' ; $postfix = '</p>' ;
		
	if (preg_match_all( /* on recherche les tags imbriqués */
		"#&lt;((".CLOSED_PROTECTED_SPIP_TAGS.")-protected)(.*?)&gt;(.*?)&lt;/\\1&gt;#se",
		$stripcode = stripslashes($code), $matches, PREG_OFFSET_CAPTURE)) {
		$len = 0 ;
		$result = $prefix . "&lt;".stripslashes($tag).stripslashes($params)."&gt;" ;
		$cpt = 0 ;
		$pos = 0 ;
		foreach($matches[1] as $key => $match) {
			$cpt++ ;
			$result .= ckeditor_outtag_protect(substr($stripcode,$pos,$matches[0][$key][1]-$pos),$cke_tags[$tag]['echappe_car']).
				ckeditor_tag_unprotect($matches[4][$key][0],$matches[2][$key][0], $matches[3][$key][0]) ;
			$pos = $matches[0][$key][1] + strlen($matches[0][$key][0]) ;
		}
		$result .= ckeditor_outtag_protect(substr($stripcode,$pos),$cke_tags[$tag]['echappe_car']) ;
		return $result."&lt;/".stripslashes($tag)."&gt;" . $postfix ;
	} else {
		return $prefix . "&lt;".stripslashes("$tag").stripslashes($params)."&gt;".stripslashes(ckeditor_outtag_protect($code,$cke_tags[$tag]['echappe_car']))."&lt;/".stripslashes($tag)."&gt;" . $postfix ;
	}
}

function ckeditor_html2spip($texte) {
	$ckeditor_html2spip_pre = charger_fonction('ckeditor_html2spip_pre','');
	$texte = $ckeditor_html2spip_pre($texte);

	$search[] = "~<br/?>(\s|\r|\n)*</li>(\s|\r|\n)*~" ; // fix: http://contrib.spip.net/CKeditor-3-0#forum468504
	$replace[] = "</li>" ;

	if (PROTECTED_SPIP_TAGS) {
		$search[] = "#&lt;(".PROTECTED_SPIP_TAGS.".*?)&gt;#s" ;
		$replace[] = "<$1>" ;
	}
	if (CLOSED_PROTECTED_SPIP_TAGS) {
		$search[] = "#<(".CLOSED_PROTECTED_SPIP_TAGS.")([^>]*)>(.*?)</\\1>#se" ;
		$replace[] = "ckeditor_tag_protect('$3','$1','$2')" ;
	}

	if (ckeditor_tweaks_actifs('decoupe')) {
		$search[] = "#\s*<div\s*style=\"page-break-after:\s*always\s*;\s*\">.*?</div>\s*#si" ; // saut de page
		if (ckeditor_lire_config("html2spip", _CKE_HTML2SPIP_DEF)) {
			$replace[] = "\n\n<p>++++</p>\n" ;
		} else {
			$replace[] = "\n\n++++\n\n" ;
		}
	}

	$search[] = "#<a\s+([^>]*?)\s*href=(\"|')(.*?)\\2\s*([^>]*?)\s*>(.*?)</a>#sei" ; // les liens
	$replace[] = "ckeditor_traite_lien_html('$5','$3','$1','$4')" ;

	$search[] = "#<a[^>]+name=(\"|')(.*?)\\1[^>]*></a>#si" ; // les ancres
	$replace[] = '[#$2<-]' ;

	$search[] = "#<img\s*([^>]*?)\s*src=\"([^\"]*?)\?docid=(\d+)(?:&amp;|&)doctype=(\w+)(?:(?:&amp;|&)docparam=([^\"]*))?\"\s*([^>]*?)\s*>#sei" ; // les images
	$replace[] = "ckeditor_traite_img_html('$3','$4','$5','$1','$6')" ;

	$search[] = "#<img\s*([^>]*?)\s*src=\"data:([^\"]*?)\"\s*([^>]*?)\s*>#sei" ; // les images incorporées/encodées en base64
	$replace[] = "ckeditor_traite_img_data('$2','$1','$3')" ;

	// nettoyage des attribus ajoutés par ckeditor
	$search[] = "#(<\w+\s*[^>]*\b)data-cke-saved-\w+=([\"']).*?\\2([^>]*>)#si" ;
	$replace[] = "$1$3" ;
	
	if (ckeditor_tweaks_actifs('smileys')) {
		$cs_path = preg_split("~/~", _DIR_PLUGIN_COUTEAU_SUISSE) ;
		$search[] = "#<img[^>]+src=\"[^\"]*".$cs_path[count($cs_path)-1]."/img/smileys/[^\"]*\"[^>]+title=\"([^\"]*)\"[^>]+/>#si" ;
		$replace[] = "$1" ;
	}

	if (ckeditor_lire_config("spiplinks")) {
		$search[] = "#(\[[^\]]*?-)&gt;([^\]]*?\])#s" ; // les liens spip
		$replace[] = "$1>$2" ;

		$search[] = "#(\[.*?)&lt;(-\])#s" ; // les ancres spip
		$replace[] = "$1<$2" ;
	}

	$search[] = "#<br/?>(\r|\n|\s)*<(td|caption|tr|tbody|/td|/caption|/tr|/tbody)[^>]*>(\r|\n|\s)*#si" ;
	$replace[] = "<$2>" ;

	/* plus de nettoyage : */
	$search[] = "#(\s*<p>\s*</p>)*\s*$#s" ;
	$replace[] = '' ;

	$search[] = '~<br/?>$~' ;
	$replace[] = '' ;

	$texte = preg_replace($search, $replace, $texte) ;

	if (ckeditor_lire_config("conversion", _CKE_CONVERSION_DEF) == 'complete') {
		/*
		 * Protection des modeles SPIP dans des
		 * <script type="ckeditor_wrap"> pour que le parser HTML
		 * ne bloque pas dessus comme non compliant HTML.
		 */
		$search_regex = sprintf("#<%s[^>]*>#s", PROTECTED_SPIP_TAGS);
		$texte = preg_replace_callback($search_regex,
					       ckeditor_wrap_callback,
					       $texte);

		/*
		 * Reconversion HTML vers typo SPIP
		 */
		require_once(find_in_path('lib/'._CKE_HTML2SPIP_VERSION.'/misc_tools.php'));
		require_once(find_in_path('lib/'._CKE_HTML2SPIP_VERSION.'/HTMLEngine.class'));
		require_once(find_in_path('lib/'._CKE_HTML2SPIP_VERSION.'/HTML2SPIPEngine.class'));
		include_spip('inc/ckeditor_class') ;

		$identity_tags = ckeditor_lire_config("html2spip_identite", _CKE_HTML2SPIP_IDENTITE);

		define('_HTML2SPIP_PRESERVE_DISTANT', true);
		$parser = new CKE_HTML2SPIPEngine($GLOBALS['db_ok']['link'], _DIR_IMG);
		$parser->loggingEnable();
		if (trim($identity_tags) != '')
			$parser->addIdentityTags(explode(';', $identity_tags));
		$output = $parser->translate($texte);
		$texte = $output['default'];

		/*
		 * Recuperation des modeles SPIP proteges
		 */
		$search_regex = '|<script type="ckeditor_wrap">([^>]*)</script>|si';
		$texte = preg_replace_callback($search_regex,
				      	       ckeditor_unwrap_callback,
					       $texte);
	}

	$ckeditor_html2spip_post = charger_fonction('ckeditor_html2spip_post','');
	$texte = $ckeditor_html2spip_post($texte);

	return $texte;
}

function ckeditor_spip2html_pre_dist($texte) {
	return $texte;
}

function ckeditor_spip2html_post_dist($texte) {
	return $texte;
}     

function ckeditor_spip2html($texte) {
	$ckeditor_spip2html_pre = charger_fonction('ckeditor_spip2html_pre','');
	$texte = $ckeditor_spip2html_pre($texte);

	$search[] = "#(?:(?:&amp;|&)lt;|<)(img|doc|emb|video|audio|text)(\d+)\|(.*?)(?:(?:&amp;|&)gt;|>)#se" ;
	$replace[] = "ckeditor_traite_img_spip('$1','$2','$3')" ;

	/* Cas de modele sans option, ex: <img1> */
	$search[] = "#(?:(?:&amp;|&)lt;|<)(img|doc|emb|video|audio|text)(\d+)(?:(?:&amp;|&)gt;|>)#se" ;
	$replace[] = "ckeditor_traite_img_spip('$1','$2','')" ;

	if (PROTECTED_SPIP_TAGS) {
		$search[] = "#(?:<|&lt;)(".PROTECTED_SPIP_TAGS.")(.*?)(?:>|&gt;)#s" ;
		$replace[] = "&lt;$1-protected$2&gt;" ; // les tags protégés ne doivent pas être traités par la fonction propre
	}

	/* Version avec bulle: [texte|bulle->lien] */
	$search[] = "#\[([^|\]]+?)\|([^\]]*?)-(?:&gt;|>)([^\]]*?)\]#se" ;
	$replace[] = "ckeditor_traite_lien_spip('$1','$3','$2')" ;

	/* Version sans bulle: [texte->lien] */
	$search[] = "#\[([^\]]*?)-(?:&gt;|>)([^\]]*?)\]#se" ;
	$replace[] = "ckeditor_traite_lien_spip('$1','$2')" ;

	$search[] = "~\[#?([^\]]*)(?:&lt;|<)-\]~s" ;
	$replace[] = "<a name='$1'></a>" ;

	$search[] = "~\[\[~" ; // on protège les notes de bas de page : on a un moyen de les afficher dans ckeditor ...
	$replace[] = "[*[*" ;

	$search[] = "~@~" ; // protection de @ : pour que Mailcrypt ne casse pas les liens
	$replace[] = "&#64;" ;

	if (CLOSED_PROTECTED_SPIP_TAGS) {
		$search[] = "#&lt;((".CLOSED_PROTECTED_SPIP_TAGS.")-protected)(.*?)&gt;(.*?)&lt;/\\1&gt;#se" ;
		$replace[] = "ckeditor_tag_unprotect('$4','$2','$3')" ;
	}

	$texte = propre(preg_replace($search, $replace, $texte)) ; // utilisation du filtre 'propre' : conseil de http://www.spip-contrib.net/RealET,411
	
	$texte = preg_replace("~\[\*\[\*~", "[[", $texte) ; // on déprotège ...
	if (PROTECTED_SPIP_TAGS) {
		$texte = preg_replace("#&lt;(".PROTECTED_SPIP_TAGS.")(-protected)#s", '&lt;$1', $texte);
	}

	$ckeditor_spip2html_post = charger_fonction('ckeditor_spip2html_post','');
	$texte = $ckeditor_spip2html_post($texte);

	return $texte ;
}

function ckeditor_convert_couleur($couleur) {
	if (preg_match("~^(.)(.)(.)$~", $couleur, $rgb)) {
		return $rgb[1].$rgb[1].$rgb[2].$rgb[2].$rgb[3].$rgb[3] ;
	} else {
		return $couleur ;
	}
}

function ckeditor_fix_default_values() {
	// fix : valeur par défaut pas lisible depuis un squelette
	// 1. nécessaire pour l'insertion d'image en mode spip
	ecrire_config("ckeditor/insertall", ckeditor_lire_config("insertall", _CKE_INSERTALL_DEF)) ; 
	// 2. nécessaire pour le plugin spipmodeles
	ckeditor_ecrire_protectedtags() ;
}
?>

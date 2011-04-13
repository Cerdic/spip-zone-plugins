<?php

/**
 * Renvoyer la balise <link> pour URL CANONIQUES
 * @return string $flux
 */
function generer_urls_canoniques(){
	include_spip('balise/url_');
	
	if (count($GLOBALS['contexte']) == 0) {
		$type_object = 'sommaire';
	} elseif (isSet($GLOBALS['contexte']['id_article'])) {
		$id_object   = $GLOBALS['contexte']['id_article'];
		$type_object = 'article';
	} elseif (isSet($GLOBALS['contexte']['id_rubrique'])) {
		$id_object   = $GLOBALS['contexte']['id_rubrique'];
		$type_object = 'rubrique';
	}

	switch ($type_object) {
		case 'sommaire':	
			$flux .= '<link rel="canonical" href="'. url_de_base() .'" />';
			break;
		default:
			$flux .= '<link rel="canonical" href="'. url_de_base() . generer_url_entite($id_object, $type_object) .'" />';
			break;
	}
	
	return $flux;
}

/**
 * Renvoyer la balise SCRIPT de Google Analytics
 * @return string $flux
 */
function generer_google_analytics(){
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);

	/* GOOGLE ANALYTICS */
	if($config['analytics']['id']){
		// Nouvelle balise : http://www.google.com/support/analytics/bin/answer.py?hl=fr_FR&answer=174090&utm_id=ad
		$flux .= "<script type=\"text/javascript\">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '".$config['analytics']['id']."']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
";
		
		// Ancien Code Google Analytics
		// $flux .= '<script type="text/javascript">';
		// $flux .= 'var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");';
		// $flux .= 'document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));';
		// $flux .= '</script>';
		// $flux .= '<script type="text/javascript">';
		// $flux .= 'try{';
		// $flux .= 'var pageTracker = _gat._getTracker("'. $config['analytics']['id'] .'");';
		// $flux .= 'pageTracker._trackPageview();';
		// $flux .= '} catch(err) {}';
		// $flux .= '</script>';
	}

	return $flux;
}

/**
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 * @return string $flux
 */
function generer_meta_tags(){
	include_spip('inc/abstract_sql');

	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);
	
	if (isSet($GLOBALS['contexte']['id_article'])) {
		$id_object   = $GLOBALS['contexte']['id_article'];
		$type_object = 'article';
	} elseif (isSet($GLOBALS['contexte']['id_rubrique'])) {
		$id_object   = $GLOBALS['contexte']['id_rubrique'];
		$type_object = 'rubrique';
	} else{
		$type_object = 'sommaire';
	}

	/* META TAGS */
	
	// If the meta tags configuration is activate
	$meta_tags = array();
	
	switch ($type_object) {
		case 'sommaire':	
				$meta_tags = $config['meta_tags']['tag'];
			break;
		default:
			$title = couper(sql_getfetsel("titre", "spip_".$type_object."s", "id_$type_object = $id_object"),64);
			$description = couper(sql_getfetsel("descriptif,texte", "spip_".$type_object."s", "id_$type_object = $id_object"),150);
			// Get the value set by default
			foreach ($config['meta_tags']['default'] as $name => $option) {
				if ($option == 'sommaire') {
					$meta_tags[$name] = $config['meta_tags']['tag'][$name];
				} elseif ($option == 'page') {
					if ($name == 'title') $meta_tags['title'] = $title;
					if ($name == 'description') $meta_tags['description'] = $description;
				} elseif ($option == 'page_sommaire') {
					if ($name == 'title') $meta_tags['title'] = $title . (($title!='')?' - ':'') . $config['meta_tags']['tag'][$name];
					if ($name == 'description') $meta_tags['description'] = $description . (($description!='')?' - ':'') . $config['meta_tags']['tag'][$name];
				}
			}
			
			// If the meta tags rubrique and articles editing is activate (should overwrite other setting)
			if ($config['meta_tags']['activate_editing'] == 'yes' && ($type_object == 'article' || $type_object == 'rubrique')) {
				$result = sql_select("*", "seo_meta_tags", "id_object = $id_object AND type_object = '$type_object'");
				while($r = sql_fetch($result)){
					if ($r['meta_content'] != '')
						$meta_tags[$r['meta_name']] = $r['meta_content'];
				}
			}				
			break;
	}
	
	// Print the result on the page
	foreach ($meta_tags as $name => $content) {
		if ($content != '')
			if ($name=='title')
				$flux .= '<title>'. htmlspecialchars(supprimer_numero(textebrut(propre($content)))) .'</title>'."\n";
			else
				$flux .= '<meta name="'. $name .'" content="'. htmlspecialchars(textebrut(propre($content))) .'" />'."\n";
	}

	return $flux;
}

/**
 * Renvoyer une META toute seule (hors balise)
 * @return string $retour
 */
function generer_meta_brute($nom){	
	$config = unserialize($GLOBALS['meta']['seo']);
	$nom = strtolower($nom);
	
	if($config['meta_tags']['tag'][$nom]){
		return $config['meta_tags']['tag'][$nom];
	}
	
	return false;
}

/**
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 * @return string $flux
 */
function generer_webmaster_tools(){
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);

	if($config['webmaster_tools']['id']){
		$flux .= '<meta name="google-site-verification" content="'. $config['webmaster_tools']['id'] .'"/>';
	}
	
	return $flux;
}

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');

/**
 * Renvoyer la balise <link> pour URL CANONIQUES
 * @return string $flux
 */
function generer_urls_canoniques(){
	include_spip('balise/url_');
	
	if (count($GLOBALS['contexte']) == 0) {
		$objet = 'sommaire';
	} elseif (isset($GLOBALS['contexte']['id_article'])) {
		$id_objet   = $GLOBALS['contexte']['id_article'];
		$objet = 'article';
	} elseif (isset($GLOBALS['contexte']['id_rubrique'])) {
		$id_objet   = $GLOBALS['contexte']['id_rubrique'];
		$objet = 'rubrique';
	}

	switch ($objet) {
		case 'sommaire':	
			$flux .= '<link rel="canonical" href="'. url_de_base() .'" />';
			break;
		default:
			$flux .= '<link rel="canonical" href="'. url_de_base() . generer_url_entite($id_objet, $objet) .'" />';
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
	}

	return $flux;
}

/**
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 * @return string $flux
 */
function calculer_meta_tags(){

	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);
	
	if (isset($GLOBALS['contexte']['id_article'])) {
		$id_objet   = $GLOBALS['contexte']['id_article'];
		$objet = 'article';
	} elseif (isset($GLOBALS['contexte']['id_rubrique'])) {
		$id_objet   = $GLOBALS['contexte']['id_rubrique'];
		$objet = 'rubrique';
	} else{
		$objet = 'sommaire';
	}

	/* META TAGS */
	
	// If the meta tags configuration is activate
	$meta_tags = array();
	
	switch ($objet) {
		case 'sommaire':	
				$meta_tags = $config['meta_tags']['tag'];
			break;
		default:
			$table = table_objet_sql($objet);
			$id_table_objet = id_table_objet($objet);
			$title = couper(sql_getfetsel("titre", $table, "$id_table_objet = ".intval($id_objet)),64);
			$requete = sql_allfetsel("descriptif,texte", $table, "$id_table_objet = ".intval($id_objet));
			if($requete) $description = couper(implode(" ",$requete[0]),150,'');
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
			if ($config['meta_tags']['activate_editing'] == 'yes' && ($objet == 'article' || $objet == 'rubrique')) {
				$result = sql_select("*", "spip_seo", "id_objet = ".intval($id_objet)." AND objet = ".sql_quote($objet));
				while($r = sql_fetch($result)){
					if ($r['meta_content'] != '')
						$meta_tags[$r['meta_name']] = $r['meta_content'];
				}
			}				
			break;
	}

	return $meta_tags;
}

function generer_meta_tags($meta_tags = null) {
	$flux = '';
    //Set meta list if not provided
    if (!is_array($meta_tags))
        $meta_tags = calculer_meta_tags();
        
	// Print the result on the page
	foreach ($meta_tags as $name => $content) {
		if ($content != '')
			if ($name=='title')
				$flux .= '<title>'. trim(htmlspecialchars(supprimer_numero(textebrut(propre($content))))) .'</title>'."\n";
			else
				$flux .= '<meta name="'. $name .'" content="'. trim(htmlspecialchars(textebrut(propre($content)))) .'" />'."\n";

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

	if($config['webmaster_tools']['id'])
		return '<meta name="google-site-verification" content="'. $config['webmaster_tools']['id'] .'" />
		';
}


/**
 * Renvoyer la META BING TOOLS
 * @return string $flux
 */
function generer_bing(){
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);

	if($config['bing']['id'])
		return '<meta name="msvalidate.01" content="'. $config['bing']['id'] .'" />
		';
}

/**
 * Renvoyer la META ALEXA
 * @return string $flux
 */
function generer_alexa(){
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);

	if($config['alexa']['id'])
		return '<meta name="alexaVerifyID" content="'. $config['alexa']['id'] .'"/>';
}

/**
 * #SEO_URL
 * Renvoyer la balise <link> pour URL CANONIQUES
 */
function balise_SEO_URL($p){
    $p->code = "calculer_balise_SEO_URL()";
    return $p;
}
function calculer_balise_SEO_URL(){
    $flux = generer_urls_canoniques();
    return $flux;
}

/**
 * #SEO_GA
 * Renvoyer la balise SCRIPT de Google Analytics
 */
function balise_SEO_GA($p){
    $p->code = "calculer_balise_SEO_GA()";
    return $p;
}
function calculer_balise_SEO_GA(){
    $flux = generer_google_analytics();
    return $flux;
}

/**
 * #SEO_META_TAGS
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 */
function balise_SEO_META_TAGS($p){
    $p->code = "calculer_balise_SEO_META_TAGS()";
    return $p;
}
function calculer_balise_SEO_META_TAGS(){
    $flux = generer_meta_tags();
    return $flux;
}

/**
 * #SEO_META_BRUTE{nom_de_la_meta}
 * Renvoyer la valeur de la meta appelée (sans balise)
 */
function balise_SEO_META_BRUTE($p){
	$_nom = str_replace("'","",interprete_argument_balise(1,$p));
    $p->code = "calculer_balise_META_BRUTE($_nom)";
	$p->interdire_scripts = false;
    return $p;
}
function calculer_balise_META_BRUTE($_nom){	
    $metas = calculer_meta_tags();
    $meta = $metas[$_nom];
    if (!$meta)
        return "";
    return $meta;
}

/**
 * #SEO_GWT
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 */
function balise_SEO_GWT($p){
    $p->code = "calculer_balise_SEO_GWT()";
    return $p;
}

function calculer_balise_SEO_GWT(){
    $flux = generer_webmaster_tools();
    return $flux;
}


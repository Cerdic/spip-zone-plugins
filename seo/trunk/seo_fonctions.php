<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');

/**
 * Renvoyer la balise <link> pour URL CANONIQUES
 * @return string $flux
 */
function generer_urls_canoniques(){
	include_spip('balise/url_');

	if (count($GLOBALS['contexte'])==0){
		$objet = 'sommaire';
	} elseif (isset($GLOBALS['contexte']['id_article'])) {
		$id_objet = $GLOBALS['contexte']['id_article'];
		$objet = 'article';
	} elseif (isset($GLOBALS['contexte']['id_rubrique'])) {
		$id_objet = $GLOBALS['contexte']['id_rubrique'];
		$objet = 'rubrique';
	}

	$flux = "";
	switch ($objet) {
		case 'sommaire':
			$flux .= '<link rel="canonical" href="' . url_de_base() . '" />';
			break;
		default:
			$flux .= '<link rel="canonical" href="' . generer_url_entite_absolue($id_objet, $objet) . '" />';
			break;
	}

	return $flux;
}

/**
 * Renvoyer la balise SCRIPT de Google Analytics
 * @return string $flux
 */
function generer_google_analytics(){
	include_spip('inc/config');

	/* GOOGLE ANALYTICS */
	$flux = "";
	if ($id=lire_config('seo/analytics/id')){
		$id = texte_script($id);
		// Nouvelle balise : http://www.google.com/support/analytics/bin/answer.py?hl=fr_FR&answer=174090&utm_id=ad
		return "<script type=\"text/javascript\">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '$id']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
";
	}

	return "";
}

/**
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 * @return string $flux
 */
function calculer_meta_tags(){
	include_spip('inc/config');

	/* CONFIG */
	$config = lire_config('seo/');

	if (isset($GLOBALS['contexte']['id_article'])){
		$id_objet = $GLOBALS['contexte']['id_article'];
		$objet = 'article';
	} elseif (isset($GLOBALS['contexte']['id_rubrique'])) {
		$id_objet = $GLOBALS['contexte']['id_rubrique'];
		$objet = 'rubrique';
	} else {
		$objet = 'sommaire';
	}

	/* META TAGS */

	// If the meta tags configuration is activate
	$meta_tags = array();

	switch ($objet) {
		case 'sommaire':
			$meta_tags = isset($config['meta_tags']['tag'])?$config['meta_tags']['tag']:array();
			break;
		default:
			$table = table_objet_sql($objet);
			$id_table_objet = id_table_objet($objet);
			$title = couper(sql_getfetsel("titre", $table, "$id_table_objet = " . intval($id_objet)), 64);
			$requete = sql_allfetsel("descriptif,texte", $table, "$id_table_objet = " . intval($id_objet));
			if ($requete) $description = couper(implode(" ", $requete[0]), 150, '');
			// Get the value set by default
			if (isset($config['meta_tags']['default'])){
				foreach ($config['meta_tags']['default'] as $name => $option){
					if ($option=='sommaire'){
						$meta_tags[$name] = $config['meta_tags']['tag'][$name];
					} elseif ($option=='page') {
						if ($name=='title') $meta_tags['title'] = $title;
						if ($name=='description') $meta_tags['description'] = $description;
					} elseif ($option=='page_sommaire') {
						if ($name=='title') $meta_tags['title'] = $title . (($title!='') ? ' - ' : '') . $config['meta_tags']['tag'][$name];
						if ($name=='description') $meta_tags['description'] = $description . (($description!='') ? ' - ' : '') . $config['meta_tags']['tag'][$name];
					}
				}
			}

			// If the meta tags rubrique and articles editing is activate (should overwrite other setting)
			if (isset($config['meta_tags']['activate_editing'])
				AND $config['meta_tags']['activate_editing']=='yes'
				AND ($objet=='article' || $objet=='rubrique')){
				$result = sql_select("*", "spip_seo", "id_objet = " . intval($id_objet) . " AND objet = " . sql_quote($objet));
				while ($r = sql_fetch($result)){
					if ($r['meta_content']!='')
						$meta_tags[$r['meta_name']] = $r['meta_content'];
				}
			}
			break;
	}

	return $meta_tags;
}

function generer_meta_tags($meta_tags = null){
	$flux = '';
	//Set meta list if not provided
	if (!is_array($meta_tags))
		$meta_tags = calculer_meta_tags();

	// Print the result on the page
	foreach ($meta_tags as $name => $content){
		if ($content!='')
			if ($name=='title')
				$flux .= '<title>' . trim(entites_html(supprimer_numero(textebrut(propre($content))))) . '</title>' . "\n";
			else
				$flux .= '<meta name="' . $name . '" content="' . trim(attribut_html(textebrut(propre($content)))) . '" />' . "\n";

	}
	return $flux;
}

/**
 * Renvoyer une META toute seule (hors balise)
 * @param string $nom
 * @return string|bool
 */
function generer_meta_brute($nom){
	include_spip('inc/config');
	return lire_config("seo/meta_tags/tag/$nom","");
}

/**
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 * @return string $flux
 */
function generer_webmaster_tools(){
	include_spip('inc/config');
	if ($id=lire_config('seo/webmaster_tools/id'))
		return '<meta name="google-site-verification" content="' . texte_script($id) . '" />'."\n";
}


/**
 * Renvoyer la META BING TOOLS
 * @return string $flux
 */
function generer_bing(){
	include_spip('inc/config');
	if ($id=lire_config('seo/bing/id'))
		return '<meta name="msvalidate.01" content="' . texte_script($id) . '" />'."\n";
}

/**
 * Renvoyer la META ALEXA
 * @return string $flux
 */
function generer_alexa(){
	include_spip('inc/config');
	if ($id=lire_config('seo/alexa/id'))
		return '<meta name="alexaVerifyID" content="' . texte_script($id) . '"/>'."\n";
}

/**
 * #SEO_URL
 * Renvoyer la balise <link> pour URL CANONIQUES
 */
function balise_SEO_URL($p){
	$p->code = "generer_urls_canoniques()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GA
 * Renvoyer la balise SCRIPT de Google Analytics
 */
function balise_SEO_GA($p){
	$p->code = "generer_google_analytics()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_TAGS
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 */
function balise_SEO_META_TAGS($p){
	$p->code = "generer_meta_tags()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_BRUTE{nom_de_la_meta}
 * Renvoyer la valeur de la meta appelée (sans balise)
 */
function balise_SEO_META_BRUTE($p){
	$_nom = str_replace("'", "", interprete_argument_balise(1, $p));
	$p->code = "table_valeur(calculer_meta_tags(),$_nom,'')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GWT
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 */
function balise_SEO_GWT($p){
	$p->code = "generer_webmaster_tools()";
	$p->interdire_scripts = false;
	return $p;
}


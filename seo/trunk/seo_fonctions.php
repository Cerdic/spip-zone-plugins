<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');

/**
 * Remplace les meta du head par celles calculees par le plugin
 * utilise par le squelette inclure/seo-head
 *
 * @param string $head
 * @param array $contexte
 * @return string
 */
function seo_insere_remplace_metas($head,$contexte){
	$append = "<!--seo_insere-->";
	// on ne fait rien si deja insere
	if (strpos($head,$append)!==false)
		return $head;

	include_spip('inc/config');
	$config = lire_config('seo/');
	$is_sommaire = (
	  (isset($contexte['type-page']) AND $contexte['type-page']=='sommaire')
	  OR (!isset($contexte['id_article']) AND !isset($contexte['id_rubrique']))
	);

	if (isset($config['meta_tags']['activate']) AND $config['meta_tags']['activate']=='yes'){
		/* d'abord les meta tags */
		$meta_tags = seo_generer_meta_tags(null,$contexte);

		foreach ($meta_tags as $key => $meta){
			$preg = '';
			/**
			 * Si le tag est <title>
			 */
			if ($key=='title')
				$preg = "/(<{$key}[^>]*>.*<\/{$key}[^>]*>)/Uims";
			/**
			 * Le tag est une <meta>
			 */
			else
				$preg = "/(<meta\s+name=['\"]{$key}['\"][^>]*>)/Uims";

			// remplacer la meta si on la trouve
			if (preg_match($preg,$head,$match)){
				$head = str_replace($match[0],$meta,$head);
			}
			else
				$append .= "$meta\n";
		}
	}
	/* META GOOGLE WEBMASTER TOOLS */
	if (isset($config['webmaster_tools'])
	  AND $config['webmaster_tools']['activate']=='yes'
	  AND $is_sommaire){
		$append .= "\n" . seo_generer_webmaster_tools();
	}

	if (isset($config['bing'])
	  AND $config['bing']['activate']=='yes'
	  AND $is_sommaire){
		$append .= "\n" . seo_generer_bing();
	}

	/* CANONICAL URL */
	if (isset($config['canonical_url'])
	  AND $config['canonical_url']['activate']=='yes'
	  AND $is_sommaire){
		$append .= "\n" . seo_generer_urls_canoniques();
	}

	/* GOOGLE ANALYTICS */
	if (isset($config['analytics'])
		AND $config['analytics']['activate']=='yes'
	  AND $is_sommaire){
		$append .= "\n" . seo_generer_google_analytics();
	}

	/* ALEXA */
	if (isset($config['alexa'])
	  AND $config['alexa']['activate']=='yes'
	  AND $is_sommaire){
		$append .= "\n" . seo_generer_alexa();
	}

	if ($append){
		$append = "\n$append\n";
		// sinon ajouter en fin de </head>
		if ($p=stripos($head,"</head>"))
			$head = substr_replace($head,$append,$p,0);
		else
			$head .= $append;
	}

	return $head;
}

/**
 * Renvoyer la balise <link> pour URL CANONIQUES
 * @return string $flux
 */
function seo_generer_urls_canoniques(){
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
function seo_generer_google_analytics(){
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
 * @param null|array $contexte
 * @return string $flux
 */
function seo_calculer_meta_tags($contexte=null){
	include_spip('inc/config');
	$config = lire_config('seo/');

	if (is_null($contexte))
		$contexte = $GLOBALS['contexte'];

	if (isset($contexte['id_article'])){
		$id_objet = $contexte['id_article'];
		$objet = 'article';
		$table = "spip_articles";
		$id_table_objet = "id_article";
	} elseif (isset($contexte['id_rubrique'])) {
		$id_objet = $contexte['id_rubrique'];
		$objet = 'rubrique';
		$table = "spip_rubriques";
		$id_table_objet = "id_rubrique";
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
			if (!$table){
				$table = table_objet_sql($objet);
				$id_table_objet = id_table_objet($objet);
			}
			$row = sql_fetsel("titre,descriptif,texte", $table, "$id_table_objet=" . intval($id_objet));
			$tag = array();
			$tag['title'] = couper($row['titre'], 64);
			unset($row['titre']);
			if (count($row)) $tag['description'] = couper(implode(" ", $row), 150, '');
			// Get the value set by default
			if (isset($config['meta_tags']['default'])){
				foreach ($config['meta_tags']['default'] as $name => $option){
					$meta_tags[$name] = array();
					if (in_array($option,array('page','page_sommaire'))){
						if (isset($tag[$name]))
							$meta_tags[$name][] = $tag[$name];
					}
					if (in_array($option,array('sommaire','page_sommaire'))){
						if (isset($config['meta_tags']['tag'][$name]))
							$meta_tags[$name][] = $config['meta_tags']['tag'][$name];
					}
					$meta_tags[$name] = implode(" - ",$meta_tags[$name]);
				}
			}

			// If the meta tags rubrique and articles editing is activate (should overwrite other setting)
			if (isset($config['meta_tags']['activate_editing'])
				AND $config['meta_tags']['activate_editing']=='yes'
				AND in_array($objet,array('article','rubrique'))){
				$result = sql_select("*", "spip_seo", "id_objet=" . intval($id_objet) . " AND objet=" . sql_quote($objet));
				while ($r = sql_fetch($result)){
					if ($r['meta_content']!='')
						$meta_tags[$r['meta_name']] = $r['meta_content'];
				}
			}
			break;
	}

	return $meta_tags;
}

/**
 * @param null|array $contexte
 * @param null|array $meta_tags
 * @return array
 */
function seo_generer_meta_tags($meta_tags = null, $contexte = null){
	$tags = array();
	//Set meta list if not provided
	if (!is_array($meta_tags))
		$meta_tags = seo_calculer_meta_tags($contexte);

	// Print the result on the page
	foreach ($meta_tags as $name => $content){
		if ($content!='')
			if ($name=='title')
				$tags[$name] = '<title>' . trim(entites_html(supprimer_numero(textebrut(propre($content))))) . '</title>';
			else
				$tags[$name] = '<meta name="' . $name . '" content="' . trim(attribut_html(textebrut(propre($content)))) . '" />';
	}
	return $tags;
}

/**
 * Renvoyer une META toute seule (hors balise)
 * @param string $nom
 * @return string|bool
 */
function seo_generer_meta_brute($nom){
	include_spip('inc/config');
	return lire_config("seo/meta_tags/tag/$nom","");
}

/**
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 * @return string $flux
 */
function seo_generer_webmaster_tools(){
	include_spip('inc/config');
	if ($id=lire_config('seo/webmaster_tools/id'))
		return '<meta name="google-site-verification" content="' . texte_script($id) . '" />';
}


/**
 * Renvoyer la META BING TOOLS
 * @return string $flux
 */
function seo_generer_bing(){
	include_spip('inc/config');
	if ($id=lire_config('seo/bing/id'))
		return '<meta name="msvalidate.01" content="' . texte_script($id) . '" />';
}

/**
 * Renvoyer la META ALEXA
 * @return string $flux
 */
function seo_generer_alexa(){
	include_spip('inc/config');
	if ($id=lire_config('seo/alexa/id'))
		return '<meta name="alexaVerifyID" content="' . texte_script($id) . '" />';
}

/**
 * #SEO_URL
 * Renvoyer la balise <link> pour URL CANONIQUES
 */
function balise_SEO_URL($p){
	$p->code = "seo_generer_urls_canoniques()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GA
 * Renvoyer la balise SCRIPT de Google Analytics
 */
function balise_SEO_GA($p){
	$p->code = "seo_generer_google_analytics()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_TAGS
 * Renvoyer les META Classiques
 * - Meta Titre / Description / etc.
 */
function balise_SEO_META_TAGS($p){
	$p->code = "seo_generer_meta_tags(null,\$Pile[0])";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_BRUTE{nom_de_la_meta}
 * Renvoyer la valeur de la meta appelée (sans balise)
 */
function balise_SEO_META_BRUTE($p){
	$_nom = str_replace("'", "", interprete_argument_balise(1, $p));
	$p->code = "table_valeur(seo_calculer_meta_tags(),$_nom,'')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GWT
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 */
function balise_SEO_GWT($p){
	$p->code = "seo_generer_webmaster_tools()";
	$p->interdire_scripts = false;
	return $p;
}


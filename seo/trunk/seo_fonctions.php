<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');

function seo_interprete_contexte($contexte){
	static $infos;
	$s = serialize($contexte);
	if (isset($infos[$s]))
		return $infos[$s];
	$infos[$s] = array('objet'=>'sommaire');
	if (isset($contexte['type-page'])){
		$infos[$s]['objet'] = $contexte['type-page'];
		if ($infos[$s]['objet']!=='sommaire'
			AND $primary = id_table_objet($infos[$s]['objet'])
			AND isset($contexte[$primary])){
			$infos[$s]['id_objet'] = $contexte[$primary];
			$infos[$s]['primary'] = $primary;
			$infos[$s]['table_sql'] = table_objet_sql($infos[$s]['objet']);
		}
		return $infos[$s];
	}
	// d'abord les rubriques
	if (isset($contexte['id_rubrique'])){
		$infos[$s] = array(
			'objet'=>'rubrique',
			'id_objet'=>$contexte['id_rubrique'],
			'primary'=>'id_rubrique',
			'table_sql'=>'spip_rubriques'
		);
	}
	// puis voyons si on trouve un objet plus precis
	$tables = lister_tables_objets_sql();
	foreach ($tables as $t=>$d){
		if (
			$t!=='spip_rubriques' AND isset($d['key']['PRIMARY KEY'])
		  AND
			($infos[$s]['objet']!=='rubrique' OR isset($d['field']['id_rubrique']))
		){
			$primary = $d['key']['PRIMARY KEY'];
			if (isset($contexte[$primary])){
				$infos[$s]['objet'] = $d['type'];
				$infos[$s]['id_objet'] = $contexte[$primary];
				$infos[$s]['primary'] = $primary;
				$infos[$s]['table_sql'] = $t;
			}
			return $infos[$s];
		}
	}
	return $infos[$s];
}

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
	$i = seo_interprete_contexte($contexte);
	$is_sommaire = ($i['objet']=='sommaire');

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
			if ($preg AND preg_match($preg,$head,$match)){
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
		$append .= "\n" . seo_generer_urls_canoniques($contexte);
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
 * @param array $contexte
 * @return string
 */
function seo_generer_urls_canoniques($contexte){
	include_spip('inc/urls');
	$i = seo_interprete_contexte($contexte);

	if (isset($i['id_objet'])){
		return '<link rel="canonical" href="' . generer_url_entite_absolue($i['id_objet'], $i['objet']) . '" />';
	}
	elseif($i['objet']=='sommaire')
		return '<link rel="canonical" href="' . url_de_base() . '" />';

	return '';
}

/**
 * Renvoyer la balise SCRIPT de Google Analytics
 * @return string
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
 * @return array
 */
function seo_calculer_meta_tags($contexte=null){
	include_spip('inc/config');
	$config = lire_config('seo/');

	if (is_null($contexte))
		$contexte = $GLOBALS['contexte'];
	$i = seo_interprete_contexte($contexte);

	/* META TAGS */

	// If the meta tags configuration is activate
	$meta_tags = array();

	if (isset($i['id_objet'])){
		$trouver_table = charger_fonction("trouver_table","base");
		$desc = $trouver_table($i['table_sql']);
		$select = array();
		if (isset($desc['titre']))
			$select[] = $desc['titre'];
		elseif(isset($desc['field']['titre']))
			$select[] = "titre";
		if (isset($desc['field']['descriptif']))
			$select[] = "descriptif";
		if (isset($desc['field']['chapo']))
			$select[] = "chapo";
		if (isset($desc['field']['texte']))
			$select[] = "texte";

		$tag = array();
		if (count($select)){
			$select = implode(",",$select);
			$row = sql_fetsel($select, $i['table_sql'], $i['primary']."=" . intval($i['id_objet']));
			if (isset($row['titre'])){
				$tag['title'] = couper($row['titre'], 64);
				unset($row['titre']);
			}
			if (isset($row['lang']))
				unset($row['lang']);
			if (count($row))
				$tag['description'] = couper(implode(" ", $row), 150, '');
		}
		// Get the value set by default
		if (isset($config['meta_tags']['default']) AND is_array($config['meta_tags']['default'])){
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
				if (count($meta_tags[$name]))
					$meta_tags[$name] = implode(" - ",$meta_tags[$name]);
				else
					unset($meta_tags[$name]);
			}
		}

		// If the meta tags rubrique and articles editing is activate (should overwrite other setting)
		if (isset($config['meta_tags']['activate_editing'])
			AND $config['meta_tags']['activate_editing']=='yes'){
			$result = sql_select("*", "spip_seo", "id_objet=" . intval($i['id_objet']) . " AND objet=" . sql_quote($i['objet']));
			while ($r = sql_fetch($result)){
				if ($r['meta_content']!='')
					$meta_tags[$r['meta_name']] = $r['meta_content'];
			}
		}
	}
	elseif($i['objet']=="sommaire") {
		$meta_tags = isset($config['meta_tags']['tag'])?$config['meta_tags']['tag']:array();
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
 * @return string
 */
function seo_generer_meta_brute($nom){
	include_spip('inc/config');
	return lire_config("seo/meta_tags/tag/$nom","");
}

/**
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 * @return string
 */
function seo_generer_webmaster_tools(){
	include_spip('inc/config');
	if ($id=lire_config('seo/webmaster_tools/id'))
		return '<meta name="google-site-verification" content="' . texte_script($id) . '" />';
}


/**
 * Renvoyer la META BING TOOLS
 * @return string
 */
function seo_generer_bing(){
	include_spip('inc/config');
	if ($id=lire_config('seo/bing/id'))
		return '<meta name="msvalidate.01" content="' . texte_script($id) . '" />';
}

/**
 * Renvoyer la META ALEXA
 * @return string
 */
function seo_generer_alexa(){
	include_spip('inc/config');
	if ($id=lire_config('seo/alexa/id'))
		return '<meta name="alexaVerifyID" content="' . texte_script($id) . '" />';
}

/**
 * #SEO_URL
 * Renvoyer la balise <link> pour URL CANONIQUES
 * @param $p
 */
function balise_SEO_URL_dist($p){
	$p->code = "seo_generer_urls_canoniques(\$Pile[0])";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GA
 * Renvoyer la balise SCRIPT de Google Analytics
 * @param $p
 */
function balise_SEO_GA_dist($p){
	$p->code = "seo_generer_google_analytics()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_TAGS
 * Renvoyer les META editoriales
 * - Meta Titre / Description / etc.
 * @param $p
 */
function balise_SEO_META_TAGS_dist($p){
	$p->code = "seo_generer_meta_tags(null,\$Pile[0])";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_META_BRUTE{nom_de_la_meta}
 * Renvoyer la valeur de la meta appelée (sans balise)
 * @param $p
 */
function balise_SEO_META_BRUTE_dist($p){
	$_nom = str_replace("'", "", interprete_argument_balise(1, $p));
	$p->code = "table_valeur(seo_calculer_meta_tags(),$_nom,'')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO_GWT
 * Renvoyer la META GOOGLE WEBMASTER TOOLS
 * @param $p
 */
function balise_SEO_GWT_dist($p){
	$p->code = "seo_generer_webmaster_tools()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * #SEO : insere toutes les meta d'un coup, a l'endroit indique
 * @param $p
 */
function balise_SEO_dist($p){
	$p->code = "seo_insere_remplace_metas('',\$Pile[0])";
	$p->interdire_scripts = false;
	return $p;
}

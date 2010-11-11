<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Par defaut, cache de 2 heures pour l'ajax statique
// possibilité de forcer une duree avec le critere {ttl_ajaxload=60*60*24}
if(!defined("_DUREE_CACHE_AJAXSTATIC")) define("_DUREE_CACHE_AJAXSTATIC", 7200);


// Evolution de #INCLURE pour inclusions ajaxload
// #INCLURE{fond=xxx,....ajax,ajaxload} le fait
function balise_INCLURE($p) {
	$f = balise_INCLURE_dist($p);

	if (false !== strpos($f->code, "'ajaxload'"))
		$f->code = preg_replace('/recuperer_fond/', 'recuperer_fond_ajax',
						$f->code, 1);
						
	// inserer UNE FOIS le X-Spip_Filtre:INCLUREAJAXLOAD_affichemeta
	// equivalent a #FILTRE{INCLUREAJAXLOAD_affichemeta}
	if(!defined("_INCLURE_AJAX_LOAD_INSERT")) {
		define("_INCLURE_AJAX_LOAD_INSERT", "oui");
		$f->code .= ".'<' . '"
			.'?php header("X-Spip-Filtre: \'.'
				."INCLUREAJAXLOAD_affichemeta"
			. " . '\"); ?'.'>'";

		$f->interdire_scripts = false;
	}
	return $f;
}


// cree un appel ahah vers ce recuperer_fond
function recuperer_fond_ajax() {
	$args = func_get_args();

	$args[1]["fond"] = $args[0];

	$cle = md5(serialize($args));
	$ajax = entites_html(encoder_contexte_ajax($args[1]));
	

	$alt = entites_html(sinon($args[1]['ajaxloadalt'],$args[1]['fond']));
	$message = $args[1]['ajaxload'];
	$searching = sinon($args[1]['ajaxsearching'],
		"<img src='".find_in_path('images/searching.gif')."' alt='$alt' />");
	// Le lien mène à l'action de pose du cookie no_js
	$url = "spip.php?action=ia_nojs&amp;retour=".urlencode(parametre_url(self(),'no_js','oui'));

	if (isset($args[2]['ajax']) AND $args[2]['ajax']=='1') 
		$class_ajax = " ajaxbloc env-$ajax";

	// Appliquer la methode: 
	// - soit laisser les automatismes Ajax de SPIP {ajaxload}
	// - soit sauvergarder "en dur" le resultat HTML {ajaxload=html}
	// - soit retourner l'url de la noisette {ajaxload=url}
	// - soit retourner l'url du fichier html {ajaxload=url_html}
	$methode = $args[1]["ajaxload"];

	$ttl = _DUREE_CACHE_AJAXSTATIC;
	if ($args[1]['ttl_ajaxload']) $ttl = valeur_numerique($args[1]['ttl_ajaxload']);

	if ($methode == "url") {
		$ajax = urlencode($ajax);
		$ret = "spip.php?var_ajax=recuperer&amp;var_ajax_env=$ajax";
		
	} else if ($methode == "url_html") {
		$fichier = sous_repertoire(_DIR_VAR, 'cache-ajaxload').$cle.".html";

		// Test sur le fichier
		if (!file_exists($fichier) || _request('var_mode') == "recalcul"
				|| (file_exists($fichier) && date("U") - @filemtime($fichier) > $ttl)
			){
			//echo "RECALCULER";
			$contenu = call_user_func_array('recuperer_fond', $args);
			ecrire_fichier($fichier, $contenu);
			// ecrire une version .gz pour content-negociation par apache, cf. [11539]
			//ecrire_fichier("$fichier.gz",$contenu, true);

		}
		
		$ret = $fichier;
		
	} else if ($methode == "html") {
		
		$fichier = sous_repertoire(_DIR_VAR, 'cache-ajaxload').$cle.".html";
		
		
		// Test sur le fichier
		if (!file_exists($fichier) || _request('var_mode') == "recalcul"
				|| (file_exists($fichier) && date("U") - @filemtime($fichier) > $ttl)
			){
			//echo "RECALCULER";
			$contenu = call_user_func_array('recuperer_fond', $args);
			ecrire_fichier($fichier, $contenu);
			// ecrire une version .gz pour content-negociation par apache, cf. [11539]
			//ecrire_fichier("$fichier.gz",$contenu, true);

		}
		
		if (_request('var_no_ajax')
		OR _request('var_mode') == 'inclure')
			return $contenu;

		$ret =
			"<div class='includestatic$class_ajax'><a href=\"$url\" rel=\"$fichier\">$searching</a></div>";
		
		
		//print_r($contenu);
	
	} else {
		if (_request('var_no_ajax')
		OR _request('var_mode') == 'inclure')
			return call_user_func_array('recuperer_fond', $args);
	
		$ret =
			"<div class='includeajax$class_ajax'><a href=\"$url\" rel=\"$ajax\">$searching</a></div>";
	}
	
	return $ret;
}

function remettre_fond_ajax($matches) {
	$url = $matches[2];
	$c = $matches[3];
	$c = decoder_contexte_ajax($c);

	$page = recuperer_fond($c["fond"],$c,array('trim'=>false));
	
	return $page;
}

function remettre_fond_ajax_static($matches) {
	$url = $matches[3];
	if (file_exists($url)) $page = join("", @file($url));
	else $page = "";
	
	return $page;
}

function INCLUREAJAXLOAD_affichemeta($page) {
	if (strpos($page, "includeajax") > 0 || strpos($page, "includestatic") > 0) {

		$javascript = '<?php if ($_COOKIE["no_js"] != "no_js" && !_IS_BOT && _request("no_js") != "oui") { ?>
<script type="text/javascript"><!--
document.write("<\/script><script>/*");
//--></script>
<meta http-equiv="refresh" content="2; url='.$GLOBALS["meta"]["adresse_site"].'/spip.php?action=ia_nojs&amp;retour=<?php echo urlencode(self(\'&\'));?>" />
<script type="text/javascript">/* */</script>
<?php } else { ?>
<script type="text/javascript"><!--
	document.cookie = "no_js=; expires=Thu, 01-Jan-70 00:00:01 GMT;";
--></script>
<?php } ?>'.$javascript;
		
		$page = str_replace("</head>", "$javascript</head>", $page);
	}
	return $page;
}

function INCLUREAJAXLOAD_affichage_final($page) {
	// Si le visiteur est un robot de moteur de recherche,
	// reconstituer les pages completes
	if(_IS_BOT || $_COOKIE["no_js"] == "no_js" || _request("no_js") == "oui" ) {
		include_spip("inc/filtres");
		include_spip("public/assembler");
		$page = preg_replace_callback(",(<div class='includeajax[^\']*'><a href=\"(.*)\" rel=\"(.*)\">.*</a></div>),msU", "remettre_fond_ajax", $page);
		$page = preg_replace_callback(",(<div class='includestatic[^\']*'><a href=\"(.*)\" rel=\"(.*)\">.*</a></div>),msU", "remettre_fond_ajax_static", $page);
	}

	return $page;
}

function INCLUREAJAXLOAD_insert_head($flux) {
	$flux .= "\n<script src=\"".find_in_path('javascript/inclure-ajaxload.js')."\" type=\"text/javascript\"></script>";
	return $flux;
}



?>

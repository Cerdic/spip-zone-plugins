<?php

function together_affichage_final_prive($texte){
	$b = '<li class="bouton">'
		.'<button id="start-togetherjs" type="button" style="line-height:20px;margin:2px 0;"'
		.'onclick="TogetherJS(this);return false" '
		.'data-end-togetherjs-html="Arrêter">Coopérer</button>'
		.'</li>';

	$texte = preg_replace(",(<ul class='rapides collaborer'>)(.*)(</ul>),Uims","\\1\\2$b\\3",$texte);


	$js = together_privacy_js();

	if ($p = strpos($texte,"</body>"))
		$texte = substr_replace($texte,$js,$p,0);

	return $texte;
}


function together_privacy_js(){
	$local_js = sous_repertoire(_DIR_VAR. "cache-js") . "togetherjs-min.js";
	$no = _DIR_VAR. "cache-js/.notogetherjs";
	$remote_js = "https://togetherjs.com/togetherjs-min.js";

	if (
	  (!file_exists($local_js)
	  OR @filemtime($local_js)<strtotime("-1 day"))){

		if (file_exists($no) AND @filemtime($no)>strtotime("-1 week"))
			$local_js = $remote_js;
		elseif (!recuperer_page($remote_js,$local_js)
		  AND !file_exists($local_js)){
			touch($no);
			$local_js = $remote_js;
		}
		elseif (include_spip("inc/compresseur_minifier") AND function_exists("minifier_js")){
			lire_fichier($local_js,$js);
			$js = minifier_js($js);
			ecrire_fichier($local_js,$js);
		}
	}

	if (!isset($GLOBALS['visiteur_session']['together_js_avatar'])){
		include_spip("inc/auth");
		$infos = auth_informer_login($GLOBALS['visiteur_session']['login']);
		$logo = "";
		include_spip("inc/filtres");
		if (isset($infos['logo']) AND $infos['logo'])
			$logo = url_absolue(extraire_attribut($infos['logo'],"src"));
		session_set("together_js_avatar",$logo);
	}
	$avatarjs = "";
	if (isset($GLOBALS['visiteur_session']['together_js_avatar'])
	  AND $GLOBALS['visiteur_session']['together_js_avatar']){
		$avatarjs = "TogetherJSConfig_getUserAvatar = function () {return '".addslashes($GLOBALS['visiteur_session']['together_js_avatar'])."';};";
	}

	return "<script>var _tohether_jqs = jQuery;"
	."TogetherJSConfig_siteName = '".addslashes($GLOBALS['meta']['nom_site'])."';"
	."TogetherJSConfig_toolName = 'Coopérer';"
	."TogetherJSConfig_getUserName = function () {return '".addslashes($GLOBALS['visiteur_session']['nom'])."';};"
	. $avatarjs
	."</script>
<script src='$local_js'></script>";
}
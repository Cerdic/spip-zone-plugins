<?php

function together_affichage_final_prive($texte){
	$js = '
<script type="text/javascript">
jQuery(function(){
	jQuery(".rapides.collaborer").append(\'<li class="bouton">\'
	+\'<button id="start-togetherjs" type="button" style="line-height:20px;margin:2px 0;"\'
	+\'onclick="TogetherJS(this);return false" \'
	+\'data-end-togetherjs-html="Arrêter">Coopérer</button>\'
	+\'</li>\');
});
</script>'.together_privacy_js();

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

	return "<script src='$local_js'></script>";
}
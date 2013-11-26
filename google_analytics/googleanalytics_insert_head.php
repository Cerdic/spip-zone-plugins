<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Dans SPIP 3 on utilise insert_head_css qui est safe
 * et on insere avant les CSS, pour ne pas bloquer celles-ci
 * (qui sont bloquees par du js inline)
 *
 * @param $flux
 * @return string
 */
function googleanalytics_insert_head_css($flux){
	return googleanalytics_snippet().$flux;
}

/**
 * Dans SPIP 2 on utilise insert_head et on ajoute a la fin
 * simplement
 *
 * @param $flux
 * @return string
 */
function googleanalytics_insert_head($flux){
	return $flux . googleanalytics_snippet();
}

/**
 * Morceau de code a inserer dans la page pour traquer avec GA
 * @return string
 */
function googleanalytics_snippet(){
	include_spip('inc/config');
	$id_google = lire_config('googleanalytics/idGoogle');
	if ($id_google
	  AND $id_google !== '_'
	  AND (strncmp($id_google,"UA-xxx",6)!=0)) {

		return '<script type="text/javascript">/*<![CDATA[*/
var _gaq = _gaq || [];
_gaq.push(["_setAccount", "'.$id_google.'"]);
_gaq.push(["_trackPageview"]);
(function() {
	var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
	ga.src = ("https:" == document.location.protocol ? "https://" : "http://") + "stats.g.doubleclick.net/dc.js";
	var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
})();
/*]]>*/</script>'."\n";

	}
	return "";
}
?>

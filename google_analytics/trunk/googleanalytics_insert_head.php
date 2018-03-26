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
function googleanalytics_insert_head_css($flux) {
	return $flux . googleanalytics_snippet();
}

/**
 * Dans SPIP 2 on utilise insert_head et on ajoute a la fin
 * simplement
 *
 * @param $flux
 * @return string
 */
function googleanalytics_insert_head($flux) {
	return $flux . googleanalytics_snippet();
}

/**
 * Morceau de code a inserer dans la page pour traquer avec GA
 * @return string
 */
function googleanalytics_snippet(){
	include_spip('inc/config');
	$id_google = lire_config('googleanalytics/idGoogle');
	$cookiebar = (isset($_COOKIE["cb-enabled"]) ? $_COOKIE["cb-enabled"] : '');
	$displayCookieConsent = ((isset($_COOKIE["displayCookieConsent"]) and strlen($_COOKIE["displayCookieConsent"])) ? $_COOKIE["displayCookieConsent"] : 'y');
	if ($id_google
	  AND $id_google !== '_'
	  AND (strncmp($id_google,"UA-xxx",6) != '0')
	  AND $cookiebar !== 'declined'
	  AND $displayCookieConsent === 'y') {
	    if (lire_config('googleanalytics/ga_universal')) {
	        return "<script type='text/javascript'>/*<![CDATA[*/
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })
  (window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', '".$id_google."');
    ga('send', 'pageview');
/*]]>*/</script>\n";
	    } else {
		    return '<script type="text/javascript">/*<![CDATA[*/
	var _gaq = _gaq || [];
	_gaq.push(["_setAccount", "'.$id_google.'"]);
	_gaq.push(["_trackPageview"]);
	(function() {
	  var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
	  ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
	  var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
	})();
/*]]>*/</script>'."\n";
        }
	}
	return "";
}
?>

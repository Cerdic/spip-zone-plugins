<?php

function balise_INSERT_NAV_CEF_dist ($p) {
	$p->code = "'<!-- CEF API -->
<div id=\'cef-root\'></div>
<script type=\"text/javascript\" charset=\"utf-8\"> 
window.cefAsyncInit = function() {
    CEF.initNavigationBar({site_search: ".lire_config('navcef/site_search', 'false').", share_links: ".lire_config('navcef/share_links', 'true').", add_top_margin: true, with_animation: true, scrolling_bar: true});
};
(function() {
var e = document.createElement(\'script\'); e.async = true;
e.src = \'http://recherche.catholique.fr/api/cef.js\';
document.getElementById(\'cef-root\').appendChild(e);
}());
</script>'";
	$p->interdire_scripts = false;
	return $p;
}

?>
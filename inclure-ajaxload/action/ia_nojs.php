<?php

function action_ia_nojs () {
	// Installer un cookie NO_JS
	// pour une semaine
	setcookie("no_js", "no_js", time()+(3600*24*7));	
		
	@header("Refresh: 0; Url="._request("retour"));
	echo "&nbsp;";	
}

?>
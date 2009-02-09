<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// ajoute les css et js necessaires dans les pages adequates
function xspf_header_prive($texte) {
	$bgiframejs = find_in_path('lib/jquery-tooltip/lib/jquery.bgiframe.js');
	$tooltipjs = find_in_path('lib/jquery-tooltip/jquery.tooltip.js');
	$swfobject = find_in_path('javascript/swfobject.js');
			
	$texte.= "
		<script type='text/javascript' src='$swfobject'></script>\n
		<script type='text/javascript' src='$bgiframejs'></script>\n
		<script type='text/javascript' src='$tooltipjs'></script>\n";

	$texte.= "
		<script type='text/javascript'>
			$(document).ready(function() {
				$('a').tooltip({
					track: true,
					delay: 0,
					showURL: false,
					showBody: ' - '
				});
			});
		</script>\n";
	return $texte;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
function xspf_affichage_final($page) {

    // on regarde rapidement si la page a des classes player
    if (strpos($page, 'class="xspf_player"')===FALSE)
        return $page;

    // Si oui on ajoute le js de swfobject
    $jsFile = find_in_path('javascript/swfobject.js');
	$head = "<script src='$jsFile' type='text/javascript'></script>";
	$pos_head = strpos($page, '</head>');

	return substr_replace($page, $head, $pos_head, 0);
}

?>
<?php

// inc/raper_pipeline_affiche_gauche.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function raper_affiche_gauche ($flux) {

	$exec = _request('exec');
	
	$pages_raper = array('raper_configure', 'raper_edit');
	
	if(in_array($exec, $pages_raper)) {
		
		$flux['data'] .= ""
			. "<br />\n"
			. "<div class='verdana2'>\n"
			. "
<script type='text/javascript'>
<!--
	if(jQuery.fn.jquery < '1.2.6') {
		document.write(\"" . raper_js_propre(_T('raper:jquery_ancienne_version')) . "\"); 
	}
//-->
</script>
<noscript>
" . raper_js_propre(_T('raper:activez_javascript')) . "
</noscript>
				"
			. "</div>\n"
			;

	}
	
	return ($flux);
}

function raper_js_propre ($s) {
	$s = ""
		. "<div class='cadre-padding'><div class='verdana2'>"
		//. $s // semble ne pas aimer les \n ?
		. preg_replace("|[[:space:]]+|" , " ", $s)
		. "</div></div>";
	return($s);
}

?>
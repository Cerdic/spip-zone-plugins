<?php

function jqueryp_insert_jquery_plugins($flux){
	$js = array('type'=>'inline','data'=>array());
	$js = _jqueryp_insert_jquery_plugins($js);
	$inline = join($js['data'],"\n");
	spip_log("inline" . $inline,'jquery_plugins');
	return $flux . "\n\n" . $inline;
}

// ajoute les plugins jquery dans jquery.js.html
function jqueryp_insert_js($flux) {
	if (isset($flux) && $flux['type']=='fichier')
		$flux = _jqueryp_insert_jquery_plugins($flux);
	return $flux;
}

// ajoute les plugins jquery dans le head pour SPIP 1.9.2
function jqueryp_insert_head($flux) {
	if (!isset($GLOBALS["spip_pipeline"]["insert_js"])){
		$flux .= _jqueryp_insert_jquery_plugins();
	}
	return $flux;
}

// ajoute les plugins jquery dans le head pour SPIP 1.9.2
function jqueryp_header_prive($flux) {
	if (!isset($GLOBALS["spip_pipeline"]["insert_js"])){
		$flux .= _jqueryp_insert_jquery_plugins();
	}
	return $flux;
}

function _jqueryp_insert_jquery_plugins($flux = null){
	if (!$lpa = jqueryp_liste_plugins_actifs())
		return $flux;
		
	if (isset($flux))
		$flux = jqueryp_add_plugins(array_values(array_flip($lpa)), $flux);
	else
		$flux = jqueryp_add_plugins(array_values(array_flip($lpa)));
	
	return $flux;
}
?>

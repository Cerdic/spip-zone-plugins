<?php

function jqueryp_insert_jquery_plugins($flux){
	$js = array('type'=>'inline','data'=>array());
	$js = _jqueryp_insert_jquery_plugins($js);
	$inline = join($js['data'],"\n");
	return $flux . "\n\n" . $inline;
}

/*
// ajoute les plugins jquery dans jquery.js.html
function jqueryp_insert_js($flux) {
	if (isset($flux) && $flux['type']=='fichier')
		$flux = _jqueryp_insert_jquery_plugins($flux);
	return $flux;
}
*/

function _jqueryp_insert_jquery_plugins($flux = null){
	if (!$lpa = jqueryp_liste_plugins('actifs'))
		return $flux;
		
	if (isset($flux))
		$flux = jqueryp_add_plugins(array_values(array_flip($lpa)), $flux);
	else
		$flux = jqueryp_add_plugins(array_values(array_flip($lpa)));
	
	return $flux;
}


/* 
 * Pipeline 'jquery_plugins' pour SPIP = 1.9.3 : ajouter simplement
 * les scripts a inserer au tableau de scripts passe dans le flux
 * cf. http://doc.spip.org/@f_jQuery
 */
function jqueryp_jquery_plugins($flux) {
	$flux = array_unique(array_merge($flux,(array)jqueryp_liste_plugins('actifs')));
	return $flux;
}

?>

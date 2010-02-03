<?php

//Appel du pipeline qui ajoute des barres de porte-plume partout dans Descriptif, chapo et PS

// function préfixe_pipelineexistant ($nomdeboucle) {}
function ppp_jquery_plugins($js) {
	$js[] = 'javascript/barre_generalisee.js';
	return $js;
}

?>
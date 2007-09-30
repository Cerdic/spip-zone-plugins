<?php

// ne pas demander l'indentation par defaut car cela ralentit le rendu d'un coeff 3
// ceux qui veulent avoir du html indente doivent le demande explicitement dans leur
// mes options
//$GLOBALS['xhtml'] = 'sax';

//$GLOBALS['xml_indent'] = 'sax'; // pour seulement le validateur XML
//$GLOBALS['xml_validation'] = true; // pour le validateur selon la DTD

if (!defined('_DIR_PLUGIN_ACCESRESTREINT') AND !function_exists('critere_tout_voir_dist')){
	// {tout_voir}
	function critere_tout_voir_dist($idb, &$boucles, $crit) {
		$boucle = &$boucles[$idb];
		$boucle->modificateur['tout_voir'] = true;
	}
}
?>
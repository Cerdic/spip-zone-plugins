<?php
$GLOBALS['spip_pipelines']['insert_body'] = '';
function phpmv_affichage_final($texte){
	global $html;
	if (!isset($GLOBALS['meta']['phpmv_flag_insert_body'])
		AND $html 
		AND (!isset($_GET['var_nophpmv'])) 
		AND (strpos($texte,'<!-- phpmyvisites -->')===FALSE)){
		include_spip('phpmv_fonctions');
		$code = phpmv_get_code();
		$texte=preg_replace(",(</body>),i","$code\n</body>",$texte);
	}
	return $texte;
}
?>
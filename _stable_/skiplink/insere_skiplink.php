<?php

# inserer les liens dans la page
function skiplink_affichage_final(&$page) {
	
	

	// ne pas se fatiguer si pas HTML
	if (!$GLOBALS['html']
	)
		return $page;

    $recherche_existe = (preg_match(',<input.*?name[ ]*=[ ]*"recherche".*?>,i', $page) == 1 ? 'oui' : 'non');
	$raccourcis_recherche_existe = strpos($page,'id="raccourci_recherche"');
	
	//si raccourci recherche existe et recherche aussi -> pas la peine de se fatiguer
	if($recherche_existe == "oui" and $raccourcis_recherche_existe)
	   return $page;
    
	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	
	$raccourcis = recuperer_fond('raccourcis', array('lang'=>$GLOBALS['spip_lang'], 'recherche'=>$recherche_existe));
	preg_match(',<body\b.*?>,i', $page, $regs);
  
	if ($regs and !strpos($page,'id="raccourcis"'))#pas deux fois au cas ou
		$page = substr_replace($page, $raccourcis, (strpos($page, $regs[0]) + strlen($regs[0])), 0);
	$remonter = recuperer_fond('remonter', array('lang'=>$GLOBALS['spip_lang']));
	$ancre_basse = recuperer_fond('ancre_basse', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<\/body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $ancre_basse, strpos($page, $regs[0]), 0);
    
    if($recherche_existe == "oui" and !$raccourcis_recherche_existe) // au cas où qu1 aurait mis la noisette sans preciser qu'il veut la recherche alors même qu'il l'a.
        $raccourcis_erronnes = recuperer_fond('raccourcis', array('lang'=>$GLOBALS['spip_lang'], 'recherche'=>'non'));
        $page = str_replace($raccourcis_erronnes,$raccourcis,$page);

	return $page;
}

?>
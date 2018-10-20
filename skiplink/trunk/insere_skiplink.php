<?php

# inserer les liens dans la page
function skiplink_affichage_final($page) {
	// ne pas se fatiguer si pas HTML
	if (!$GLOBALS['html']
	  OR strpos($page, 'id="skiplinks"') # pas deux fois, au cas ou !
		OR strpos($page,"<!-- insert_head -->")===false # pas de insert_head, pas d'insertion auto des liens
	)
		return $page;

	if (!function_exists('recuperer_fond')) include_spip('public/assembler');
	$recherche_existe = (preg_match(',<input.*?name[ ]*=[ ]*"recherche".*?>,i', $page) == 1 ? 'oui' : 'non');
	$skiplinks = recuperer_fond('go/skiplinks', array('lang'=>$GLOBALS['spip_lang'], 'recherche'=>$recherche_existe));
	preg_match(',<body\b.*?>,i', $page, $regs);

	if ($regs)
		$page = substr_replace($page, $skiplinks, (strpos($page, $regs[0]) + strlen($regs[0])), 0);
	$go_top = recuperer_fond('go/top', array('lang'=>$GLOBALS['spip_lang']));
	$a_bottom = recuperer_fond('a/bottom', array('lang'=>$GLOBALS['spip_lang']));
	preg_match(',<\/body\b.*?>,i', $page, $regs);
	if ($regs)
		$page = substr_replace($page, $a_bottom, strpos($page, $regs[0]), 0);

	return $page;
}

?>

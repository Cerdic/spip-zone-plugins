<?php
if(defined('_cache_PERSO')) {
	function cs_detecte_cache($liste) {
		$a = array();
		foreach(explode(',', $liste) as $c)
			if(preg_match(',([a-z]+)([0-9]+)=([^\s]+),', $c, $reg))
				$a["\$GLOBALS['id_$reg[1]']==$reg[2];"] = $reg[3];
		return $a;
	}
	$GLOBALS['cs_caches'] = cs_detecte_cache(_cache_PERSO);
	function cs_fixe_cache(&$cache) {
		if(is_array($GLOBALS['cs_caches'])) foreach($GLOBALS['cs_caches'] as $id_test => $delai) {
			$test = false;
			@eval('$test = '.$id_test);
			if($test) {	$cache = valeur_numerique($delai); return; }
		}
	}
	// on court-circuite la balise #CACHE
	function balise_CACHE($p) {
		cs_fixe_cache($p->param[0][1][0]->texte);
		return balise_CACHE_dist($p);
	}
	// une balise pour tester le cache
	function balise_CACHE_TEST($p) {
		$p->code = "time()";
		$p->interdire_scripts = false;
		return $p;
	}
}
?>
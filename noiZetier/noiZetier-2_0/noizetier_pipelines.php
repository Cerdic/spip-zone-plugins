<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_header_prive($flux){
	$css = find_in_path('css/noizetier.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}


/**
 * Pipeline styliser pour appeler le générateur de bloc du noizetier s'il s'agit d'une page activée
 *
 * @param array $flux
 * @return array
 */
function noizetier_styliser($flux){
	include_spip('inc/noizetier');
	// On réoriente si la page est active
	if (preg_match(',(contenu|navigation|extra)/([^/]*)$,i',$flux['data'],$regs)
	  AND $bloc = $regs[1]
	  AND $page = $regs[2]
	  AND $pages_actives = unserialize($GLOBALS['meta']['noizetier-pages-actives'])
	  AND isset($pages_actives[$page])){
		$ext = $flux['args']['ext'];
		if ($squelette = find_in_path("noizetier-generer-bloc-$bloc.$ext")) 
			$flux['data'] = substr($squelette, 0, -strlen(".$ext"));
	}
	// Dans le cas où Zpip a appelé bloc/page-dist et que la page par défaut est activé
	// On réoriente sur la page par défaut
	if (preg_match(',(contenu|navigation|extra)/page-dist$,i',$flux['data'],$regs)
	  AND $bloc = $regs[1]
	  AND $pages_actives = unserialize($GLOBALS['meta']['noizetier-pages-actives'])
	  AND isset($pages_actives['page'])){
		$ext = $flux['args']['ext'];
		if ($squelette = find_in_path("noizetier-generer-bloc-$bloc-dist.$ext")) 
			$flux['data'] = substr($squelette, 0, -strlen(".$ext"));
	}
	// Dans le cas où on a recours à la noisette squelettebloc
	if (preg_match(',(contenu|navigation|extra)/([^/]*)-squelettebloc$,i',$flux['args']['fond'],$regs)
	  AND $bloc = $regs[1]
	  AND $page = $regs[2]){
		$ext = $flux['args']['ext'];
		if ($squelette = find_in_path("$bloc/$page.$ext")) 
			$flux['data'] = substr($squelette, 0, -strlen(".$ext"));
		else {
			$type=noizetier_page_type($page);
			$composition=noizetier_page_type($composition);
			if($composition!='' AND $squelette = find_in_path("$bloc/$type.$ext"))
				$flux['data'] = substr($squelette, 0, -strlen(".$ext"));
			elseif ($squelette = find_in_path("$bloc/page-dist.$ext"))
				$flux['data'] = substr($squelette, 0, -strlen(".$ext"));
		}
	}
	return $flux;
}




?>

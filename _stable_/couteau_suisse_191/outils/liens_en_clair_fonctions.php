<?php

function liens_en_clair_callback($matches) {
	if (preg_match(',^(mailto:|news:)(.*)$,', $lien = $matches[1], $matches2)) $lien =  $matches2[2];
	// si mailcrypt est actif, on decode le lien cache dans "title"
	if (defined('_mailcrypt_AROBASE_JS') && preg_match(',title="([^"]+)'.preg_quote(_mailcrypt_AROBASE_JS).'([^"]+)",', $matches[0], $matches2)) 
		$lien = $matches2[1]._mailcrypt_AROBASE.$matches2[2];
	// doit-on afficher le lien en clair ?
	$ajouter_lien = 
		$lien!=$matches[2]
			// pas les ancres
			&& $lien[0]!='#'
			// pas les liens internes...
			&& strpos($matches[0], '"spip_in"')===false
			// pas le glossaire SPIP...
			&& strpos($matches[0], '"spip_glossaire"')===false;
	if ($ajouter_lien) return $matches[0] . " [$lien]";
	return $matches[0];
}

// filtre utilisable sur les balises SPIP
function liens_en_clair($texte) {
	if (strpos($texte, 'href')===false) return $texte;
	// recherche de tous les liens : <a href="??">
	$texte = preg_replace_callback(',<a[^>]+href="([^"]+)"[^>]*>(.*)</a>,Umsi', 'liens_en_clair_callback', $texte);
	return $texte;
}

?>
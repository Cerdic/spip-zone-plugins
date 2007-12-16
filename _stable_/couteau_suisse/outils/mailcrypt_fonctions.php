<?php

// pour _cs_liens_AUTORISE
include_spip('outils/inc_cs_liens');

// tip visible onMouseOver
@define('_mailcrypt_AROBASE_JS', '..&aring;t..');
// span ayant l'arobase en background
@define('_mailcrypt_AROBASE', '<span class="spancrypt">&nbsp;</span>');
@define('_mailcrypt_REGEXPR1', ',\b['._cs_liens_AUTORISE.']*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._cs_liens_AUTORISE.']*)?,');
@define('_mailcrypt_REGEXPR2', ',\b(['._cs_liens_AUTORISE.']*)@([a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._cs_liens_AUTORISE.']*)?),');

function mailcrypt($texte) {
	if (strpos($texte, '@')===false) return $texte;

	// liens HTML
	$lien = '$1' . _mailcrypt_AROBASE_JS . '$2';
	$texte = preg_replace(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 
		'"#" title="'.$lien.'" onclick="location.href=lancerlien(\'$1\',\'$2\'); return false;"', $texte);
	if (strpos($texte, '@')===false) return $texte;

	// protection de tout le reste...
	$texte = preg_replace(_mailcrypt_REGEXPR2, '$1'._mailcrypt_AROBASE.'$2', $texte);
	return $texte;
}

?>
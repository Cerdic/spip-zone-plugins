<?php

@define('_mailcrypt_AROBASE_JS', '..&aring;t..'); // tip visible onMouseOver
//@define('_mailcrypt_MAIL', '['.ucfirst(_T('email')).']'); // affichage de : [Email]
// span ayant l'arobase en background
@define('_mailcrypt_AROBASE', '<span class="spancrypt"></span>');
// regexp trouvee dans l'outil "liens orphelins"
@define('_mailcrypt_AUTORISE', '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
@define('_mailcrypt_REGEXPR1', ',\b['._mailcrypt_AUTORISE.']*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._mailcrypt_AUTORISE.']*)?,');
@define('_mailcrypt_REGEXPR2', ',\b(['._mailcrypt_AUTORISE.']*)@([a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._mailcrypt_AUTORISE.']*)?),');

function mailcrypt($texte) {
	if (strpos($texte, '@')===false) return $texte;
	
	// liens HTML
	$lien = '$1' . _mailcrypt_AROBASE_JS . '$2';
	$texte = preg_replace(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 
		'"#" title="'.$lien.'" onclick="location.href=lancerlien(\'$1\',\'$2\'); return false;"', $texte);
	if (strpos($texte, '@')===false) return $texte;

	// nettoyage total, on ne sait jamais...
	//$texte = preg_replace(_mailcrypt_REGEXPR1, _mailcrypt_MAIL, $texte);
	$texte = preg_replace(_mailcrypt_REGEXPR2, '$1'._mailcrypt_AROBASE.'$2', $texte);
	return $texte;
}

?>
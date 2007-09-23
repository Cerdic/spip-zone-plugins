<?php

@define('_mailcrypt_AROBASE_JS', '..&aring;t..'); // tip visible onMouseOver
//@define('_mailcrypt_MAIL', '['.ucfirst(_T('email')).']'); // affichage de : [Email]
// span ayant l'arobase en background
@define('_mailcrypt_AROBASE', '<span style="background:transparent url('
	. find_in_path('img/mailcrypt/leure.gif')
	. ') no-repeat scroll left center; color:#000099; padding-left:12px; text-decoration:none;"></span>'); // tip visible onMouseOver
// regexp trouvee dans l'outil "liens orphelins"
@define('_mailcrypt_AUTORISE', '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
@define('_mailcrypt_REGEXPR1', ',\b['._mailcrypt_AUTORISE.']*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._mailcrypt_AUTORISE.']*)?,');
@define('_mailcrypt_REGEXPR2', ',\b(['._mailcrypt_AUTORISE.']*)@([a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._mailcrypt_AUTORISE.']*)?),');

function mailcrypt($texte) {
	if (strpos($texte, '@')===false) return $texte;
	
	// liens HTML
	$texte = preg_replace(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 
		'"#" title="$1' . _mailcrypt_AROBASE_JS . '$2" onclick="location.href=lien(this.title); return false;"', $texte);
	if (strpos($texte, '@')===false) return $texte;

	// nettoyage total, on ne sait jamais...
	//$texte = preg_replace(_mailcrypt_REGEXPR1, _mailcrypt_MAIL, $texte);
	$texte = preg_replace(_mailcrypt_REGEXPR2, '$1'._mailcrypt_AROBASE.'$2', $texte);
	return $texte;
}

?>
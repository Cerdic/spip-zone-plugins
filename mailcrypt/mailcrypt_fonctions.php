<?php

function mailcrypt_echappe($matches) {
	return code_echappement($matches[0], 'MAILCRYPT');
}

function mailcrypt($texte) {
	static $ok = NULL;
	if (strpos($texte, '@')===false) return $texte;

	if(is_null($ok)) {
		$ok = true;
		// tip visible onMouseOver (title)
		// jQuery replacera ensuite le '@' comme ceci : title.replace(/\.\..t\.\./,'[\x40]')
		@define('_MAILCRYPT_AROBASE_JS', '..&aring;t..');
		@define('_MAILCRYPT_AROBASE_JSQ', preg_quote(_MAILCRYPT_AROBASE_JS,','));
		// span ayant l'arobase en background
		@define('_MAILCRYPT_AROBASE', '<span class=\'spancrypt\'> '._T('mailcrypt:chez').' </span>');
		@define('_MAILCRYPT_CARACTERES_LIENS', '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
		@define('_MAILCRYPT_REGEXPR', ',\b(['._MAILCRYPT_CARACTERES_LIENS.']+)@([a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._MAILCRYPT_CARACTERES_LIENS.']*)?),');
		@define('_MAILCRYPT_FONCTION_JS_LANCER_LIEN','mc_lancerlien');
	}

	// echappement des 'input' au cas ou le serveur y injecte des mails persos
	if (strpos($texte, '<in')!==false) 
		$texte = preg_replace_callback(',<input [^<]+/>,Umsi', 'mailcrypt_echappe', $texte);
	// echappement des 'protoc://login:mdp@site.ici' afin ne pas les confondre avec un mail
	if (strpos($texte, '://')!==false) 
		$texte = preg_replace_callback(',[a-z0-9]+://['._MAILCRYPT_CARACTERES_LIENS.']+:['._MAILCRYPT_CARACTERES_LIENS.']+@,Umsi', 'mailcrypt_echappe', $texte);
	// echappement des domaines .htm/.html : ce ne sont pas des mails
	if (strpos($texte, '.htm')!==false)
		$texte = preg_replace_callback(',href=(["\'])[^>]*@[^>]*\.html?\\1,', 'mailcrypt_echappe', $texte);

	// protection des liens HTML
	$texte = preg_replace(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 
		'"#" title="$1' . _MAILCRYPT_AROBASE_JS . '$2" onclick="location.href=' . _MAILCRYPT_FONCTION_JS_LANCER_LIEN . '(\'$1\',\'$2\'); return false;"', $texte);
	// retrait des titles en doublon... un peu sale, mais en attendant mieux ?
	$texte = preg_replace(',title="[^"]+'._MAILCRYPT_AROBASE_JSQ.'[^"]+"([^>]+title=[\"\']),', '$1', $texte);

	if (strpos($texte, '@')===false) return echappe_retour($texte, 'MAILCRYPT');
	// protection de tout le reste...
	$texte = preg_replace(_MAILCRYPT_REGEXPR, '$1'._MAILCRYPT_AROBASE.'$2', $texte);
	return echappe_retour($texte, 'MAILCRYPT');
}

?>
<?php

@define('_mailcrypt_AROBASE', '..&aring;t..'); // tip visible onMouseOver
@define('_mailcrypt_MAIL', '['.ucfirst(_T('email')).']'); // affichage par defaut d'un lien mail sans texte

function mailcrypt($texte) {
	if (strpos($texte, '@')===false) return $texte;
	
	// liens HTML
	$texte = preg_replace(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 
		'"#" title="$1' . _mailcrypt_AROBASE . '$2" onclick="location.href=lien(this.title); return false;"', $texte);
	if (strpos($texte, '@')===false) return $texte;

	// nettoyage total, on ne sait jamais... regexp trouvee dans l'outil "liens orphelins"
	$autorises =  '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9';
	$texte = preg_replace(",\b[{$autorises}]*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?[{$autorises}]*)?,", _mailcrypt_MAIL, $texte);
	return $texte;
}

?>
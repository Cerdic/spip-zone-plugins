<?php
/**
 * Plugin MailCrypt
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function mailcrypt_post_propre($texte) {
	return mailcrypt($texte);
}

function mailcrypt_affichage_final($texte){
	if ($GLOBALS['html']
	  AND strpos($texte,"mc_lancerlien")!==false){
		$js = <<<js
<script type="text/javascript">/*<![CDATA[*/
function mc_lancerlien(a,b){x='ma'+'ilto'+':'+a+'@'+b.replace(/\.\..t\.\./g,'@'); return x;}
jQuery(function(){
	jQuery('.spancrypt').empty().append('@');
	jQuery('a.spip_mail').attr('title',function(i, val) {	return val.replace(/\.\..t\.\./g,'@');	});
});/*]]>*/</script>
js;
		if ($p = stripos($texte,"</body>"))
			$texte = substr_replace($texte,$js,$p,0);
	}
	return $texte;
}

function mailcrypt_facteur_pre_envoi($facteur) {
	$facteur->Body = maildecrypt($facteur->Body);
	$facteur->AltBody = maildecrypt($facteur->AltBody);
	return $facteur;
}

function mailcrypt_echappe($matches) {
	return code_echappement($matches[0], 'MAILCRYPT');
}

function mailcrypt_protection_lien($matches) {
	$m1 = $matches[1];
	$m2 = $matches[2];
	$m2 = preg_replace(',\@,', _MAILCRYPT_AROBASE_JS, $m2); // Il faut aussi tenir compte des ecritures du type mailto:toto@toto.org,titi@titi.org  ou mailto:toto@toto.org?cc=tata@tata.org
	return '"#'.$m1.'#mc#'.$m2.'#" title="'.$m1. _MAILCRYPT_AROBASE_JS . $m2.'" onclick="location.href=' . _MAILCRYPT_FONCTION_JS_LANCER_LIEN . '(\''.$m1.'\',\''.$m2.'\'); return false;"';
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
	$texte = preg_replace_callback(",[\"\']mailto:([^@\"']+)@([^\"']+)[\"\'],", 'mailcrypt_protection_lien', $texte);
	// retrait des titles en doublon... un peu sale, mais en attendant mieux ?
	$texte = preg_replace(',title="[^"]+'._MAILCRYPT_AROBASE_JSQ.'[^"]+"([^>]+title=[\"\']),', '$1', $texte);

	if (strpos($texte, '@')===false) return echappe_retour($texte, 'MAILCRYPT');
	// protection de tout le reste...
	$texte = preg_replace(_MAILCRYPT_REGEXPR, '$1'._MAILCRYPT_AROBASE.'$2', $texte);
	return echappe_retour($texte, 'MAILCRYPT');
}

function maildecrypt($texte) {
	if (strpos($texte, 'spancrypt')===false AND strpos($texte, 'mc_lancerlien')===false AND strpos($texte, '#mc')===false) return $texte;
	
	// Traiter les <span class="spancrypt">chez</span>
	$texte = preg_replace(',<span class=\'spancrypt\'>(.*)</span>,U','@',$texte);
	$texte = preg_replace(',<span class="spancrypt">(.*)</span>,U','@',$texte);
	
	// Traiter les liens HTML
	$texte = preg_replace(
		',href="#(\S+)#mc#(\S+)#" title="(\S+)'._MAILCRYPT_AROBASE_JSQ.'(\S+)" onclick="location.href=' . _MAILCRYPT_FONCTION_JS_LANCER_LIEN. '(.+)",U',
		'href="mailto:$3@$4"',
		$texte
	);
	
	// Traiter les liens texte
	$texte = preg_replace(',#(\S+)#mc#(\S+)#,U' , 'mailto:$1@$2' , $texte);
	$texte = preg_replace(',(\S+) '._T('mailcrypt:chez').' (\S+),U' , '$1@$2' , $texte);
	
	// Supprimer l'appel du javascript
	$texte = preg_replace(',<script type=\'text/javascript\'(.*)mailcrypt.js(.*)</script>,U','',$texte);
	
	// Nettoyer les eventuels _MAILCRYPT_AROBASE_JSQ restants (lien avec destinataires mutiples)
	$texte = preg_replace(',_MAILCRYPT_AROBASE_JSQ,', '@', $texte);
	
	return $texte;
}



?>
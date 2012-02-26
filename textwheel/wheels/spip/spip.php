<?php

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/texte');

/*

 Sauts de ligne automatiques, adressables en CSS :
   .autobr:before{content:"\0B6";color:orange;}
 effacables en CSS :
   .autobr br {content:""}
 pour retrouver le fonctionnement SPIP ancien (ignorer les sauts de ligne) :
   define('_AUTOBR', '');

*/


/**
 * callback pour la puce qui est definissable/surchargeable
 */
function replace_puce(){
	static $puce;
	if (!isset($puce))
		$puce = "\n<br />".definir_puce()."&nbsp;";
	return $puce;
}

/**
 * callback fermer-para-mano
 * on refait le preg, a la main
 */
function fermer_para_mano(&$t) {
	defined('_AUTOBR')||define('_AUTOBR', "<span class='autobr'><br /></span>");

	# match: ",<p (.*)<(/?)(STOP P|div|pre|ul|ol|li|blockquote|h[1-6r]|t(able|[rdh]|body|foot|extarea)|form|object|center|marquee|address|d[ltd]|script|noscript|map|button|fieldset|style)\b,UimsS"
	# replace: "\n<p "+trim($1)+"</p>\n<$2$3"

	foreach (explode('<p ', $t) as $c => $p) {
		if ($c == 0)
			$t = $p;
		else {
			$pi = strtolower($p);
			if (preg_match(
			",</?(?:stop p|div|pre|ul|ol|li|blockquote|h[1-6r]|t(able|[rdh]|body|foot|extarea)|form|object|center|marquee|address|d[ltd]|script|noscript|map|button|fieldset|style)\b,S",
			$pi, $r)) {
				$pos = strpos($pi, $r[0]);
				$t .= "\n<p ".str_replace("\n", _AUTOBR."\n", rtrim(substr($p,0,$pos)))."</p>\n".substr($p,$pos);
			} else {
				$t .= '<p '.$p;
			}
		}
	}

	$t = str_replace(_AUTOBR."\n<br />", '<br />', $t);

	return $t;
}

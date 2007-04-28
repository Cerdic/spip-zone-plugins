<?php
/*
 code & integration 2007 : Patrice Vanneufville
 Toutes les infos sur : http://www.spip-contrib.net/?article1564
 Cet outil est base sur les references suivantes :
 	http://gbiv.com/protocols/uri/rfc/rfc3986.html
	http://tools.ietf.org/html/rfc3696
*/

function liens_orphelins_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'LIENS');
}
function liens_orphelins_raccourcis_callback($matches) {
 return cs_code_echappement('[->'.$matches[1].']', 'LIENS');
}

function liens_orphelins_rempl($texte){
	// deja, on s'en va si pas de point...
	if (strpos($texte, '.')===false) return $texte;
	// prudence 1 : on protege TOUTES les balises <a></a> pour eviter les doublons
	if (strpos($texte, '<a')!==false) 
		$texte = preg_replace_callback(',(<a\s*[^<]+</a>),Ums', 'liens_orphelins_echappe_balises_callback', $texte);
	// prudence 2 : on protege TOUS les raccourcis de liens Spip, au cas ou...
	if (strpos($texte, '[')!==false) 
		$texte = preg_replace_callback(',(\[([^][]*)->(>?)([^]]*)\]),msS', 'liens_orphelins_echappe_balises_callback', $texte);
	// prudence 3 : on protege TOUTES les balises contenant des points, histoire de voir plus clair
	if (strpos($texte, '<')!==false) 
		$texte = preg_replace_callback(',(<[^>]+\.[^>]*>),Ums', 'liens_orphelins_echappe_balises_callback', $texte);
	// encore ici, on s'en va si pas de point...
	if (strpos($texte, '.')===false) return echappe_retour($texte, 'LIENS');

	// chiffres, lettres, 20 caracteres speciaux autorises dans les urls
	$autorises =  '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9';
	$autorisesfin = '\#\$\&\'\*\+\-\/\=\^\_\`\|\~a-zA-Z0-9';

   // trouve : protocole://qqchose
   $texte = preg_replace(",([a-zA-Z]+://[{$autorises}:@]*[{$autorisesfin}]),", "@@LO1@@$1@@LO2@@", $texte);
   // on protege, pour la suite...
   $texte = preg_replace_callback(',@@LO1@@(.+)@@LO2@@,U', 'liens_orphelins_raccourcis_callback', $texte);
// bizarre j'arrive pas a faire marcher directement :
//	$texte = preg_replace_callback(',([a-zA-Z]+://[{$autorises}:@]*),', 'liens_orphelins_raccourcis_callback', $texte);

   // trouve : www.lieu.qqchose ou ftp.lieu.qqchose
   $texte = preg_replace(",\b((www|ftp)\.[a-zA-Z0-9_-]+\.[{$autorises}]*[{$autorisesfin}]),", "@@LO1@@$1@@LO2@@", $texte);
   // on protege, pour la suite...
   $texte = preg_replace_callback(',@@LO1@@(.+)@@LO2@@,U', 'liens_orphelins_raccourcis_callback', $texte);
// bizarre j'arrive pas a faire marcher directement :
//	$texte = preg_replace_callback(',\b((www|ftp)\.[a-zA-Z0-9_-]+\.[{$autorises}]*),', 'liens_orphelins_raccourcis_callback', $texte);

   // trouve : mailto:qqchose ou news:qqchose
   if($GLOBALS["liens_orphelins_etendu"]) {
	   $texte = preg_replace(",\b(news:[{$autorises}]*[{$autorisesfin}]),", "[->$1]", $texte);
	   $texte = preg_replace(",\b(mailto:)?([{$autorises}]*@[a-zA-Z][a-zA-Z0-9-]*\.[a-zA-Z]+(\?[{$autorises}]*)?),", "[$2->mailto:$2]", $texte);
	}
	return echappe_retour($texte, 'LIENS');
}

function liens_orphelins($texte){
	if (strpos($texte, '.')===false) return $texte;
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'liens_orphelins_rempl', $texte);
}

?>
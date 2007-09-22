<?php

// expanser_liens() introduit sous SPIP 1.93
if ($GLOBALS['spip_version_code']<1.9262) {
	@define('_RACCOURCI_LIEN', ",\[([^][]*)->(>?)([^]]*)\],msS");
	function expanser_liens($letexte) {
		$inserts = array();
		if (preg_match_all(_RACCOURCI_LIEN, $letexte, $matches, PREG_SET_ORDER)) {
			$i = 0;
			foreach ($matches as $regs) {
				$inserts[++$i] = traiter_raccourci_lien($regs);
				$letexte = str_replace($regs[0], "@@SPIP_ECHAPPE_LIEN_$i@@", $letexte);
			}
		}
		$letexte = typo($letexte, /* echap deja fait, accelerer */ false);
		foreach ($inserts as $i => $insert)
			$letexte = str_replace("@@SPIP_ECHAPPE_LIEN_$i@@", $insert, $letexte);
		return $letexte;
	}
}

function liens_orphelins_echappe_balises_callback($matches) {
 return cs_code_echappement($matches[1], 'LIENS');
}
function liens_orphelins_raccourcis_callback($matches) {
 return cs_code_echappement('[->'.$matches[1].']', 'LIENS');
}

function liens_orphelins($texte){
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
	   $texte = preg_replace(",\b(mailto:)?([{$autorises}]*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?[{$autorises}]*)?),", "[$2->mailto:$2]", $texte);
	}

// SPIP 1.93 ne repasse plus les liens semble-t-il !?
// TODO >> voir si ca reste necessaire :
if ($GLOBALS['spip_version_code']>=1.9262) $texte=expanser_liens($texte);

	return echappe_retour($texte, 'LIENS');
}

?>
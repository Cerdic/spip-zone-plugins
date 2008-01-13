<?php
// expanser_liens() est introduit sous SPIP 1.93
if (!defined('_SPIP19300')) {
	@define('_RACCOURCI_LIEN', ',\[([^][]*)->(>?)([^]]*)\],msS');
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

/*
 chiffres, lettres, 20 caracteres speciaux autorises dans les urls
 voir les references suivantes :
 	http://gbiv.com/protocols/uri/rfc/rfc3986.html
	http://tools.ietf.org/html/rfc3696
*/
@define('_cs_liens_AUTORISE', $autorises='\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
@define('_cs_liens_AUTORISE_FIN', $autorisesfin='\#\$\&\'\*\+\-\/\=\^\_\`\|\~a-zA-Z0-9');
@define('_cs_liens_HTTP', ",[a-zA-Z]+://[{$autorises}:@]*[{$autorisesfin}],");
@define('_cs_liens_WWW', ",\b(www|ftp)\.[a-zA-Z0-9_-]+\.[{$autorises}]*[{$autorisesfin}],");
@define('_cs_liens_NEWS', ",\bnews:[{$autorises}]*[{$autorisesfin}],");
@define('_cs_liens_MAILS', ",\b(mailto:)?([{$autorises}]*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?[{$autorises}]*)?),");

// les callback et echappements...
function cs_liens_echappe_callback($matches)
	{return cs_code_echappement($matches[0], 'LIENS');}
function cs_liens_raccourcis_callback($matches)
	{return cs_code_echappement(expanser_liens('[->'.retour_interro_amp($matches[0]).']'), 'LIENS');}
function cs_liens_email_callback($matches)
	{return cs_code_echappement(expanser_liens("[$matches[2]->mailto:$matches[2]]"), 'LIENS');}
function echappe_interro_amp($texte)
	{return str_replace(array('?', '!', '&'), array('++cs_INTERRO++', '++cs_EXCLAM++', '++cs_AMP++'), $texte);}
function retour_interro_amp($texte)
	{return str_replace(array('++cs_INTERRO++', '++cs_EXCLAM++', '++cs_AMP++'), array('?', '!', '&'), $texte);}

?>
<?php
/*
 code & integration 2007 : Patrice Vanneufville
 Toutes les infos sur : http://www.spip-contrib.net/?article1564
 Cet outil est base sur les references suivantes :
 	http://gbiv.com/protocols/uri/rfc/rfc3986.html
	http://tools.ietf.org/html/rfc3696
*/

// liens_orphelins() est dans liens_orphelins_fonctions.php pour permettre les traitements
function liens_orphelins_pipeline($texte){
	if (strpos($texte, '.')===false) return $texte;
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'liens_orphelins', $texte);
}

?>
<?php
/*
 - ToutMulti -
 Introduit le raccourci <:texte:> pour utiliser librement des
 blocs multi dans un flux de texte (via typo ou propre)
 Accepte egalement les arguments. Exemple :
  <:chaine{argument1=un texte, argument2=un autre texte}:>
*/

function ToutMulti_rempl($texte) {
	if(!defined('CS_MODULES_CORE') || !defined('CS_BALISE_IDIOMES')) {
		// expression tiree du code de SPIP 2.0 et 3.0 : ecrire/public/phraser_html.php
		// les filtres ont ete retires ; on pourrait ignorer les modules et laisser SPIP se debrouiller...
		@define('CS_BALISE_IDIOMES',',<:(([a-z0-9_]+):)?([a-z0-9_:]+)({([^=>]*=[^>]*)})?:>,iS');
		// modification du separateur des listes de modules a consulter sous SPIP 3
		// on pourrait mettre cette constante a '' pour laisser SPIP se debrouiller...
		@define('CS_MODULES_CORE', defined('_SPIP30000')?'spip|ecrire|public:':'spip/ecrire/public:');
	}
	if (preg_match_all(CS_BALISE_IDIOMES, $texte, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $m) {
			// Stocker les arguments de la balise de traduction
			$args = array();
			foreach(explode(',',$m[5]) as $val) {
				$arg = explode('=', $val);
				if (strlen($key = trim($arg[0]))) $args[$key] = trim($arg[1]);	
			}
			$texte = str_replace($m[0], _T((strlen($m[1])?$m[1]:CS_MODULES_CORE).$m[3], $args), $texte);
		}
	}
	return $texte;
}

// fonction principale (pipeline pre_typo)
function ToutMulti_pre_typo($texte) {
	if (strpos($texte, '<:')===false) return $texte;
	// appeler ToutMulti_rempl() une fois que certaines balises ont ete protegees
	return cs_echappe_balises('', 'ToutMulti_rempl', $texte);
}

?>
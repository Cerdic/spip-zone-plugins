<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


//
if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelee par propre() s'il repere un mode <math>
// http://doc.spip.org/@traiter_math
function traiter_math($letexte, $source='') {

	$texte_a_voir = $letexte;
	while (($debut = strpos($texte_a_voir, "<math>")) !== false) {
		if (!$fin = strpos($texte_a_voir,"</math>"))
			$fin = strlen($texte_a_voir);

		$texte_debut = substr($texte_a_voir, 0, $debut);
		$texte_milieu = substr($texte_a_voir,
			$debut+strlen("<math>"), $fin-$debut-strlen("<math>"));
		$texte_fin = substr($texte_a_voir,
			$fin+strlen("</math>"), strlen($texte_a_voir));

		// Les doubles $$x^2$$ en mode 'div'
		while((preg_match(",[$][$]([^$]+)[$][$],",$texte_milieu, $regs))) {
			$echap = "\n<p class=\"spip\" style=\"text-align: center;\">".$regs[0]."</p>\n";
			$pos = strpos($texte_milieu, $regs[0]);
			$texte_milieu = substr($texte_milieu,0,$pos)
				. code_echappement($echap, $source)
				. substr($texte_milieu,$pos+strlen($regs[0]));
		}

		// Les simples $x^2$ en mode 'span'
		while((preg_match(",[$]([^$]+)[$],",$texte_milieu, $regs))) {
			$echap = $regs[0];
			$pos = strpos($texte_milieu, $regs[0]);
			$texte_milieu = substr($texte_milieu,0,$pos)
				. code_echappement($echap, $source)
				. substr($texte_milieu,$pos+strlen($regs[0]));
		}

		$texte_a_voir = $texte_debut.$texte_milieu.$texte_fin;
	}

	return $texte_a_voir;
}

?>

<?php

/*
 * ancres
 *
 * introduit le raccourci [#ancre<-] pour les ancres
 *
 * Auteur : collectif
 * © 2005 - Distribue sous licence BSD
 *
 */

class Ancres {

	// la fonction est tres legere on la definit directement ici
	function ancres($texte) {
		$regexp = "|\[#?([^][]*)<-\]|";
		if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
		foreach ($matches as $regs)
			$texte = str_replace($regs[0],
			'<a name="'.entites_html($regs[1]).'"></a>', $texte);
		return $texte;
	}
}
?>
<?php

/*
 * smallcaps
 *
 * introduit le raccourci <sc>...</sc> pour les petites majuscules
 *
 * Auteur : arno@scarabee.com
 * © 2005 - Distribue sous licence GNU/GPL
 *
 */

class SmallCaps{

	// Raccourci typographique <sc></sc>
	function post_typo($texte) {
		$texte = str_replace("<sc>",
			"<span style=\"font-variant: small-caps\">", $texte);
		$texte = str_replace("</sc>", "</span>", $texte);
		return $texte;
	}
  
}


?>
<?php

// mailcrypt() est dans mailcrypt_fonctions.php
function mailcrypt_post_propre($texte) {
	if (strpos($texte, '@')===false) return $texte;
	// appeler mailcrypt() une fois que certaines balises ont ete protegees
	return cs_echappe_balises('html|code|cadre|frame|script|acronym|cite', 'mailcrypt', $texte);
}

?>
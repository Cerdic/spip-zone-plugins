<?php
// New line string, which should be:
//		\n		on unices
//		\r		on Mac OS
//		\r\n	on Windows
@define('CM_NEWLINE', "\n");
// penser a recopier le fichier _CLEVERMAIL_LETTRE_EN_LIGNE_fonctions.php ou placer la fonction extraire dans le fichier de fonction
@define("_CLEVERMAIL_LETTRE_EN_LIGNE", 'clevermail_lettre');
@define("_CLEVERMAIL_NOUVEAUTES_HTML", 'clevermail_nouveautes_html');
// _CLEVERMAIL_NOUVEAUTES_HTML_OPTION est facultatif. Il permet de completer l'url amorcee avec _CLEVERMAIL_NOUVEAUTES_HTML.
// define("_CLEVERMAIL_NOUVEAUTES_HTML_OPTION", 'cat=mot&sujet=1&pied=1&entete=1');
@define("_CLEVERMAIL_NOUVEAUTES_TEXT", 'clevermail_nouveautes_text');
// _CLEVERMAIL_NOUVEAUTES_TEXT_OPTION est facultatif. Il permet de completer l'url amorcee avec _CLEVERMAIL_NOUVEAUTES_TEXT.
// define("_CLEVERMAIL_NOUVEAUTES_TEXT_OPTION", 'cat=mot&sujet=1&pied=1&entete=1');
@define("_CLEVERMAIL_PREVIEW_HTML", 'clevermail_post_preview_html');
@define("_CLEVERMAIL_PREVIEW_TEXTE", 'clevermail_post_preview_text');
@define("_CLEVERMAIL_VALIDATION", 'clevermail_do');
@define("_CLEVERMAIL_INVALIDATION", 'clevermail_rm');
?>
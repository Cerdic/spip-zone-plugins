<?php
// New line string, which should be:
//		\n		on unices
//		\r		on Mac OS
//		\r\n	on Windows
@define('CM_NEWLINE', "\n");
// penser a recopier le fichier _CLEVERMAIL_LETTRE_EN_LIGNE_fonctions.php ou placer la fonction extraire dans le fichier de fonction
@define("_CLEVERMAIL_LETTRE_EN_LIGNE", 'clevermail_lettre');
@define("_CLEVERMAIL_PREVIEW_HTML", 'clevermail_post_preview_html');
@define("_CLEVERMAIL_PREVIEW_TEXTE", 'clevermail_post_preview_text');
@define("_CLEVERMAIL_VALIDATION", 'clevermail_do');
@define("_CLEVERMAIL_INVALIDATION", 'clevermail_rm');
// par defaut la date de calcul des squelettes de lettre
// est place sur la date du dernier envoi (1970 s'il n'y en a pas avant).
// mettre false pour avoir la date du jour de calcul...
@define("_CLEVERMAIL_AGE_PLACE_SUR_DERNIER_ENVOI", true);

?>

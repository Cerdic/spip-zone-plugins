<?php
// Ce fichier est charge a chaque recalcul
// Attention, ici il se peut que le plugin ne soit pas initialise (cas des .js/.css par exemple)

// pour voir les erreurs ?
if (defined('_CS_REPORT')) error_reporting(E_ALL ^ E_NOTICE);
elseif (defined('_CS_REPORTALL')) error_reporting(E_ALL);

$GLOBALS['cs_fonctions_essai'] = 1;
//cs_log("INIT : cout_fonctions ($GLOBALS[cs_options]/$GLOBALS[cs_fonctions]/$GLOBALS[cs_init])");

// plugin initialise si cout_options est OK (fin de compilation par exemple)
if(!$GLOBALS['cs_init']) {
	if($GLOBALS['cs_options']) {
		if(!$GLOBALS['cs_fonctions']) {
			// inclusion des fonctions pre-compilees
			@include(_DIR_CS_TMP.'mes_fonctions.php');
			cs_log("INCL : cout_fonctions, cs_fonctions = $GLOBALS[cs_fonctions]");
		} // else cs_log(' FIN : cout_fonctions deja inclus');
	} else cs_log('ESSAI : cout_fonctions, mais cout_options n\'est pas inclus');
} else cs_log('ESSAI : cout_fonctions, mais initialisation en cours');

?>
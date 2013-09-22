<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/piecon/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_piecon' => 'Configuration de Piecon',

	// E
	'explication_fallback' => 'Le fallback est la méthode utilisée pour changer soit le title, soit la favicon de la page. Il peut avoir 3 valeurs :<br />
"false", la valeur par défaut, ne changera que la favicon lorsque c’est possible<br />
"true", seul le title sera modifié en indiquant un pourcentage même si le navigateur accepte le changement de favicon<br />
"force", changera la favicon lorsque c’est possible ainsi que le title de la page ;',

	// L
	'label_background' => 'Couleur de fond',
	'label_color' => 'Couleur',
	'label_fallback' => 'Méthode par défaut',
	'label_shadow' => 'Couleur d’ombre',

	// O
	'option_fallback_false' => 'false',
	'option_fallback_force' => 'force',
	'option_fallback_true' => 'true'
);

?>

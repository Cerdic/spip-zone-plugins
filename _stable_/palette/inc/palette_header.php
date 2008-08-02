<?php

/**
 * Définit l'entête par défaut pour le plugin Palette
 */
function inc_palette_header_dist() {
  if ($GLOBALS['couleur_foncee']) // Couleurs de la palette pour l'espace prive
    $couleurs_admin = '&couleur_foncee='.substr($GLOBALS['couleur_foncee'],1).'&couleur_claire='.substr($GLOBALS['couleur_claire'],1);
	return '<link rel="stylesheet" href="'._DIR_RACINE.'spip.php?page=palette.css'.$couleurs_admin.'" type="text/css" />'."\n".
				'<link rel="stylesheet" href="'.find_in_path('lib/farbtastic/farbtastic.css').'" type="text/css" />'."\n".
				'<script type="text/javascript" src="'._DIR_RACINE.'spip.php?page=palette.js"></script>'."\n";
}
?>
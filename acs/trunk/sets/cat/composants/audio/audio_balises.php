<?php

function joli_titre($titre) {
  $titre = basename($titre);
  $titre = ereg_replace('.mp3','',$titre);
  $titre = ereg_replace('^ ','',$titre);
  $titre = eregi_replace("_"," ", $titre );
  $titre = eregi_replace("'"," ",$titre );
  return $titre ;
}

// Librairies javascript a inclure pour ce composant
function audio_jslib() {
	if (find_in_path('javascript/dragdrop_interface.js'))
		$js_dragdrop = 'javascript/dragdrop_interface.js';
	// A partir de spip 2.1, l'interface dragdrop de JQuery a changé de nom:
	elseif (find_in_path('javascript/jquery-ui-1.8-drag-drop.min.js'))
		$js_dragdrop = 'javascript/jquery-ui-1.8-drag-drop.min.js';
	return array($js_dragdrop);
}
?>
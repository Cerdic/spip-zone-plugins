<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function refbase_compagnon_messages($flux) {

	$exec = $flux['args']['exec'];
	$pipeline = $flux['args']['pipeline'];
	$aides = &$flux['data'];

	if (($exec=='accueil' AND $pipeline=='affiche_milieu') OR ($exec=='admin_plugin' AND $pipeline=='affiche_gauche')) {
		if (!unserialize($GLOBALS['meta']['refbase'])) {
			$aides[] = array(
				'id' => 'configurer_refbase',
				'titre' => _T('refbase:compagnon_titre'),
				'texte' => _T('refbase:compagnon_texte'),
				'statuts'=> array('webmestre'),
				'target' => '#bando1_menu_squelette',
			);
		}
	}
	
	return $flux;
}

?>

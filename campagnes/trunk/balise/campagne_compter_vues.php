<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function balise_CAMPAGNE_COMPTER_VUES($p) {
	return calculer_balise_dynamique($p, 'CAMPAGNE_COMPTER_VUES', array('id_campagne'));
}

function balise_CAMPAGNE_COMPTER_VUES_stat($args, $context_compil) {
	return $args;
}

function balise_CAMPAGNE_COMPTER_VUES_dyn($id_campagne) {
	static $fait = array();
	
	// On s'assure que le comptage d'une même pub n'est fait qu'une seule fois par hit et pas par un robot
	if (!isset($fait[$id_campagne]) and !_IS_BOT){
		include_spip('inc/campagnes');
		$infos = campagnes_recuperer_infos_visiteur();
		
		$id_encart = sql_getfetsel('id_encart', 'spip_campagnes', 'id_campagne = '.$id_campagne);
		$page = self();
		
		// On ajoute la date et la pub
		$infos = array_merge($infos, array('id_campagne' => $id_campagne, 'id_encart' => $id_encart, 'page' => $page, 'date' => 'NOW()'));
		
		// On enregistre l'affichage
		$ok = sql_insertq(
			'spip_campagnes_vues',
			$infos
		);
		
		// On marque que c'est fait
		if ($ok !== false) $fait[$id_campagne] = true;
	}
}

?>
